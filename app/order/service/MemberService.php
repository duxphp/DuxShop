<?php

namespace app\order\service;
/**
 * 用户接口
 */
class MemberService extends \app\base\service\BaseService {


    public function del($id) {
        $orderList = target('order/Order')->loadList([
            'order_user_id' => $id
        ]);
        if(empty($orderList)) {
            return true;
        }
        $orderIds = array_column($orderList, 'order_id');
        if (!target('order/Order')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderAddress')->where(['user_id' => $id])->delete()) {
            return false;
        }
        if (!target('order/OrderComment')->where(['user_id' => $id])->delete()) {
            return false;
        }
        if (!target('order/OrderDelivery')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderGoods')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderInvoice')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderLog')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderParcel')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        if (!target('order/OrderPay')->where(['user_id' => $id])->delete()) {
            return false;
        }
        if (!target('order/OrderReceipt')->where(['order_id' => $orderIds])->delete()) {
            return false;
        }
        $refundList = target('order/OrderRefund')->loadList([
            'user_id' => $id
        ]);
        if(empty($refundList)) {
            return true;
        }
        $refundIds = array_column($refundList, 'refund_id');
        if (!target('order/OrderRefund')->where(['user_id' => $id])->delete()) {
            return false;
        }
        if (!target('order/OrderRefundRemark')->where(['refund_id' => $refundIds])->delete()) {
            return false;
        }
        return true;
    }
}

