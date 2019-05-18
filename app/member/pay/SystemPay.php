<?php

namespace app\member\pay;
/**
 * 余额支付
 */
class SystemPay extends \app\base\service\BaseService {


    public function getData($payInfo, $returnUrl) {
        $orderPayNo = $payInfo['order_no'];
        $data = [];
        $data['user_id'] = $payInfo['user_id'];
        $data['type'] = 0;
        $data['money'] = $payInfo['money'];
        $payId = target('member/Finance', 'service')->account($data);
        if (!$payId) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        $app = $payInfo['app'];
        $callbackList = target('member/PayConfig')->callbackList();
        $callbackInfo = $callbackList[$app];
        if(!target($callbackInfo['target'], 'service')->pay($orderPayNo, $payInfo['money'], '账号支付', $orderPayNo, 'system')) {
            dux_log(target($callbackInfo['target'], 'service')->getError());
            return $this->error(target($callbackInfo['target'], 'service')->getError());
        }
        return $this->success([
            'url' => $returnUrl,
            'complete' => true
        ]);
    }

    public function transfer($data) {
        $accountStatus = target('member/Finance', 'service')->account([
            'user_id' => $data['user_id'],
            'money' => $data['money'],
            'type' => 1,
        ]);
        if (!$accountStatus) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        $accountStatus = target('statis/Finance', 'service')->account([
            'user_id' => $data['user_id'],
            'species' => 'member_account',
            'sub_species' => 'cash',
            'no' => $data['pay_no'],
            'money' => $data['money'],
            'type' => 1,
            'title' => '系统转账',
            'remark' => $data['remark'],
        ]);
        if (!$accountStatus) {
            return $this->error(target('statis/Finance', 'service')->getError());
        }
        return $data['pay_no'];

    }

    public function refund($payData) {
        $data = [];
        $payNo = log_no($payData['user_id']);
        $data['user_id'] = $payData['user_id'];
        $data['money'] = $payData['money'];
        if (!target('member/Finance', 'service')->account($data)) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        return $this->success($payNo);
    }


}