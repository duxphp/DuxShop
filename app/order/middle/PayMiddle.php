<?php

/**
 * 订单支付
 */

namespace app\order\middle;


class PayMiddle extends \app\base\middle\BaseMiddle {


    protected function base() {
        $orderNo = html_clear($this->params['order_no']);
        $this->params['user_id'] = intval($this->params['user_id']);
        if (empty($orderNo)) {
            return $this->stop('订单号不存在!');
        }

        $orderNo = urldecode($orderNo);
        $orderNos = explode('|', $orderNo);
        $orderNo = implode('|', $orderNos);

        foreach ($orderNos as $key => $vo) {
            $orderNos[$key] = int_format($vo);
        }

        if (empty($orderNos)) {
            return $this->stop('订单不能存在!');
        }

        $where = [];
        $where['_sql'] = 'order_no in(' . implode(',', $orderNos) . ')';
        $orderList = target('order/Order')->LoadList($where);

        if (empty($orderList)) {
            return $this->stop('订单不存在!');
        }
        $payPrice = 0;
        $orderPrice = 0;
        $deliveryPrice = 0;
        $orderSum = 0;
        $orderIds = [];

        $currencyData = [];
        $currencyAppend = [];
        $currencyExchange = [];

        foreach ($orderList as $key => $vo) {
            if ($vo['pay_status'] || !$vo['order_status'] || $vo['order_user_id'] <> $this->params['user_id']) {
                unset($orderList[$key]);
                continue;
            }

            //支付总和
            $orderIds[] = $vo['order_id'];
            $orderPrice += $vo['order_price'];
            $deliveryPrice += $vo['delivery_price'];
            $orderSum += $vo['order_sum'];
            $payPrice += $vo['pay_price'];

            //扩展支付
            if(empty($vo['pay_currency'])) {
                continue;
            }
            foreach ($vo['pay_currency']['append'] as $k => $v) {
                if($v['status']) {
                    continue;
                }
                $currencyData[] = $k;
                if(empty($currencyAppend[$k])) {
                    $currencyAppend[$k] = $v;
                }else {
                    $currencyAppend[$k]['money'] += $v['money'];
                }
            }

            foreach ($vo['pay_currency']['exchange'] as $k => $v) {
                if($v['status']) {
                    continue;
                }
                $currencyData[] = $k;
                if(empty($currencyExchange[$k])) {
                    $currencyExchange[$k] = $v;
                }else {
                    $currencyExchange[$k]['money'] += $v['money'];
                }
            }
        }

        if (empty($orderList)) {
            return $this->stop('提交付款的订单无效或关闭!');
        }
        arsort($orderIds);

        $orderIds = implode(',', $orderIds);
        $orderPrice = price_format($orderPrice);
        $deliveryPrice = price_format($deliveryPrice);
        $payPrice = price_format($payPrice);

        $currencyType = target('member/MemberCurrency')->typeList();
        $currencyList = [];
        foreach ($currencyType as $key => $vo) {
            if (!in_array($key, $currencyData)) {
                unset($currencyType[$key]);
                continue;
            }
            $currencyType[$key]['type'] = $key;
            $currencyType[$key]['amount'] = target($vo['target'], 'service')->amountAccount($this->params['user_id']);
            $currencyList[] = $currencyType[$key];
        }


        $payList = target('member/PayConfig')->typeList(true, $this->params['platform'], true, true);
        $payList = array_reverse($payList);

        foreach ($payList as $key => $vo) {
            if ($vo['internal']) {
                $payList[$key]['amount'] = target($vo['service'], 'service')->amountAccount($this->params['user_id']);
            }
        }

        $totalPrice = price_calculate($payPrice, '+', $deliveryPrice);

        return $this->run([
            'orderNo' => $orderNo,
            'orderList' => $orderList,
            'currencyList' => $currencyList,
            'payList' => $payList,
            'totalPrice' => $totalPrice,
            'payPrice' => $payPrice,
            'orderPrice' => $orderPrice,
            'deliveryPrice' => $deliveryPrice,
            'orderSum' => $orderSum,
            'orderIds' => $orderIds,
            'currencyAppend' => $currencyAppend,
            'currencyExchange' => $currencyExchange
        ]);

    }

