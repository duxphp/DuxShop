<?php

namespace app\order\service;
/**
 * 订单处理
 */
class OrderService extends \app\base\service\BaseService {
    /**
     * @var string
     */
    private $model = 'order/Order';

    /**
     * 添加订单
     *
     * @param [type] $userId
     * @param [type] $proData
     * @param [type] $addInfo
     * @param [type] $payType
     * @return void
     */
    public function addOrder($userId, $proData, $addInfo, $payType) {
        if (empty($proData)) {
            return $this->error('提交的订单暂无商品!');
        }
        if (empty($proData)) {
            return $this->error('购物车内暂无商品！');
        }
        foreach ($proData as $key => $vo) {
            $target = target($vo['app'] . '/' . 'Order', 'service');
            if (!$target->refreshCart($userId, $vo)) {
                return $this->error($target->getError());
            }
        }

        if (empty($addInfo['name']) || empty($addInfo['province']) || empty($addInfo['city']) || empty($addInfo['address'])) {
            return $this->error('收货信息不完整!');
        }

        if (empty($addInfo['tel'])) {
            return $this->error('请填写收货电话!');
        }

        $model = target($this->model);

        $model->beginTransaction();
        $orderIds = [];
        $orderNos = [];
        $couponUse = [];

        foreach ($proData as $key => $vo) {
            $orderData = [];
            $payPrice = $vo['order_price'];
            $orderPrice = $vo['order_price'];
            $discountPrice = 0;
            $deliveryPrice = 0;

            $curPro = reset($vo['items']);
            $orderNo = log_no($userId);
            $orderNos[] = $orderNo;

            //商品数量
            $num = 0;
            foreach ($vo['items'] as $data) {
                $num += $data['qty'];
            }
            //货币信息
            $currency = $this->getCurrency($vo['items']);
            if (bccomp(0, $payPrice, 2) !== -1) {
                $payPrice = 0;
            }

            if (!$vo['attr']['type'] && $vo['attr']['take_id']) {
                $takeInfo = target('order/OrderTake')->getWhereInfo([
                    'take_id' => $vo['attr']['take_id'],
                ]);
                if (empty($takeInfo)) {
                    return $this->error('自提点不存在!');
                }
                $orderData['take_id'] = $vo['attr']['take_id'];
                $orderData['take_type'] = $vo['attr']['take_type'];
                if ($vo['attr']['take_type']) {
                    $deliveryPrice = $takeInfo['freight'];
                }
            } else {
                $deliveryPrice = $vo['delivery_price'];
            }

            $time = time();
            $freightFreeInfo = target('order/OrderFreight')->getWhereInfo([
                '_sql' => 'start_time <= ' . $time . ' AND stop_time >=' . $time . ' AND order_money <= ' . $orderPrice,
                'type' => 0,
                'status' => 1,
            ]);
            if ($freightFreeInfo) {
                $deliveryPrice = 0;
            }

            //优惠券使用
            $couponInfo = [];
            $discountCoupon = 0;
            if ($vo['attr']['coupon_id']) {
                $couponInfo = target('marketing/MarketingCouponLog')->getWhereInfo([
                    'A.log_id' => $vo['attr']['coupon_id'],
                    'A.user_id' => $userId,
                    'A.status' => 0,
                    'A.del' => 0,
                    '_sql' => 'A.end_time >= ' . time(),
                ]);
                if (empty($couponInfo)) {
                    return $this->error('所选优惠券不可用!');
                }
                if (in_array($vo['attr']['coupon_id'], $couponUse)) {
                    return $this->error('多个订单不能同时使用优惠券!');
                }
                $couponUse[] = $vo['attr']['coupon_id'];
                $couponIds = target($couponInfo['typeInfo']['target'])->hasCoupon($couponInfo, $vo);
                if ($couponIds) {
                    $payPrice = price_calculate($payPrice, '-', $couponInfo['money'], 2);
                    $discountPrice = price_calculate($discountPrice, '+', $couponInfo['money'], 2);
                    $discountCoupon = $discountPrice;
                    $status = target('marketing/MarketingCouponLog')->edit([
                        'log_id' => $couponInfo['log_id'],
                        'status' => 1,
                    ]);
                    if (!$status) {
                        return $this->error(target('marketing/MarketingCouponLog')->getError());
                    }
                    $orderData['order_coupon'] = $couponInfo['log_id'];
                    $couponInfo['has_id'] = $couponIds;
                } else {
                    return $this->error('所选优惠券不可用！');
                }
            }

            $orderData['order_user_id'] = $userId;
            $orderData['order_app'] = $vo['app'];

            if ($num > 1) {
                $orderData['order_title'] = $curPro['name'] . '等' . $num . '件商品';
            } else {
                $orderData['order_title'] = $curPro['name'];
            }

            $orderData['order_image'] = $curPro['image'];
            $orderData['order_price'] = $orderPrice;
            $orderData['order_sum'] = $vo['order_sum'];
            $orderData['order_remark'] = str_len(html_clear($vo['attr']['remark']), 250);

            $orderData['receive_name'] = $addInfo['name'];
            $orderData['receive_tel'] = $addInfo['tel'];
            $orderData['receive_province'] = $addInfo['province'];
            $orderData['receive_city'] = $addInfo['city'];
            $orderData['receive_region'] = $addInfo['region'];
            $orderData['receive_street'] = $addInfo['street'];
            $orderData['receive_address'] = $addInfo['address'];
            $orderData['receive_zip'] = $addInfo['zip'];

            $orderData['order_no'] = $orderNo;
            $orderData['order_status'] = 1;
            $orderData['order_create_time'] = time();
            $orderData['order_complete_status'] = 0;
            $orderData['order_ip'] = \dux\lib\Client::getUserIp();

            $orderData['pay_type'] = $payType;
            $orderData['pay_status'] = 0;
            $orderData['pay_id'] = 0;
            $orderData['pay_price'] = $payPrice;
            $orderData['pay_discount'] = $discountPrice;
            $orderData['pay_currency'] = serialize($currency);
            $orderData['discount_coupon'] = $discountCoupon;
            $orderData['discount_user'] = 0;
            $orderData['delivery_status'] = 0;
            $orderData['delivery_price'] = $deliveryPrice;

            $orderId = $model->add($orderData);
            if (!$orderId) {
                $model->rollBack();
                return $this->error($model->getError());
            }

            $orderData['order_id'] = $orderId;

            $goodsList = [];

            foreach ($vo['items'] as $data) {
                $goodsData = [
                    'order_id' => $orderId,
                    'has_id' => $data['app_id'],
                    'sub_id' => $data['id'],
                    'user_id' => $userId,
                    'goods_no' => $data['item_no'],
                    'goods_qty' => $data['qty'],
                    'goods_price' => $data['price'],
                    'goods_cost_price' => $data['cost_price'],
                    'goods_market_price' => $data['market_price'],
                    'goods_weight' => $data['weight'],
                    'goods_unit' => $data['unit'],
                    'goods_options' => serialize($data['options']),
                    'goods_name' => $data['name'],
                    'goods_image' => $data['image'],
                    'goods_url' => $data['url'],
                    'price_total' => price_calculate($data['price'], '*', $data['qty']),
                    'extend' => serialize($data['extend']),
                    'attr_comment' => $data['comment_status'] ? 1 : 0,
                    'attr_service' => $data['service_status'] ? 1 : 0,
                    'attr_invoice' => $data['invoice_status'] ? 1 : 0,
                    'gift_status' => $data['gift_status'] ? 1 : 0,
                    'discount_status' => $data['discount_status'],
                    'goods_currency' => serialize($data['currency']),
                ];
                $goodsList[] = $goodsData;

            }

            $hasGoodsData = [];
            //优惠券折扣
            if ($couponInfo) {
                foreach ($goodsList as $k => $data) {
                    if ((in_array($data['sub_id'], $couponInfo['has_id']) || empty($couponInfo['has_id'])) AND bccomp($data['price_total'], 0, 2) === 1) {
                        $hasGoodsData[$k] = $data;
                    }
                }
                if ($hasGoodsData) {
                    $sumPrice = 0;
                    foreach ($hasGoodsData as $goods) {
                        $sumPrice = price_calculate($sumPrice, '+', $goods['price_total'], 2);
                    }
                    foreach ($hasGoodsData as $k => $goods) {
                        $goodsDiscount = round($goods['price_total'] / $sumPrice * $couponInfo['money'], 2);
                        $goodsList[$k]['price_total'] = price_calculate($goods['price_total'], '-', $goodsDiscount, 2);
                        $goodsList[$k]['price_discount'] = price_calculate($goods['price_discount'], '+', $goodsDiscount, 2);
                        $goodsList[$k]['discount_coupon'] = $goodsDiscount;
                    }
                }
            }

            //会员折扣
            $discountGoods = [];
            $sumPrice = 0;
            foreach ($goodsList as $goods) {
                if ($goods['discount_status']) {
                    $sumPrice = price_calculate($sumPrice, '+', $goods['price_total'], 2);
                    $discountGoods[] = $goods;
                }
            }
            $userInfo = target('member/MemberUser')->getInfo($userId);
            if ($userInfo['discount'] > 1 && $userInfo['discount'] < 100 && bccomp($sumPrice, 0, 2) === 1) {
                $discount = round($sumPrice * $userInfo['discount'] / 100, 2);
                $userDiscount = price_calculate($sumPrice, '-', $discount);
                $discountPrice = price_calculate($discountPrice, '+', $userDiscount);
                $editData = [
                    'order_id' => $orderId,
                    'discount_user' => $userDiscount,
                    'pay_discount' => $discountPrice,
                    'pay_price' => price_calculate($payPrice, '-', $userDiscount),
                ];
                if (!$model->edit($editData)) {
                    $model->rollBack();
                    return $this->error($model->getError());
                }
                foreach ($discountGoods as $k => $goods) {
                    $goodsDiscount = round($goods['price_total'] / $sumPrice * $userDiscount, 2);
                    $goodsList[$k]['price_total'] = price_calculate($goods['price_total'], '-', $goodsDiscount, 2);
                    $goodsList[$k]['price_discount'] = price_calculate($goods['price_discount'], '+', $goodsDiscount, 2);
                    $goodsList[$k]['discount_user'] = $goodsDiscount;
                }
            }

            $invoiceMoney = 0;
            foreach ($goodsList as $goods) {
                if (!target('order/OrderGoods')->add($goods)) {
                    $model->rollBack();
                    return $this->error('订单提交失败!');
                }
                //发票信息
                if ($goods['attr_attr_invoice']) {
                    $invoiceMoney = price_calculate($invoiceMoney, '+', $goods['price_total'], 2);
                }
            }

            //发票处理
            if ($vo['attr']['invoice'] && bccomp($invoiceMoney, 0, 2) === 1) {
                $status = target('order/OrderInvoice')->add(array_merge($vo['attr']['invoice'], [
                    'create_time' => time(),
                    'user_id' => $userId,
                    'order_id' => $orderId,
                    'money' => $invoiceMoney,
                ]));
                if (!$status) {
                    $model->rollBack();
                    return $this->error('发票提交失败!');
                }
            }

            $target = target($vo['app'] . '/Order', 'service');
            if (!$target->addOrder($orderData, $goodsList, $addInfo)) {
                $model->rollBack();
                return $this->error($target->getError());
            }

            $hookList = run('service', 'Order', 'hookAddOrder', [$orderData, $goodsList]);
            if (!empty($hookList)) {
                foreach ($hookList as $a => $v) {
                    if (!$v) {
                        return $this->error(target($a . '/Order', 'service')->getError());
                    }
                }
            }

            if (!$this->addLog($orderId, '订单已提交，等待配货操作')) {
                $model->rollBack();
                return $this->error('订单日志记录失败!');
            }
            $orderIds[] = $orderId;
        }

        //自动取消
        $config = target('order/OrderConfig')->getConfig();
        $time = time() + $config['cancel_hour'] * 60;
        foreach ($orderIds as $orderId) {
            if (!target('tools/Queue', 'service')->add('send', $orderId, '订单自动取消', 'order/Order', 'autoCancel', 'service', [], $time)) {
                return $this->error('发送失败!');
            }
        }

        $model->commit();

        return $this->success($orderNos);
    }

