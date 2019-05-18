<?php

/**
 * 订单管理
 */

namespace app\mall\middle;

class OrderMiddle extends \app\base\middle\BaseMiddle {

    protected function meta() {
        $this->setMeta('订单详情');
        $this->setName('订单详情');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '我的订单',
                'url' => url('order/Order/index')
            ],
            [
                'name' => '订单详情',
                'url' => url('info', ['order_no' => $this->params['order_no']])
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }


    protected function info() {
        $userId = intval($this->params['user_id']);
        $orderNo = html_clear($this->params['order_no']);
        if (empty($orderNo)) {
            return $this->stop('订单号不存在!', 404);
        }
        $orderInfo = target('mall/MallOrder')->getWhereInfo([
            'B.order_no' => $orderNo,
            'B.order_user_id' => $userId
        ]);
        if (empty($orderInfo)) {
            return $this->stop('订单不存在!', 404);
        }
        $payData = [];
        if($orderInfo['pay_status']) {
            $payList = target('member/PayConfig')->typeList();
            foreach ($orderInfo['pay_data'] as $vo) {
                $payTypeInfo = $payList[$vo['way']];
                $payData[] = array_merge(target('statis/StatisFinancialLog')->getInfo($vo['id']), ['pay_type' => $payTypeInfo['name']]);
            }
        }
        $deliveryInfo = [];
        $markiList = [];
        if ($orderInfo['delivery_status']) {
            $deliveryInfo = target('order/OrderDelivery')->field(['delivery_name', 'delivery_no'])->where(['order_id' => $orderInfo['order_id']])->find();
            $markiList = target('warehouse/WarehouseMarkiDelivery')->loadList(['A.order_id' => $orderInfo['order_id']]);

        }
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id']
        ], 0, 'id asc');

        $orderGoods = target('order/Order', 'service')->getActionStatus($orderInfo, $orderGoods);

        $takeInfo = target('order/OrderTake')->getInfo($orderInfo['take_id']);
        $invoiceInfo = target('order/OrderInvoice')->getWhereInfo([
            'C.order_id' => $orderInfo['order_id'],
        ]);

        return $this->run([
            'payData' => $payData,
            'info' => $orderInfo,
            'orderGoods' => $orderGoods,
            'deliveryInfo' => (object)$deliveryInfo,
            'markiList' => $markiList,
            'takeInfo' => (object)$takeInfo,
            'invoiceInfo' => (object)$invoiceInfo,
        ]);
    }


}