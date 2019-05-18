<?php
namespace app\member\pay;
/**
 * 支付宝WEB端服务
 */
class AlipayAppPay extends \app\base\service\BaseService {


    public function getConfig() {
        $config = target('member/PayConfig')->getConfig('alipay_app');
        if (empty($config['partner']) || empty($config['key'])) {
            return $this->error('请先配置支付接口信息!');
        }
        $notifyUrl = url('api/member/AlipayApp/index', [], true);
        $config = [
            //'partner' => $config['partner'],
            'app_id' => $config['appid'],
            'ali_public_key' => $config['public_key'],
            'private_key' => $config['private_key'],
            'notify_url' => $notifyUrl,
        ];
        return $config;
    }

    public function getData($data) {
        if (empty($data)) {
            return $this->error('订单数据未提交!');
        }
        $config = $this->getConfig();
        $payData = [
            'out_trade_no' => $data['order_no'],
            'total_amount' => $data['money'] ? price_format($data['money']) : 0,
            'subject' => str_len($data['title'], 125),
            //'body' => $data['title'] ? $data['title'] : $data['body'],
            'passback_params' => urlencode(http_build_query(['app' => $data['app']])),
            //'timeout_express' => time() + 604800
        ];

        if (empty($payData['out_trade_no'])) {
            return $this->error('订单号不能为空!');
        }
        if ($payData['total_amount'] <= 0) {
            return $this->error('支付金额不正确!');
        }
        if (empty($payData['subject'])) {
            return $this->error('支付信息描述不正确!');
        }
        if (empty($payData['passback_params'])) {
            return $this->error('订单应用名不正确!');
        }
        try {
            $pay = \Yansongda\Pay\Pay::alipay($config)->app($payData);
            return $this->success($pay->getContent());
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function refund($data) {
        $payData = [
            'trade_no' => $data['pay_no'],
            'refund_reason' => $data['remark'],
            'refund_amount' => $data['money'],
        ];

        if (bccomp(0, $payData['refund_amount'], 2) !== -1) {
            return $this->error('退款金额不正确!');
        }
        if (empty($payData['trade_no']) && empty($payData['out_trade_no'])) {
            return $this->error('退款单号不正确!');
        }
        $config = $this->getConfig();
        try {
            $return = \Yansongda\Pay\Pay::alipay($config)->refund($payData);
            return $this->success($return['trade_no']);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


}