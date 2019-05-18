<?php
namespace app\order\service;
/**
 * 支付订单处理
 */
class PayOrderService extends \app\base\service\BaseService {

    public function pay($rechargeNo, $money, $payName, $payNo, $payWay = 'system') {
        if(!target('order/Order', 'service')->payOrder($rechargeNo, $money, $payName, $payNo, $payWay)) {
            return $this->error(target('order/Order', 'service')->getError());
        }
        return $this->success();
    }


}