    /**
     * 支付订单
     * @param $orderPayNo
     * @param $money
     * @param $payName
     * @param $payNo
     * @param $payWay
     * @return bool
     */
    public function payOrder($orderPayNo, $money, $payName, $payNo, $payWay = 'system') {
        if (empty($orderPayNo) || empty($payName)) {
            return $this->error('订单信息错误!');
        }

        $orderPay = target('order/OrderPay')->getWhereInfo([
            'pay_no' => $orderPayNo,
        ]);

        if (empty($orderPay)) {
            return $this->error('支付订单不存在!');
        }

        $where = [];
        $where['_sql'] = 'order_id in(' . $orderPay['order_ids'] . ')';
        $where['A.pay_status'] = 0;
        $orderList = target('order/Order')->LoadList($where, 0, '', true);

        if (empty($orderList)) {
            return $this->success();
        }

        $orderData = [];
        foreach ($orderList as $key => $vo) {
            //设置订单状态，防止异步关闭
            target('order/Order')->where(['order_id' => $vo['order_id']])->data(['order_status' => 1])->update();
            $orderData[$vo['order_app']][$key] = $vo;
        }

        //过滤被取消订单
        $orderIds = [];
        $orderNos = [];

        foreach ($orderList as $key => $vo) {
            $orderIds[] = $vo['order_id'];
            $orderNos[] = $vo['order_no'];
        }

        $sumPrice = 0;
        foreach ($orderList as $key => $vo) {
            $sumPrice = price_calculate($vo['total_price'], '+', $sumPrice);
        }

        if (bccomp($money, $sumPrice, 2) === -1) {
            return $this->error('支付金额不正确');
        }

        $orderNos = [];
        foreach ($orderList as $vo) {
            $orderNos[] = $vo['order_no'];
        }

        $orderInfo = current($orderList);
        $orderNum = count($orderList);

        $payId = target('statis/Finance', 'service')->account([
            'user_id' => $orderInfo['order_user_id'],
            'species' => 'member_order',
            'sub_species' => 'pay',
            'no' => implode(',', $orderNos),
            'money' => $sumPrice,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'pay_way' => $payWay,
            'type' => 0,
            'title' => $orderNum > 1 ? '订单合并支付' : '订单支付',
            'remark' => '订单【' . implode(',', $orderNos) . '】支付',
        ]);
        if (!$payId) {
            return $this->error(target('statis/Finance', 'service')->getError());
        }

        if (!empty($orderIds)) {
            //更改订单支付状态
            foreach ($orderList as $vo) {
                $vo['pay_data'][] = [
                    'way' => $payWay,
                    'id' => $payId,
                    'money' => $vo['total_price'],
                ];
                $editData = [
                    'pay_status' => 1,
                    'pay_data' => serialize($vo['pay_data']),
                ];
                if (empty($vo['pay_time'])) {
                    $editData['pay_time'] = time();
                }
                $status = target('order/Order')->where([
                    'order_id' => $vo['order_id'],
                ])->data($editData)->update();
                if (!$status) {
                    return $this->error('订单支付失败!');
                }
            }
        }
        foreach ($orderList as $order) {
            if (!$this->addLog($order['order_id'], '订单支付成功', '支付方式【' . $payName . '】')) {
                return $this->error('订单日志记录失败!');
            }
        }

        //付款成功回调
        foreach ($orderData as $app => $rows) {
            if (!target($app . '/Order', 'service')->payOrder($rows, $payName, $payNo)) {
                return $this->error(target($app . '/Order', 'service')->getError());
            }
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookPayOrder', [$orderList, $payName, $payNo]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        //通知接口
        foreach ($orderList as $vo) {
            target('tools/Tools', 'service')->notice('order', 'pay', $vo['order_user_id'], [
                '用户名' => $vo['show_name'],
                '订单编号' => $vo['order_no'],
                '订单标题' => $vo['order_title'],
                '下单时间' => date('Y-m-d H:i:s', $vo['order_create_time']),
                '订单金额' => $vo['order_price'],
                '支付类型' => $payName,
                '支付号' => $payNo,
                '支付金额' => $vo['total_price'],
                '支付时间' => date('Y-m-d H:i:s', time()),
            ], 'pages/order/index');
        }

        // 到付完成
        foreach ($orderList as $vo) {
            if (!$vo['pay_type']) {
                if (!target('order/Order', 'service')->confirmOrder($vo['order_id'])) {
                    return $this->error(target('order/Order', 'service')->getError());
                }
            }
        }

        return $this->success();
    }

    /**
     * 创建合并支付
     * @param $userId
     * @param $orderIds
     * @param $payType
     * @return bool
     */
    public function addPay($userId, $orderIds, $payType) {
        $info = target('order/OrderPay')->getWhereInfo([
            'user_id' => $userId,
            'order_ids' => $orderIds,
            'pay_type' => $payType,
        ]);
        if (empty($info)) {
            $payNo = log_no($userId);
            $data = [
                'user_id' => $userId,
                'order_ids' => $orderIds,
                'pay_type' => $payType,
                'pay_no' => $payNo,
                'time' => time(),
            ];
            if (!target('order/OrderPay')->add($data)) {
                return $this->error('支付信息创建失败!');
            }
        } else {
            $status = target('order/OrderPay')->edit([
                'pay_id' => $info['pay_id'],
                'time' => time(),
            ]);
            if (!$status) {
                return $this->error('支付信息创建失败!');
            }
            $payNo = $info['pay_no'];
        }
        return $this->success($payNo);
    }

    /**
     * 自动取消订单
     * @param array $data
     * @param $orderId
     * @return bool
     */
    public function autoCancel($data = [], $orderId) {
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId, true);
        if ($orderInfo['order_complete_status'] || $orderInfo['delivery_status'] || $orderInfo['pay_status'] || !$orderInfo['order_status']) {
            return true;
        }

        $orderPay = target('order/OrderPay')->loadList([
            'user_id' => $orderInfo['order_user_id'],
            '_sql' => 'FIND_IN_SET(' . $orderId . ', order_ids)',
        ], 1, 'time desc');

        if (!empty($orderPay)) {
            $orderPay = $orderPay[0];
            if ($orderPay['time'] + 300 > time()) {
                //5分钟后再次检测
                $time = time() + 300;
                if (!target('tools/Queue', 'service')->add('send', $orderId, '订单取消推迟确认', 'order/Order', 'autoCancel', 'service', [], $time)) {
                    return $this->error('队列加入失败!');
                }
                return true;
            }
        }

        if (!$this->cancelOrder($orderId, false)) {
            return $this->error($this->getError());
        }
        return true;
    }

