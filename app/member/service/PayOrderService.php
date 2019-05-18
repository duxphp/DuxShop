<?php
namespace app\member\service;
/**
 * 支付订单处理
 */
class PayOrderService extends \app\base\service\BaseService {

    public function pay($rechargeNo, $money, $payName, $payNo, $payWay = 'system') {
        if(!target('member/Member', 'service')->payRecharge($rechargeNo, $money, $payName, $payNo, $payWay)) {
            return $this->error(target('member/Member', 'service')->getError());
        }
        return $this->success();
    }
}