    protected function info() {
        $orderGoods = target('order/OrderGoods')->loadList([
            '_sql' => 'order_id in (' . $this->data['orderIds'] . ')'
        ]);
        $orderGroup = [];
        foreach ($orderGoods as $key => $vo) {
            $orderGroup[$vo['order_id']][] = $vo;
        }
        $orderList = $this->data['orderList'];
        foreach ($orderList as $key => $vo) {
            $orderList[$key]['order_items'] = $orderGroup[$vo['order_id']];
        }

        $payAccountInfo = target('member/PayAccount')->getWhereInfo([
            'A.user_id' => $this->params['user_id'],
        ]);

        return $this->run([
            'orderList' => $orderList,
            'config' => [
                'password_status' => $payAccountInfo['password'] ? true : false,
            ]
        ]);
    }

    protected function pay() {
        $type = html_clear($this->params['type']);
        $target = target('order/Order');

        $payPrice = $this->data['payPrice'];
        $deliveryPrice = $this->data['deliveryPrice'];

        $config = target('order/OrderConfig')->getConfig();
        foreach ($this->data['orderList'] as $key => $vo) {
            if($vo['order_create_time'] + $config['cancel_hour'] * 60 <= time() ) {
                //return $this->stop('该订单超过支付时间！');
            }
        }

        $orderData = [];
        foreach ($this->data['orderList'] as $key => $vo) {
            $orderData[$vo['order_app']][$key] = $vo;
        }
        foreach ($orderData as $app => $rows) {
            if (!target($app . '/Order', 'service')->checkOrder($rows)) {
                return $this->stop(target($app . '/Order', 'service')->getError());
            }
        }

        $target->beginTransaction();

        $postCurrency = $this->params['currency'];
        $postCurrency = $postCurrency ? $postCurrency : [];


        //$orderIdArray = explode(',', $this->data['orderIds']);
        //兑换支付
        $currencyPay = [];

        foreach ($this->data['currencyAppend'] as $vo) {
            $key = $vo['type'];
            if (!in_array($key, $postCurrency)) {
                return $this->stop('请选择' . $vo['name'] . '支付方式!');
            }
            if(bccomp(0, $vo['money'], 2) !== -1) {
                continue;
            }

            $currencyType = [];
            foreach ($this->data['currencyList'] as $v) {
                if($vo['type'] == $key) {
                    $currencyType = $v;
                }
            }
            $status = target($currencyType['target'], 'service')->account([
                'user_id' => $this->params['user_id'],
                'money' => $vo['money'],
                'type' => 0,
            ]);
            if (!$status) {
                return $this->stop(target($currencyType['target'], 'service')->getError());
            }

            $payId = target('statis/Finance', 'service')->account([
                'user_id' => $this->params['user_id'],
                'species' => $key . '_order',
                'sub_species' => 'pay',
                'money' => $vo['money'],
                'no' =>  $this->data['orderNo'],
                'type' => 0,
                'title' => '订单支付',
                'remark' => '订单【'.$this->data['orderNo'].'】付款'
            ]);
            if (!$payId) {
                return $this->stop(target('statis/Finance', 'service')->getError());
            }

            $currencyPay[$key] = [
                'pay_id' => $payId
            ];
        }

        foreach ($this->data['currencyExchange'] as $vo) {
            $key = $vo['type'];
            if (!in_array($key, $postCurrency)) {
                continue;
            }
            $currencyInfo = [];
            foreach ($this->data['currencyList'] as $v) {
                if($vo['type'] == $key) {
                    $currencyInfo = $v;
                }
            }


            if(bccomp(0, $currencyInfo['amount'], 2) !== -1) {
                continue;
            }

            $currencyMoney = price_calculate($payPrice, '/', $currencyInfo['rate']);


            if($vo['min_limit'] > $currencyMoney) {
                continue;
            }

            if($vo['max_limit'] < $currencyMoney) {
                $currencyMoney = $vo['max_limit'];
            }

            if($currencyMoney > $currencyInfo['amount']) {
                $currencyMoney = $currencyInfo['amount'];
            }

            $status = target($currencyInfo['target'], 'service')->account([
                'user_id' => $this->params['user_id'],
                'money' => $currencyMoney,
                'type' => 0,
            ]);
            if (!$status) {
                return $this->stop(target($currencyInfo['target'], 'service')->getError());
            }

            $payId = target('statis/Finance', 'service')->account([
                'user_id' => $this->params['user_id'],
                'species' => $key . '_order',
                'sub_species' => 'pay',
                'money' => $currencyMoney,
                'no' =>  $this->data['orderNo'],
                'type' => 0,
                'title' => '订单支付',
                'remark' => '订单【'.$this->data['orderNo'].'】付款'
            ]);
            if (!$payId) {
                return $this->stop(target('statis/Finance', 'service')->getError());
            }

            $this->data['currencyExchange'][$key]['id'] = $payId;
            $payCurrencyPrice = price_calculate($currencyMoney, '*', $currencyInfo['rate']);

            $payPrice = price_calculate($payPrice, '-', $payCurrencyPrice);

            $currencyPay[$key] = [
                'pay_id' => $payId,
                'pay_price' => $payCurrencyPrice,
                'currency_price' => $currencyMoney
            ];
        }

        //完成兑换支付状态
        if ($currencyPay) {
            foreach ($this->data['orderList'] as $key => $vo) {
                $orderCurrency = $vo['pay_currency'];
                $orderPayPrice = $vo['pay_price'];
                $orderDiscount = $vo['pay_discount'];
                $discountPrice = 0;
                foreach ($orderCurrency['append'] as $k => $v) {
                    $curPay = [];
                    foreach ($currencyPay as $ki => $vi) {
                        if($ki == $v['type']) {
                            $curPay = $vi;
                        }
                    }
                    if($curPay) {
                        $orderCurrency['append'][$k]['status'] = 1;
                        $orderCurrency['append'][$k]['id'] = $curPay['pay_id'];
                    }else {
                        unset($orderCurrency['append'][$k]);
                    }
                }
                foreach ($orderCurrency['exchange'] as $k => $v) {
                    $curPay = [];
                    foreach ($currencyPay as $ki => $vi) {
                        if($ki == $v['type']) {
                            $curPay = $vi;
                        }
                    }
                    if($curPay) {
                        $orderCurrency['exchange'][$k]['status'] = 1;
                        $orderCurrency['exchange'][$k]['id'] = $curPay['pay_id'];
                        $orderCurrency['exchange'][$k]['money'] = $curPay['currency_price'];
                        $orderCurrency['exchange'][$k]['deduct'] = $curPay['pay_price'];
                    }else {
                        unset($orderCurrency['exchange'][$k]);
                    }
                    if(bccomp(0, $curPay['pay_price'], 2) === -1) {
                        $orderPayPrice = price_calculate($orderPayPrice, '-', $curPay['pay_price']);
                        $orderDiscount = price_calculate($orderDiscount, '+', $curPay['pay_price']);
                        $discountPrice = price_calculate($discountPrice, '+', $curPay['pay_price']);
                    }
                }

                if (!$target->edit(['order_id' => $vo['order_id'], 'pay_discount'=> $orderDiscount, 'pay_price' => $orderPayPrice, 'pay_currency' => serialize($orderCurrency)])) {
                    return $this->stop($target->getError());
                }
                $orderGoods = target('order/OrderGoods')->loadList([
                    'order_id' => $vo['order_id']
                ]);

                //平均分配优惠金额
                $sumPrice = 0;
                foreach ($orderGoods as $goods) {
                    $goodsCurrency = $goods['goods_currency'];
                    foreach ($orderCurrency['exchange'] as $ki => $vi) {
                        if ($goodsCurrency['type'] == $ki) {
                            $sumPrice = price_calculate($sumPrice, '+', $goods['price_total'], 2);
                        }
                    }
                }

                foreach ($orderGoods as $k => $v) {
                    $goodsCurrency = $v['goods_currency'];
                    $goodsPrice = $v['price_total'];
                    $goodsDiscount = $v['price_discount'];
                    foreach ($orderCurrency['exchange'] as $ki => $vi) {
                        if($goodsCurrency['type'] == $ki) {
                            $discount = round($v['price_total']/$sumPrice * $orderDiscount, 2);
                            $goodsDiscount = price_calculate($goodsDiscount, '+', $discount, 2);
                            $goodsPrice = price_calculate($goodsPrice, '-', $discount, 2);
                            $goodsCurrency['money'] = price_calculate($discount, '*', $vi['rate']);
                        }else {
                            $goodsCurrency = [];
                        }
                    }
                    target('order/OrderGoods')->edit(['id' => $v['id'], 'price_total' => $goodsPrice,  'price_discount' => $goodsDiscount, 'goods_currency' => serialize($goodsCurrency)]);
                }
            }
        }
        $payPrice = price_calculate($payPrice, '+', $deliveryPrice);

        if(bccomp(0, $payPrice, 2) === 1) {
            $payPrice = 0;
        }

        if (bccomp(0, $payPrice, 2) === 1 || empty($type)) {
            $type = 'system';
        }
        $payList = $this->data['payList'];
        //终端支付
        foreach ($payList as $vo) {
            if($vo['type'] == $type) {
                $payTypeInfo = $vo;
            }
        }
        if (empty($payTypeInfo)) {
            return $this->stop('该支付类型不存在!');
        }
        $curInfo = current($this->data['orderList']);
        $title = $curInfo['order_title'];
        $password = $this->params['password'];

        if($payTypeInfo['password']) {
            $payAccountInfo = target('member/PayAccount')->getWhereInfo([
                'A.user_id' => $this->params['user_id'],
            ]);
            if(empty($payAccountInfo)) {
                return $this->stop('请先设置支付密码!');
            }
            if($payAccountInfo['password'] <> md5($password)) {
                return $this->stop('支付密码不正确！');
            }
        }

        $orderNos = [];
        $orderList = $this->data['orderList'];
        foreach ($orderList as $vo) {
            $orderNos[] = $vo['order_no'];
        }


        //创建合并支付订单
        $orderPayNo = target('order/Order', 'service')->addPay($this->params['user_id'], $this->data['orderIds'], $type);
        if (!$orderPayNo) {
            return $this->stop(target('order/Order', 'service')->getError());
        }
        $sign = data_sign($orderPayNo);

        $data = target($payTypeInfo['target'], 'pay')->getData([
            'user_id' => $this->params['user_id'],
            'open_id' => $this->params['open_id'],
            'order_no' => $orderPayNo,
            'money' => $payPrice,
            'title' => '订单支付',
            'body' => $title,
            'app' => 'order',
        ], url('complete', ['pay_no' => $orderPayNo, 'pay_sign' => $sign]));
        if (!$data) {
            $target->rollBack();
            return $this->stop(target($payTypeInfo['target'], 'pay')->getError());
        }

        //记录支付时间
        foreach ($orderList as $vo) {
            target('order/Order')->edit([
                'order_id' => $vo['order_id'],
                'pay_time' => time()
            ]);
        }
        $target->commit();
        return $this->run(['app' => $orderList[0]['order_app'], 'type' => $type, 'complete' => isset($data['complete']) ? $data['complete'] : 0, 'data' => $data, 'pay_no' => $orderPayNo, 'pay_sign' => $sign], '即将进行支付中!');
    }

    protected function complete() {
        $payNo = $this->params['pay_no'];
        $paySign = $this->params['pay_sign'];
        if (empty($payNo) || empty($paySign)) {
            return $this->stop('页面不存在', 404);
        }

        if(!data_sign_has($payNo, $paySign)) {
            return $this->stop('支付数据验证失败!', 404);
        }

        $name = '支付完成';
        $desc = '支付操作完成，请等待系统处理支付结果!';

        $list = hook('service', 'Type', 'PayComplete');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }

        return $this->run([
            'status' => 1,
            'name' => $name,
            'desc' => $desc,
            'payNo' => $payNo,
            'paySign' => $paySign,
            'hookList' => $data,
        ]);
    }

}