    /**
     * 订单/商品退款
     * @param $hasId
     * @param int $type
     * @param $price
     * @return bool
     */
    public function refundOrder($hasId, $type = 0, $price = 0) {
        $goodsIds = [];
        if ($type) {
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $hasId,
                '_sql' => 'service_status <> 2',
            ]);
            foreach ($goodsList as $vo) {
                $goodsIds[] = $vo['id'];
            }
        } else {
            $goodsIds[] = $hasId;
        }

        if (empty($goodsIds)) {
            return $this->error('商品数据不存在!');
        }

        $goodsList = target('order/OrderGoods')->loadList([
            '_sql' => 'id in (' . implode(',', $goodsIds) . ')',
        ]);

        $orderInfo = target('order/Order')->getWhereInfo([
            'order_id' => $goodsList[0]['order_id'],
        ]);

        $totalPrice = 0;
        $currencyList = target('member/MemberCurrency')->typeList();
        $currency = [];

        foreach ($goodsList as $info) {
            $id = $info['id'];
            $save = target('order/OrderGoods')->edit([
                'id' => $id,
                'service_status' => 2,
            ]);
            if (!$save) {
                return $this->error('退款处理失败!');
            }
            if ($type) {
                $price = $info['price_total'];
            }
            $totalPrice = price_calculate($totalPrice, '+', $price);
            if (!$info['goods_currency'] || bccomp(0, $info['goods_currency']['money'], 2) !== -1) {
                continue;
            }
            $currency[$info['goods_currency']['type']] = price_calculate($currency[$info['goods_currency']['type']], '+', $info['goods_currency']['money']);

        }
        $goodsCount = target('order/OrderGoods')->countList([
            'order_id' => $orderInfo['order_id'],
            'service_status' => 0,
        ]);
        $orderStatus = 1;
        if (!$goodsCount) {
            $orderStatus = 0;
        }
        foreach ($currency as $key => $vo) {
            $status = target($currencyList[$key]['target'], 'service')->account([
                'user_id' => $orderInfo['order_user_id'],
                'money' => $vo,
            ]);
            if (!$status) {
                return $this->error(target($currencyList[$key]['target'], 'service')->getError());
            }
            $status = target('statis/Finance', 'service')->account([
                'user_id' => $orderInfo['order_user_id'],
                'species' => $key . '_order',
                'sub_species' => 'refund',
                'no' => $orderInfo['order_no'],
                'money' => $vo,
                'title' => '订单退款',
                'remark' => '订单【' . $orderInfo['order_no'] . '】退款',
            ]);
            if (!$status) {
                return $this->error(target('statis/Finance', 'service')->getError());
            }
        }
        //退款
        if (bccomp($totalPrice, 0, 2) === 1) {
            $payList = target('member/PayConfig')->typeList();
            $payData = $orderInfo['pay_data'];
            foreach ($payData as $vo) {
                $payTypeInfo = $payList[$vo['way']];
                $payInfo = target('statis/StatisFinancialLog')->getInfo($vo['id']);
                $payNo = target($payTypeInfo['target'], 'pay')->refund([
                    'user_id' => $orderInfo['order_user_id'],
                    'total_money' => $payInfo['money'],
                    'money' => $totalPrice,
                    'pay_no' => $payInfo['pay_no'],
                ]);
                if (empty($payNo)) {
                    return $this->error(target($payTypeInfo['target'], 'pay')->getError());
                }
                $status = target('statis/Finance', 'service')->account([
                    'user_id' => $orderInfo['order_user_id'],
                    'species' => 'member_order',
                    'sub_species' => 'refund',
                    'no' => $orderInfo['order_no'],
                    'money' => $totalPrice,
                    'pay_no' => $payNo,
                    'pay_name' => $payTypeInfo['name'],
                    'pay_way' => $vo['way'],
                    'title' => '订单退款',
                    'remark' => '订单【' . $orderInfo['order_no'] . '】退款',
                ]);
                if (!$status) {
                    return $this->error(target('statis/Finance', 'service')->getError());
                }
            }
        }
        //更新订单信息
        $closeTime = 0;
        if (!$orderStatus) {
            $closeTime = time();
        }
        $status = target('order/Order')->edit([
            'order_id' => $orderInfo['order_id'],
            'order_status' => $orderStatus,
            'order_close_time' => $closeTime,
            'refund_price' => price_calculate($orderInfo['refund_price'], '+', $totalPrice),
        ]);
        if (!$status) {
            return $this->error(target('order/Order', 'service')->getError());
        }

        //优惠券回退
        if (!$orderStatus) {
            if ($orderInfo['order_coupon']) {
                $status = target('marketing/MarketingCouponLog')->where([
                    'log_id' => $orderInfo['order_coupon'],
                ])->data([
                    'status' => 0,
                ])->update();
                if (!$status) {
                    return $this->error(target('marketing/MarketingCouponLog')->getError());
                }
            }
        }

        $hookList = run('service', 'Order', 'hookRefundOrder', [$hasId, $type, $totalPrice]);
        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        return $this->success();
    }

    /**
     * 取消订单
     * @param string $ids
     * @param bool $refund
     * @return bool
     */
    public function cancelOrder($ids = '', $refund = true) {
        if (empty($ids)) {
            return $this->error('ID参数未知');
        }

        $orderList = target('order/Order')->loadList([
            '_sql' => 'order_id in (' . $ids . ')',
            'A.order_status' => 1,
        ]);

        if (empty($orderList)) {
            return $this->success();
        }

        $ids = implode(',', array_column($orderList, 'order_id'));
        $closeTime = time();
        $status = target('order/Order')->where([
            '_sql' => 'order_id in (' . $ids . ')',
        ])->data([
            'order_status' => 0,
            'order_close_time' => $closeTime,
        ])->update();
        if (!$status) {
            return $this->error('取消订单失败!');
        }

        $orderData = [];
        $appData = [];
        $refundStatus = false;
        foreach ($orderList as $key => $vo) {
            $orderData[$vo['order_user_id']][$key] = $vo;
            $appData[$vo['order_app']][$key] = $vo;
            if (!$this->addLog($vo['order_id'], '订单取消成功')) {
                return $this->error('订单日志记录失败!');
            }
            $goodsCount = target('order/OrderGoods')->countList([
                'order_id' => $vo['order_id'],
                'service_status' => 1,
            ]);
            if ($goodsCount) {
                return $this->error('订单中有售后中商品，暂时无法取消!');
            }
            if ($vo['pay_status']) {
                $refundStatus = true;
            }
        }

        //取消方法
        foreach ($appData as $app => $order) {
            foreach ($order as $item) {
                if (!target($app . '/Order', 'service')->cancelOrder($item)) {
                    return $this->error(target($app . '/Order', 'service')->getError());
                }
                if (!$refund) {
                    $hookList = run('service', 'Order', 'hookCancelOrder', [$item, $refund]);
                    if (!empty($hookList)) {
                        foreach ($hookList as $a => $vo) {
                            if (!$vo) {
                                return $this->error(target($a . '/Order', 'service')->getError());
                            }
                        }
                    }
                }
            }
        }

        //优惠券回退
        foreach ($orderList as $key => $vo) {
            if ($vo['order_coupon']) {
                $status = target('marketing/MarketingCouponLog')->where([
                    'log_id' => $vo['order_coupon'],
                ])->data([
                    'status' => 0,
                ])->update();
                if (!$status) {
                    return $this->error(target('marketing/MarketingCouponLog')->getError());
                }
            }
        }

        if ($refund) {
            $refund = $refundStatus ? true : false;
        }

        if (!$refund) {
            return $this->success();
        }

        //退货币
        $currencyList = target('member/MemberCurrency')->typeList();
        foreach ($orderList as $order) {
            $currency = [];
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $order['order_id'],
                'service_status' => 0,
            ]);
            foreach ($goodsList as $vo) {
                $currency[] = $vo['goods_currency'];
            }
            foreach ($currency as $key => $vo) {
                if (bccomp(0, $vo['money'], 2) !== -1) {
                    continue;
                }
                $status = target($currencyList[$vo['type']]['target'], 'service')->account([
                    'user_id' => $order['order_user_id'],
                    'money' => $vo['money'],
                ]);
                if (!$status) {
                    return $this->error(target($currencyList[$vo['type']]['target'], 'service')->getError());
                }
                $status = target('statis/Finance', 'service')->account([
                    'user_id' => $order['order_user_id'],
                    'species' => $vo['type'] . '_order',
                    'sub_species' => 'refund',
                    'no' => $order['order_no'],
                    'money' => $vo['money'],
                    'title' => '订单退款',
                    'remark' => '订单【' . $order['order_no'] . '】取消退款',
                ]);
                if (!$status) {
                    return $this->error(target('statis/Finance', 'service')->getError());
                }
            }
        }

        //退款操作
        $payList = target('member/PayConfig')->typeList();
        foreach ($orderData as $userId => $list) {
            foreach ($orderList as $order) {
                $payData = $order['pay_data'];
                foreach ($payData as $vo) {
                    $payInfo = target('statis/StatisFinancialLog')->getInfo($vo['id']);
                    $payWay = $payInfo['pay_way'];
                    $payTypeInfo = $payList[$payWay];
                    $money = price_calculate($order['total_price'], '-', $order['refund_price']);
                    if (bccomp(0, $money, 2) !== -1) {
                        continue;
                    }
                    $payNo = target($payTypeInfo['target'], 'pay')->refund([
                        'user_id' => $userId,
                        'total_money' => $payInfo['money'],
                        'money' => $money,
                        'pay_no' => $payInfo['pay_no'],
                    ]);
                    if (empty($payNo)) {
                        return $this->error(target($payTypeInfo['target'], 'pay')->getError());
                    }
                    $status = target('statis/Finance', 'service')->account([
                        'user_id' => $order['order_user_id'],
                        'species' => 'member_order',
                        'sub_species' => 'refund',
                        'no' => $order['order_no'],
                        'money' => $money,
                        'pay_no' => $payNo,
                        'pay_name' => $payTypeInfo['name'],
                        'pay_way' => $payWay,
                        'title' => '订单退款',
                        'remark' => '订单【' . $order['order_no'] . '】退款',
                    ]);
                    if (!$status) {
                        return $this->error(target('statis/Finance', 'service')->getError());
                    }
                }

                $status = target('order/Order')->where([
                    'order_id' => $order['order_id'],
                ])->data([
                    'refund_price' => $order['total_price'],
                ])->update();
                if (!$status) {
                    return $this->error('取消订单失败!');
                }

                $hookList = run('service', 'Order', 'hookCancelOrder', [$order, $refund]);
                if (!empty($hookList)) {
                    foreach ($hookList as $a => $vo) {
                        if (!$vo) {
                            return $this->error(target($a . '/Order', 'service')->getError());
                        }
                    }
                }
            }
        }

        return $this->success();
    }

    /**
     * 订单配货
     * @param $orderId
     * @param string $remark
     * @param bool $log
     * @return bool
     */
    public function parcelOrder($orderId, $remark = '', $log = true) {
        $status = target('order/Order')->where([
            'order_id' => $orderId,
        ])->data([
            'parcel_status' => 1,
        ])->update();
        if (!$status) {
            return $this->error('配货状态更改失败!');
        }
        if (!$log) {
            return $this->success();
        }
        $time = time();
        $data = [
            'order_id' => $orderId,
            'create_time' => $time,
            'status' => 1,
            'remark' => $remark,
        ];
        $data['log'] = target('order/OrderParcel')->addLog([], '生成发货单,待工作人员配货', '', $time);
        if (!target('order/OrderParcel')->add($data)) {
            return $this->error('配货单生成失败!');
        }
        return $this->success();
    }

    /**
     * 订单发货
     * @param $orderId
     * @param $ids
     * @param $deliveryType
     * @param array $params
     * @param bool $log
     * @return bool
     */
    public function deliveryOrder($orderId, $ids, $deliveryType, $params, $log = true) {
        if (empty($orderId)) {
            return $this->error('传递参数有误!');
        }

        $deliveryType = intval($deliveryType);
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId);
        $markiId = 0;

        if (empty($orderInfo)) {
            return $this->error('订单不存在!');
        }

        if ($ids) {
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $orderId,
                '_sql' => 'id in (' . implode(',', $ids) . ')',
                'delivery_status' => 0,
            ]);
        } else {
            $goodsList = target('order/OrderGoods')->loadList([
                'order_id' => $orderId,
                'delivery_status' => 0,
            ]);
        }
        $goodsIds = array_column($goodsList, 'id');

        if (empty($goodsList)) {
            return $this->error('订单暂无需发货商品！');
        }

        $deliveryId = 0;
        $time = time();
        if ($deliveryType == 1) {
            $singleType = $params['single_type'];
            $name = $params['name'];
            $no = $params['no'];
            $remark = $params['remark'];
            if (!$singleType && (empty($name) || empty($no))) {
                return $this->error('请输入快递信息!');
            }
            $data = [
                'order_id' => $orderId,
                'delivery_name' => html_clear($name),
                'delivery_no' => html_clear($no),
                'create_time' => $time,
                'goods_ids' => implode(',', $goodsIds),
                'remark' => html_clear($remark),
            ];
            $deliveryId = target('order/OrderDelivery')->add($data);
            if (!$deliveryId) {
                return $this->error(target('order/OrderDelivery')->getError());
            }
        }

        if ($deliveryType == 2) {
            $markiId = intval($params['marki_id']);
            $data = [
                'order_id' => $orderId,
                'marki_id' => $markiId,
                'create_time' => $time,
                'goods_ids' => implode(',', $goodsIds),
                'remark' => html_clear($params['remark']),
            ];
            $deliveryId = target('warehouse/WarehouseMarkiDelivery')->add($data);
            if (!$deliveryId) {
                return $this->error(target('warehouse/WarehouseMarkiDelivery')->getError());
            }
        }

        //完成订单状态
        $countGoods = target('order/OrderGoods')->countList(['order_id' => $orderId, 'delivery_status' => 0]);
        if ($countGoods == count($goodsList)) {
            $status = $model->edit([
                'order_id' => $orderId,
                'delivery_status' => 1,
            ]);
            if (!$status) {
                return $this->error('订单发货失败!');
            }
        }

        //设置货品状态
        foreach ($goodsList as $vo) {
            target('order/OrderGoods')->edit([
                'id' => $vo['id'],
                'delivery_type' => $deliveryType,
                'delivery_id' => $deliveryId,
                'delivery_status' => 1,
            ]);
        }

        //设置配货状态
        $status = target('order/OrderParcel')->where([
            'order_id' => $orderId,
        ])->data([
            'status' => 2,
        ])->update();
        if (!$status) {
            return $this->error('订单配货失败!');
        }

        $app = $orderInfo['order_app'];
        if (!target($app . '/Order', 'service')->deliveryOrder($orderInfo, $goodsList, $deliveryType, $params, $log)) {
            return $this->error(target($app . '/Order', 'service')->getError());
        }

        //自动完成
        $config = target('order/OrderConfig')->getConfig();
        $time = time() + $config['confirm_day'] * 86400;
        if (!target('tools/Queue', 'service')->add('send', $orderId, '订单自动完成', 'order/Order', 'autoConfirm', 'service', [], $time)) {
            return $this->error('发送失败!');
        }

        //被动接口
        $params['delivery_id'] = $deliveryId;
        $hookList = run('service', 'Order', 'hookDeliveryOrder', [$orderInfo, $goodsList, $deliveryType, $params, $log]);
        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }

        if ($log) {
            if ($deliveryType == 1) {
                $this->addLog($orderId, '订单已发货', '通过快递进行发货');
            }
            if ($deliveryType == 2) {
                $this->addLog($orderId, '订单已发货', '通过商城进行配送');
            }
            if ($deliveryType == 0) {
                $this->addLog($orderId, '订单已发货', '无需快递物流');
            }
        }

        //面单接口
        if ($deliveryType == 1 && $singleType) {
            $config = target('order/OrderConfig')->getConfig();
            $typeInfo = target('order/OrderConfigWaybill')->typeInfo($config['waybill_type']);
            if (!$typeInfo['waybill']) {
                return $this->error('接口不支持电子面单！');
            }
            $orderNo = $orderInfo['order_no'] . '_' . $deliveryId;

            $goodsData = [];
            foreach ($goodsList as $vo) {
                $goodsData[] = [
                    'name' => $vo['goods_name'],
                    'code' => $vo['goods_no'],
                    'num' => $vo['goods_qty'],
                    'price' => $vo['goods_price'],
                    'weight' => $vo['goods_weight'],
                ];
            }

            $placeInfo = target($typeInfo['target'], 'service')->place($name, $orderInfo['order_user_id'], $orderNo, $goodsData, $orderInfo['receive_name'], $orderInfo['receive_tel'], $orderInfo['receive_province'], $orderInfo['receive_city'], $orderInfo['receive_region'], $orderInfo['receive_address'], $orderInfo['receive_zip'], $remark);
            if (!$placeInfo) {
                return $this->error(target($typeInfo['target'], 'service')->getError());
            }
            $data = [
                'delivery_id' => $deliveryId,
                'delivery_name' => $placeInfo['express_name'],
                'delivery_no' => $placeInfo['express_no'],
                'api_data' => json_encode($placeInfo),
                'api_status' => 1,
            ];
            $no = $placeInfo['express_no'];
            target('order/OrderDelivery')->edit($data);
        }

        //小票接口
        if($deliveryType == 2 && $params['pos_print']) {
            $config = target('order/OrderConfig')->getConfig();
            if(!$config['pos_type'] && !$config['pos_tpl']) {
                return $this->error('打印机未配置！');
            }
            $driverInfo = target('warehouse/WarehousePosDriver')->getInfo($config['pos_type']);
            if(empty($driverInfo)) {
                return $this->error('打印机不存在！');
            }
            $tplInfo = target('warehouse/WarehousePosTpl')->getInfo($config['pos_tpl']);
            if(empty($tplInfo)) {
                return $this->error('打印机不存在！');
            }
            if($driverInfo['pos_id'] <> $tplInfo['pos_id']) {
                return $this->error('打印机模板不匹配！');
            }

            $goodsData = [];
            $totalPrice = 0;
            foreach ($goodsList as $vo) {
                $options = [];
                if($vo['goods_options']) {
                    foreach ($vo['goods_options'] as $v) {
                        $options[] = $v['value'];
                    }
                }
                $goodsData[] = [
                    'name' => $vo['goods_name'],
                    'option' => implode(' ', $options),
                    'num' => $vo['goods_qty'],
                    'price' => $vo['goods_price'],
                    'weight' => $vo['goods_weight'],
                    'unit' => $vo['goods_unit'],
                    'total' => $vo['price_total'],
                ];
                $totalPrice = price_calculate($totalPrice, '+', $vo['price_total']);
            }
            $orderNo = $orderInfo['order_no'] . 'D' . $deliveryId;
            $printInfo = target($driverInfo['type_target'], 'service')->print($orderNo, [
                'orderInfo' => $orderInfo,
                'goodsData' => $goodsData,
                'totalPrice' => $totalPrice,
                'deliveryNo' => $orderNo
            ], $config['pos_type'], $tplInfo['tpl'], 'text');
            if (!$printInfo) {
                return $this->error(target($driverInfo['type_target'], 'service')->getError());
            }

        }

        //用户通知
        if ($deliveryType == 1) {
            $typeName = '快递配送';
        }
        if ($deliveryType == 2) {
            $typeName = '商城配送';
            $markiInfo = target('warehouse/WarehouseMarki')->getInfo($markiId);
            $name = $markiInfo['name'] . '(' . $markiInfo['tel'] . ')';
            $no = '';
        }
        if ($deliveryType == 0) {
            $typeName = '无需物流';
        }

        target('tools/Tools', 'service')->notice('order', 'delivery', $orderInfo['order_user_id'], [
            '用户名' => $orderInfo['show_name'],
            '订单编号' => $orderInfo['order_no'],
            '订单标题' => $orderInfo['order_title'],
            '下单时间' => date('Y-m-d H:i:s', $orderInfo['order_create_time']),
            '快递费用' => $orderInfo['delivery_price'],
            '发货方式' => $typeName,
            '快递名称' => $name ? $name : '无需物流',
            '快递单号' => $no ? $no : '无',
            '发货时间' => date('Y-m-d H:i:s'),
            '收件信息' => $orderInfo['receive_name'] . ' ' . $orderInfo['receive_tel'] . ' ' . $orderInfo['receive_city'] . $orderInfo['receive_region'] . $orderInfo['receive_address'],
        ], 'pages/order/detail?order_app=' . $orderInfo['order_app'] . '&order_no=' . $orderInfo['order_no']);

        return $this->success();
    }

    /**
     * 自动完成订单
     * @param $data
     * @param $orderId
     * @return bool
     */
    public function autoConfirm($data, $orderId) {
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId);
        if ($orderInfo['order_complete_status'] || !$orderInfo['order_status']) {
            return true;
        }
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderId,
            'service_status' => 1,
        ]);
        if (!empty($orderGoods)) {
            //存在售后中商品，推迟3天确认
            $time = time() + 3 * 86400;
            if (!target('tools/Queue', 'service')->add('send', $orderId, '订单完成推迟确认', 'order/Order', 'autoConfirm', 'service', [], $time)) {
                return $this->error('队列加入失败!');
            }
            return true;
        }
        return $this->confirmOrder($orderId);
    }

    /**
     * 确认收货订单
     * @param $orderId
     * @return bool
     */
    public function confirmOrder($orderId) {
        $model = target('order/Order');
        $orderInfo = $model->getInfo($orderId);
        $app = $orderInfo['order_app'];
        $status = $model->edit([
            'order_id' => $orderId,
            'order_complete_status' => 1,
            'order_complete_time' => time(),
        ]);

        if (!$status) {
            return $this->error('订单确认失败!');
        }

        $status = target('order/OrderDelivery')->where([
            'order_id' => $orderId,
        ])->data([
            'receive_status' => 1,
            'receive_time' => time(),
        ])->update();

        if (!$status) {
            return $this->error('订单确认失败!');
        }

        if (!$this->addLog($orderId, '订单已确认收货')) {
            return $this->error('订单日志记录失败!');
        }

        if (!target($app . '/Order', 'service')->confirmOrder($orderInfo)) {
            return $this->error(target($app . '/Order', 'service')->getError());
        }

        //被动接口
        $hookList = run('service', 'Order', 'hookConfirmOrder', [$orderInfo]);

        if (!empty($hookList)) {
            foreach ($hookList as $a => $vo) {
                if (!$vo) {
                    return $this->error(target($a . '/Order', 'service')->getError());
                }
            }
        }
        //用户升级
        $userInfo = target('member/MemberUser')->getInfo($orderInfo['order_user_id']);
        $levelInfo = target('member/MemberGrade')->getWhereInfo([
            'grade_id' => $userInfo['grade_id'],
        ]);
        if ($levelInfo['update_status']) {
            $levelList = target('member/MemberGrade')->loadList([
                'update_status' => 1,
                'sort[>]' => $levelInfo['sort'],
            ], 0, 'sort asc, grade_id asc');
            $levelInfo = $levelList[0];
            $total = target('order/Order')
                ->field(['SUM(pay_price + delivery_price - refund_price) as total'])
                ->where([
                    'order_user_id' => $orderInfo['order_user_id'],
                    'order_status' => 1,
                    'pay_status' => 1,
                    'order_complete_status' => 1,
                ])->find();
            if ($levelInfo && bccomp($total['total'], $levelInfo['update_money'], 2) == 1) {
                $status = target('member/MemberUser')->edit([
                    'user_id' => $userInfo['user_id'],
                    'grade_id' => $levelInfo['grade_id'],
                ]);
                if (!$status) {
                    return $this->error(target('member/MemberUser')->getError());
                }
            }
        }

        //确认通知
        $deliveryInfo = target('order/OrderDelivery')->getWhereInfo([
            'A.order_id' => $orderInfo['order_id'],
        ]);

        target('tools/Tools', 'service')->notice('order', 'complete', $orderInfo['order_user_id'], [
            '用户名' => $orderInfo['show_name'],
            '订单编号' => $orderInfo['order_no'],
            '订单标题' => $orderInfo['order_title'],
            '下单时间' => date('Y-m-d H:i:s', $orderInfo['order_create_time']),
            '确认时间' => date('Y-m-d H:i:s', time()),
            '订单金额' => $orderInfo['order_price'],
            '发货时间' => $deliveryInfo['create_time'] ? date('Y-m-d H:i:s', $deliveryInfo['create_time']) : $orderInfo['pay_time'],
        ], 'pages/order/detail?order_app=' . $orderInfo['order_app'] . '&order_no=' . $orderInfo['order_no']);

        // 确认配送完成
        if (!target('order/OrderDelivery')->where(['order_id' => $orderId])->data(['receive_status' => 1, 'receive_time' => time()])->update()) {
            return $this->error(target('order/OrderDelivery')->getError());
        }

        $count = target('warehouse/WarehouseMarkiDelivery')->where(['order_id' => $orderId, 'marki_id' => 0])->count();
        if($count) {
            return $this->error('该订单配送员暂未接单!');
        }

        if (!target('warehouse/WarehouseMarkiDelivery')->where(['order_id' => $orderId])->data(['receive_status' => 1, 'receive_time' => time()])->update()) {
            return $this->error(target('warehouse/WarehouseMarkiDelivery')->getError());
        }

        return $this->success();
    }

    /**
     * 增加订单日志
     * @param $orderId
     * @param $msg
     * @param string $remark
     * @param int $systemId
     * @return mixed
     */
    public function addLog($orderId, $msg, $remark = '', $systemId = 0) {
        return target('order/OrderLog')->add([
            'order_id' => $orderId,
            'msg' => $msg,
            'remark' => $remark,
            'time' => time(),
            'ip' => \dux\lib\Client::getUserIp(),
            'system_id' => $systemId,
        ]);
    }

    /**
     * 获取订单跟踪记录
     * @param $deliveryId
     * @return bool
     */
    public function getWaybillLog($deliveryId) {
        $deliveryInfo = target('order/OrderDelivery')->getWhereInfo([
            'A.delivery_id' => $deliveryId,
        ]);

        if ($deliveryInfo['delivery_log_update'] + 21600 >= time()) {
            return $this->success($deliveryInfo['delivery_log']);
        }

        $orderConfig = target('order/OrderConfig')->getConfig();
        $waybillType = target('order/OrderConfigWaybill')->typeInfo($orderConfig['waybill_type']);

        if (empty($waybillType)) {
            return $this->error('物流查询接口不存在');
        }

        $expressInfo = target('order/OrderConfigExpress')->getWhereInfo([
            'name' => $deliveryInfo['delivery_name'],
        ]);

        if (empty($expressInfo)) {
            return $this->error('该配送类型不存在');
        }

        $target = target($waybillType['target'], 'service');
        $log = $target->query($deliveryInfo['delivery_name'], $expressInfo['label'], $deliveryInfo['delivery_no']);

        if (!$log) {
            return $this->error($target->getError());
        }

        target('order/OrderDelivery')->edit([
            'delivery_id' => $deliveryInfo['delivery_id'],
            'log' => serialize($log),
            'log_update' => time(),
        ]);

        return $this->success($log);
    }

    /**
     * 订单状态
     * @param $info
     * @return array
     */
    public function getAction($info) {
        if (!$info['order_status']) {
            return [
                'name' => '已取消',
                'color' => 'cancel',
                'action' => 'close',
                'message' => '订单已取消，请重新下单',
                'type' => 0,
            ];
        }

        if ($info['pay_type'] && !$info['pay_status']) {
            return [
                'name' => '未付款',
                'color' => 'danger',
                'action' => 'pay',
                'message' => '商品未付款，请及时进行支付',
                'type' => 1,
            ];
        }

        if (!$info['parcel_status']) {
            return [
                'name' => '待配货',
                'color' => 'primary',
                'action' => 'parcel',
                'message' => '订单等待商家配货中，请耐心等待',
                'type' => 2,
            ];
        }

        if (!$info['delivery_status']) {
            return [
                'name' => '待配送',
                'color' => 'primary',
                'action' => 'delivery',
                'message' => '订单正在发货中，请耐心等待',
                'type' => 3,
            ];
        }

        if (!$info['pay_type'] && !$info['pay_status']) {
            return [
                'name' => '配送中',
                'color' => 'warning',
                'action' => 'pay',
                'message' => '货到付款订单已发货，等待货到支付',
                'type' => 7,
            ];
        }

        if (!$info['order_complete_status']) {
            return [
                'name' => '配送中',
                'color' => 'warning',
                'action' => 'receive',
                'message' => '订单已发货，等待确认收货',
                'type' => 4,
            ];
        }

        if ($info['order_complete_status']) {
            if (!$info['comment_status']) {
                return [
                    'name' => '待评价',
                    'color' => 'warning',
                    'action' => 'comment',
                    'message' => '订单已确认签收，请对商品进行评价',
                    'type' => 5,
                ];
            } else {
                return [
                    'name' => '已完成',
                    'color' => 'success',
                    'action' => 'complete',
                    'message' => '订单已完成，欢迎您下次光临',
                    'type' => 6,
                ];
            }
        }
    }

    /**
     * 订单管理状态
     * @param $info
     * @return array
     */
    public function getManageStatus($info) {
        $data = [];

        if ($info['order_status'] && !$info['pay_status']) {
            $data['pay'] = true;
        }

        if ($info['order_status'] && ($info['pay_status'] || !$info['pay_type']) && !$info['parcel_status']) {
            $data['parcel'] = true;
        }

        if ($info['order_status'] && $info['parcel_status'] && !$info['delivery_status']) {
            $data['delivery'] = true;
        }

        if ($info['order_status'] && $info['pay_status'] && $info['delivery_status'] && !$info['order_complete_status']) {
            $data['complete'] = true;
        }

        if ($info['order_status'] && !$info['order_complete_status']) {
            $data['close'] = true;
        }

        return $data;
    }

    /**
     * 订单操作状态
     */
    public function getActionStatus($info, $goodsList) {
        $parcelInfo = target('order/OrderParcel')->getWhereInfo([
            'A.order_id' => $info['order_id'],
        ]);
        foreach ($goodsList as $key => $vo) {
            if ((($info['status_data']['action'] == 'parcel' && $parcelInfo['status'] > 1) || $info['status_data']['action'] == 'delivery' || $info['status_data']['action'] == 'receive') && !$vo['service_status']) {
                $goodsList[$key]['action_service'] = true;
            }
            if (($info['status_data']['action'] == 'comment' && $parcelInfo['complete'] == 3) && !$vo['comment_status']) {
                $goodsList[$key]['action_comment'] = true;
            }
        }
        return $goodsList;

    }

    /**
     * 获取货币信息
     * @param $list
     * @return array
     */
    public function getCurrency($list) {
        $currencyType = target('member/MemberCurrency')->typeList();

        $exchangeMaxLimit = [];
        $exchangeMinLimit = [];
        $exchangeMoney = [];
        $currencyData = [];

        foreach ($list as $key => $val) {
            if (empty($val['currency'])) {
                continue;
            }
            $money = $val['currency']['money'] * $val['qty'];
            $currencyData[$val['currency']['type']] += $money;
            if (!$currencyType[$val['currency']['type']]['hybrid']) {
                $exchangeMoney[$val['currency']['type']] += $val['total'];
                $exchangeData[$val['currency']['type']] = $val['currency'];
                $exchangeMaxLimit[$val['currency']['type']] += $val['currency']['max_limit'] * $val['qty'];
                $exchangeMinLimit[$val['currency']['type']] += $val['currency']['min_limit'] * $val['qty'];
            }
        }

        $currencyAppend = [];
        $currencyExchange = [];
        foreach ($currencyData as $key => $vo) {

            if ($currencyType[$key]['hybrid']) {
                $currencyAppend[] = [
                    'type' => $key,
                    'name' => $currencyType[$key]['name'],
                    'unit' => $currencyType[$key]['unit'],
                    'money' => $vo,
                ];
            } else {
                $rate = $currencyType[$key]['rate'] ? $currencyType[$key]['rate'] : 1;
                $curData = [
                    'type' => $key,
                    'name' => $currencyType[$key]['name'],
                    'unit' => $currencyType[$key]['unit'],
                    'rate_money' => price_calculate(1, '/', $rate, 2), //兑换比例
                    'rate' => $rate,
                    'max_limit' => $exchangeMaxLimit[$key], //最大使用
                    'min_limit' => $exchangeMinLimit[$key], //最小使用
                    'money' => 0,
                ];

                $curCurrency = price_calculate($exchangeMoney[$key], '/', $rate, 2);
                if ($curCurrency < $curData['max_limit']) {
                    $curData['max_limit'] = $curCurrency;
                } else if (empty($curData['max_limit'])) {
                    $curData['max_limit'] = $curCurrency;
                }
                $currencyExchange[] = $curData;
            }
        }

        return [
            'append' => $currencyAppend,
            'exchange' => $currencyExchange,
        ];
    }

    /**
     * 拆分订单
     * @param string $area
     * @param array $data
     * @return array
     */
    public function splitOrder($area = '', $data = []) {
        if (empty($data)) {
            return [];
        }

        $appData = [];
        $orderData = [];
        foreach ($data as $key => $vo) {
            $appData[$vo['app']][] = $vo;
        }

        foreach ($appData as $app => $pro) {
            $invoice = 0;
            foreach ($pro as $k => $v) {
                if ($invoice) {
                    break;
                }
                if ($v['invoice_status']) {
                    $invoice = 1;
                }
            }

            $expressData = [];
            $takeData = [];
            foreach ($pro as $vo) {
                if (!$vo['type']) {
                    $expressData[] = $vo;
                } else {
                    $takeData[] = $vo;
                }

            }

            if ($expressData) {
                $priceData = $this->priceData($expressData, $area);
                $orderData[] = array_merge($priceData, ['app' => $app, 'type' => 0, 'invoice_status' => $invoice, 'items' => $expressData]);
            }

            if ($takeData) {
                $priceData = $this->priceData($takeData, $area);
                $orderData[] = array_merge($priceData, ['app' => $app, 'type' => 1, 'invoice_status' => $invoice, 'items' => $takeData]);
            }

        }

        $hookList = run('service', 'Order', 'splitOrder', [$orderData]);

        $config = target('site/SiteConfig')->getConfig();

        foreach ($orderData as $key => $vo) {
            $ids = [];
            foreach ($vo['items'] as $k => $v) {
                $ids[] = $v['id'];
                $currency = target('order/Order', 'service')->getCurrency([$v]);
                $orderData[$key]['items'][$k]['currency_data'] = [
                    'append' => (array)$currency['append'],
                    'exchange' => (array)$currency['exchange'],
                ];
            }
            $orderData[$key]['ids'] = implode(',', $ids);
            $orderData[$key]['store'] = [
                'name' => $config['info_name'],
                'id' => 0,
            ];
            $orderData[$key]['currency_data'] = target('order/Order', 'service')->getCurrency($vo['items']);
        }

        if (!empty($hookList)) {
            $hookData = [];
            foreach ($hookList as $app => $vo) {
                $hookData = array_merge($hookData, $vo);
            }
            $orderData = $hookData ? $hookData : $orderData;
        }

        return $orderData;
    }

    /**
     * 价格计算
     * @param $data
     * @param $area
     * @return array
     */
    public function priceData($data, $area) {
        $weightData = [];
        $tplIds = [];
        $freightPrice = 0;
        $orderPrice = 0;
        $orderCount = 0;

        foreach ($data as $vo) {
            if ($vo['freight_type']) {
                //模板运费
                $tplIds[] = $vo['freight_tpl'];
                $weightData[$vo['freight_tpl']] += $vo['weight'] * $vo['qty'];
            } else {
                //固定运费
                $freightPrice = price_calculate($freightPrice, '+', $vo['freight_price']);
            }
            $orderCount += $vo['qty'];
            $orderPrice = price_calculate($orderPrice, '+', $vo['price'] * $vo['qty']);
        }

        $tplIds = array_unique($tplIds);
        $tplList = [];

        if ($tplIds) {
            $tplList = target('order/OrderConfigDelivery')->loadList([
                '_sql' => 'delivery_id in (' . implode(',', $tplIds) . ')',
            ]);
        }

        foreach ($tplList as $key => $vo) {
            $areaList = unserialize($vo['area']);

            if (!empty($areaList)) {
                foreach ($areaList as $v) {
                    $areaData = explode(',', $v['area']);
                    if (in_array($area, $areaData)) {
                        $vo['first_price'] = $v['first_price'];
                        $vo['second_price'] = $v['second_price'];
                    }
                }
            }
            $freightPrice = price_calculate($freightPrice, '+', $vo['first_price']);
            $orderPrice = price_calculate($orderPrice, '+', $vo['price'] * $vo['qty']);

            $weight = $weightData[$vo['tpl_id']];

            if ($weight <= 0) {
                continue;
            }

            if ($weight < $vo['first_weight']) {
                continue;
            }

            $secondWeight = $weight - $vo['first_weight'];
            $secondWeight = $vo['second_weight'] ? ceil($secondWeight / $vo['second_weight']) : 0;
            $freightPrice += $secondWeight * $vo['second_price'];
            $freightPrice = price_calculate($freightPrice, '*', $vo['second_price']);
        }

        return [
            'order_price' => $orderPrice,
            'delivery_price' => $freightPrice,
            'order_sum' => $orderCount,
        ];
    }
}
