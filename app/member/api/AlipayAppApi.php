<?php

/**
 * 支付宝移动端通知
 */

namespace app\member\api;

class AlipayAppApi {

    public function index() {
        $urlParams = request('', 'passback_params', '', 'urldecode');
        $urlParams = urldecode($urlParams);
        parse_str($urlParams, $params);
        $app = $params['passback_params'];
        if(empty($app)) {
            dux_log('回调参数获取失败');
            dux_log($urlParams);
            return false;
        }

        $config = target('member/AlipayApp', 'pay')->getConfig();
        try{
            $alipay = \Yansongda\Pay\Pay::alipay($config);
            $data = $alipay->verify();
            if ($data['trade_status'] <> 'TRADE_SUCCESS') {
                dux_log('支付状态失败');
                return false;
            }
            $orderNo = $data['out_trade_no'];
            if (empty($orderNo)) {
                dux_log('支付号错误');
                return false;
            }
            $model = target('member/PayRecharge');

            $callbackList = target('member/PayConfig')->callbackList();
            $callbackInfo = $callbackList[$app];

            $model->beginTransaction();
            if(!target($callbackInfo['target'], 'service')->pay($orderNo, $data['total_amount'], '支付宝APP端', $data['trade_no'], 'alipay_app')) {
                $model->rollBack();
                dux_log(target($callbackInfo['target'], 'service')->getError());
                return false;
            }
            $model->commit();
            return $alipay->success()->send();
        } catch (\Exception $e) {
            dux_log($e->getMessage());
        }
    }

}