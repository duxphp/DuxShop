<?php
namespace app\member\pay;
/**
 * 支付宝WEB端服务
 */
class AlipayWebPay extends \app\base\service\BaseService {


    public function getConfig($notifyUrl = '') {
        $config = target('member/PayConfig')->getConfig('alipay_web');
        if (empty($config['partner']) || empty($config['key'])) {
            return $this->error('请先配置支付接口信息!');
        }
        $config = [
            //'partner' => $config['partner'],
            'app_id' => $config['appid'],
            'ali_public_key' => $config['public_key'],
            'private_key' => $config['private_key'],
            'notify_url' => $notifyUrl,
        ];
        return $config;
    }

    public function getData($data, $returnUrl) {
        if (empty($data)) {
            return $this->error('订单数据未提交!');
        }
        unset($data['user_id']);
        $data['return_url'] = urlencode(DOMAIN . $returnUrl);
        $data['tmp'] = time();
        $data['token'] = data_sign($data);
        $url = url('controller/member/Alipay/index') . '?' . http_build_query($data);
        return $this->success([
            'url' => $url
        ]);
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