<?php

/**
 * 购物车提交
 */

namespace app\order\middle;


class CartSubmitMiddle extends \app\base\middle\BaseMiddle {

    protected function data() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $addId = intval($this->params['add_id']);
        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);
        $addList = target('order/OrderAddress')->loadList(['user_id' => $this->params['user_id']]);
        $type = intval($this->params['type']);
        $_list = target('order/Cart', 'service')->getList($this->params['user_id']);
        $list = [];
        
        /* $config = target('order/OrderConfig')->getConfig();

        $startTime = strtotime(date('Y-m-d') . ' ' . $config['time_start']);
        if($config['time_start'] > $config['time_stop']) {
            $stopTime = strtotime(date('Y-m-d', strtotime("+1 day")) . ' ' . $config['time_stop']);
        }else {
            $stopTime = strtotime(date('Y-m-d') . ' ' . $config['time_stop']);
        }
        $time = time();
        if($time <= $startTime || $time >= $stopTime) {
            return $this->stop('当前时段不可进行下单！');
        } */

        $quick = $this->params['quick'];

        if ($quick) {
            $quickArray = explode('-', $quick);
            $app = $quickArray[0];
            $hasId = $quickArray[1];
            $subId = $quickArray[2];
            $qty = $quickArray[3];
            $info = target($app . '/' . $app, 'service')->getCart($this->params['user_id'], $hasId, $subId, $qty);
            if (!$info) {
                $this->stop(target($app . '/' . $app, 'service')->getError());
            }
            if ($info['type']) {
                $type = 1;
            } else {
                $type = 0;
            }
            $info['total'] = $info['price'] * $info['qty'];
            $info['items'] = $info['qty'];
            $list[] = $info;
        } else {
            if ($_list) {
                foreach ($_list as $k => $v) {
                    if ($v['checked'] && $v['type'] == $type) {
                        $list[$k] = $v;
                    }
                }
            }
        }

        if (empty($list)) {
            return $this->stop('请选择您要结算的商品！', 500, url('order/Cart/index'));
        }

        $orderPrice = 0;
        $deliveryPrice = 0;
        $userDeliveryPrice = 0;

        $orderData = target('order/Order', 'service')->splitOrder($addInfo['province'], $list);

        //优惠券
        $couponList = target('marketing/MarketingCouponLog')->loadList([
            'A.user_id' => $this->params['user_id'],
            'A.status' => 0,
            'A.del' => 0,
            '_sql' => 'A.end_time >= ' . time(),
        ], 0, 'B.money desc');

        foreach ($orderData as $key => $data) {
            $couponData = [];
            foreach ($couponList as $k => $v) {
                if (target($v['typeInfo']['target'])->hasCoupon($v, $data)) {
                    $couponData[] = $v;
                }
            }
            $orderData[$key]['coupon'] = $couponData;
        }

        //赠品
        $time = time();
        $giftList = target('order/OrderGift')->loadList([
            '_sql' => 'start_time <= ' . $time . ' AND stop_time >=' . $time,
            'status' => 1
        ]);

        $userInfo = target('member/MemberUser')->getInfo($this->params['user_id']);
        foreach ($orderData as $key => $data) {
            //会员折扣
            $sumPrice = 0;
            foreach ($data['items'] as $goods) {
                if($goods['discount_status']) {
                    $sumPrice = price_calculate($sumPrice, '+', $goods['price'] * $goods['qty'], 2);
                }
            }
            $orderData[$key]['user_delivery_price'] = price_calculate($sumPrice, '-', round($sumPrice * $userInfo['discount'] / 100, 2));

            //满额包邮
            $freightInfo = target('order/OrderFreight')->getWhereInfo([
                '_sql' => 'start_time <= ' . $time . ' AND stop_time >=' . $time . ' AND order_money <= ' . $data['order_price'],
                'status' => 1
            ]);
            if($freightInfo) {
                $orderData[$key]['delivery_price'] = 0;
            }
            $orderData[$key]['freight_free'] = (object)$freightInfo;

            $orderData[$key]['gift'] = [];
            $hasIds = [];
            //订单赠品
            foreach ($giftList as $vo) {
                if ($vo['type']) {
                    continue;
                }
                if ($vo['order_money'] > $data['order_price']) {
                    continue;
                }
                $hasIds[] = $vo['has_ids'];
            }
            //商品赠品
            $ids = explode(',', $data['ids']);
            foreach ($ids as $id) {
                foreach ($giftList as $vo) {
                    if (!$vo['type']) {
                        continue;
                    }
                    if (!$vo['mall_ids']) {
                        continue;
                    }
                    $mallIds = explode(',', $vo['mall_ids']);

                    if (in_array($id, $mallIds)) {
                        $hasIds[] = $vo['has_ids'];
                    }
                }
            }
            //合并
            $hasIds = implode(',', $hasIds);
            $hasIds = explode(',', $hasIds);
            $hasIds = array_filter($hasIds);
            $hasIds = array_unique($hasIds);

            if (empty($hasIds)) {
                continue;
            }
            $hasIds = implode(',', $hasIds);

            $mallData = target('mall/MallProducts')->loadList([
                '_sql' => 'B.mall_id in (' . $hasIds . ') AND A.store > 0',
                'B.gift_status' => 1,
                'B.type' => $data['type'],
                'C.status' => 1,
            ]);
            if (empty($mallData)) {
                continue;
            }
            $giftData = [];
            foreach ($mallData as $vo) {
                $specData = unserialize($vo['spec_data']);
                $specText = [];
                if ($specData) {
                    foreach ($specData as $v) {
                        $specText[] = $v['value'];
                    }
                }
                $giftData[] = [
                    'title' => $vo['title'],
                    'app' => $vo['app'],
                    'image' => $vo['image'],
                    'url' => $vo['url'],
                    'description' => $vo['description'],
                    'mall_id' => $vo['mall_id'],
                    'products_id' => $vo['products_id'],
                    'sell_price' => $vo['sell_price'],
                    'goods_no' => $vo['goods_no'],
                    'store' => $vo['store'],
                    'unit' => $vo['unit'],
                    'spec' => implode(' ', $specText)
                ];
            }
            $orderData[$key]['gift'] = $giftData;


        }

        foreach ($orderData as $vo) {
            $userDeliveryPrice = $vo['user_delivery_price'];
            $deliveryPrice += $vo['delivery_price'];
            $orderPrice += $vo['order_price'];
        }

        //费用处理
        $currency = target('order/Order', 'service')->getCurrency($list);

        $config = target('order/OrderConfig')->getConfig();

        return $this->run([
            'type' => $type,
            'cartData' => $orderData,
            'userDeliveryPrice' => price_format($userDeliveryPrice),
            'deliveryPrice' => price_format($deliveryPrice),
            'orderPrice' => price_format($orderPrice),
            'addList' => $addList,
            'addInfo' => $addInfo,
            'currencyAppend' => $currency['append'],
            'currencyExchange' => $currency['exchange'],
            'invoiceClass' => target('order/OrderInvoiceClass')->loadList(),
            'config' => [
                'pay_type' => array_filter(explode(',', $config['pay_type']), function($var) {
                    if(!strlen($var)) {
                        return false;
                    }
                    return true;
                }),
                'pay_tip' => $config['pay_tip'],
                'pay_agreement' => html_out($config['pay_agreement']),
            ]
        ]);
    }

    protected function take() {
        $addId = intval($this->params['add_id']);
        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);

        $where = [];
        //$groupTake = [];
        $where['province'] = $addInfo['province'];
        $where['city'] = $addInfo['city'];
        $where['region'] = $addInfo['region'];
        $order = 'take_id desc';

        $takeList = target('base/Base')->table('order_take')
            ->where(array_merge([
                'status' => 1,
            ], $where))->order($order)->select();

        return $this->run([
            'takeList' => $takeList
        ]);
    }

    private function distance($longitude1, $latitude1, $longitude2, $latitude2, $unit = 2, $decimal = 2) {
        $EARTH_RADIUS = 6370.996; // 地球半径系数
        $PI = pi();

        $radLat1 = $latitude1 * $PI / 180.0;
        $radLat2 = $latitude2 * $PI / 180.0;

        $radLng1 = $longitude1 * $PI / 180.0;
        $radLng2 = $longitude2 * $PI / 180.0;

        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;

        $distance = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $distance = $distance * $EARTH_RADIUS * 1000;

        if ($unit == 2) {
            $distance = $distance / 1000;
        }

        return round($distance, $decimal);
    }

    protected function post() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $couponId = $this->params['coupon_id'];
        $takeId = $this->params['take_id'];
        $takeType = $this->params['take_type'];
        $addId = $this->params['add_id'];
        $remark = $this->params['remark'];
        $type = intval($this->params['type']);
        $gift = $this->params['gift'];
        $quick = $this->params['quick'];
        $payType = $this->params['pay_type'];
        $config = target('order/OrderConfig')->getConfig();

        if(!$config['pay_type']) {
            $payType = 1;
        }
        if($config['pay_type'] == 1) {
            $payType = 0;
        }
        //订单提交
        $data = [];
        if ($quick) {
            $quickArray = explode('-', $quick);
            $app = $quickArray[0];
            $hasId = $quickArray[1];
            $subId = $quickArray[2];
            $qty = $quickArray[3];
            $info = target($app . '/' . $app, 'service')->getCart($this->params['user_id'], $hasId, $subId, $qty);
            if (!$info) {
                return $this->stop(target($app . '/' . $app, 'service')->getError());
            }
            $info['total'] = $info['price'] * $info['qty'];
            $info['items'] = $info['qty'];
            if ($info['type']) {
                $type = 1;
            } else {
                $type = 0;
            }
            $data[] = $info;
        } else {
            $list = target('order/Cart', 'service')->getList($this->params['user_id']);
            if ($list) {
                foreach ($list as $k => $v) {
                    if ($v['checked'] && $v['type'] == $type) {
                        $data[$k] = $v;
                    }
                }
            }
        }
        if (empty($data)) {
            return $this->stop('结算商品不存在!');
        }

        $addInfo = target('order/OrderAddress')->getAddress($this->params['user_id'], $addId);
        if (empty($addInfo)) {
            return $this->stop('收货地址不存在,请重新选择!');
        }

        $target = target('order/Order', 'service');
        $data = $target->splitOrder($addInfo['province'], $data);


        //重组数据
        foreach ($data as $key => $vo) {
            $attr = [
                'coupon_id' => intval($couponId[$key]),
                'type' => $vo['type'],
                'take_id' => intval($takeId[$key]),
                'take_type' => $takeType[$key] ? 1 : 0,
                'remark' => html_clear($remark[$key]),
                'gift' => intval($gift[$key]),
            ];

            if ($attr['gift']) {
                $proInfo = target('mall/MallProducts')->getMallInfo([
                    '_sql' => 'A.products_id = ' . $attr['gift'] . ' AND A.store > 0',
                    'B.gift_status' => 1,
                    'B.type' => $type,
                    'C.status' => 1,
                ]);
                if (empty($proInfo)) {
                    return $this->stop('所选赠品已下架或售罄！');
                }

                $time = time();
                $giftList = target('order/OrderGift')->loadList([
                    '_sql' => 'start_time <= ' . $time . ' AND stop_time >=' . $time . ' AND FIND_IN_SET(' . $proInfo['mall_id'] . ', has_ids)',
                    'status' => 1
                ]);
                if (empty($giftList)) {
                    return $this->stop('赠品活动已过期或不存在！');
                }
                $proId = 0;
                foreach ($giftList as $gift) {
                    if ($proId) {
                        break;
                    }
                    if ($vo['type']) {
                        //商品条件
                        $mallIds = explode(',', $gift['mall_ids']);
                        if (in_array($proInfo['mall_id'], $mallIds)) {
                            $proId = $attr['gift'];
                        }
                    } else {
                        //订单条件
                        if ($vo['order_price'] >= $gift['order_money']) {
                            $proId = $attr['gift'];
                        }
                    }
                }
                if (empty($proId)) {
                    return $this->stop('赠品活动不存在！');
                }

                $cartData = [];
                $cartData['item_no'] = $proInfo['products_no'];
                $cartData['type'] = $type;
                $cartData['app'] = $vo['app'];
                $cartData['app_id'] = $proInfo['mall_id'];
                $cartData['id'] = $proInfo['products_id'];
                $cartData['qty'] = 1;
                $cartData['price'] = $proInfo['sell_price'];
                $cartData['cost_price'] = $proInfo['cost_price'];
                $cartData['market_price'] = $proInfo['market_price'];
                $cartData['name'] = $proInfo['title'];
                $cartData['options'] = $proInfo['spec_data'];
                $cartData['image'] = $proInfo['image'];
                $cartData['weight'] = $proInfo['weight'];
                $cartData['url'] = url(VIEW_LAYER_NAME . '/mall/info/index', ['id' => $proInfo['mall_id']]);
                $cartData['freight_type'] = 0;
                $cartData['freight_tpl'] = 0;
                $cartData['freight_price'] = 0;
                $cartData['gift_status'] = 1;

                $data[$key]['items'][] = $cartData;
                $data[$key]['ids'] = implode(',', [$proId, $vo['ids']]);

                $data[$key]['order_price'] += $proInfo['sell_price'];
            }

            if ($this->params['invoice'][$key]) {
                if (empty($this->params['invoice_class'][$key]) || empty($this->params['invoice_name'][$key])) {
                    return $this->stop('请输入发票抬头并选择发票内容！');
                }
                if ($this->params['invoice_type'][$key] && empty($this->params['invoice_label'][$key])) {
                    return $this->stop('请输入纳税人识别号！');
                }
                $attr['invoice'] = [
                    'type' => intval($this->params['invoice_type'][$key]),
                    'class_id' => intval($this->params['invoice_class'][$key]),
                    'name' => html_clear($this->params['invoice_name'][$key]),
                    'number' => html_clear($this->params['invoice_label'][$key]),
                ];
            }
            $data[$key]['attr'] = $attr;
        }

        $orderNos = $target->addOrder($this->params['user_id'], $data, $addInfo, $payType);
        if (!$orderNos) {
            return $this->stop($target->getError());
        }
        if (empty($quick)) {
            target('order/Cart', 'service')->clear($this->params['user_id']);
        }

        $orderList = target('order/Order')->loadList([
            '_sql' => 'order_no in(' . implode(',', $orderNos) . ')'
        ]);

        $deliveryPrice = 0;
        $orderPrice = 0;
        $app = [];

        foreach ($orderList as $vo) {
            if (!$takeId) {
                $deliveryPrice += $vo['delivery_price'];
            }
            $orderPrice += $vo['order_price'];
            $app[] = $vo['order_app'];
        }
        $app = array_unique($app);

        return $this->run(['order_no' => implode('|', $orderNos), 'order_price' => $orderPrice, 'delivery_price' => $deliveryPrice, 'app' => $app, 'pay_type' => $payType]);
    }

}
