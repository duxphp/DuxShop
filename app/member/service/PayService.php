<?php
namespace app\member\service;
/**
 * 系统支付接口
 */
class PayService {

    /**
     * 获取支付类型接口
     */
    public function getTypePay() {
        return array(
            'alipay_app' => array(
                'name' => '支付宝',
                'desc' => '支付宝APP支付接口',
                'info' => '支付宝安全支付',
                'target' => 'member/AlipayApp',
                'manage' => 'member/PayLog/info',
                'platform' => 'app',
                'order' => 1,
                'configRule' => array(
                    'partner' =>   'pid',
                    'appid' =>    'Appid',
                    'key' =>    'key密钥',
                    'private_key' =>    'RSA2私钥',
                    'public_key' =>    'RSA2支付宝公钥',
                )
            ),
            'alipay_mobile' => array(
                'name' => '支付宝',
                'desc' => '支付宝手机版支付接口',
                'info' => '支付宝安全支付',
                'target' => 'member/AlipayMobile',
                'manage' => 'member/PayLog/info',
                'platform' => 'wap',
                'order' => 1,
                'configRule' => array(
                    'partner' =>   'pid',
                    'appid' =>    'Appid',
                    'key' =>    'key密钥',
                    'private_key' =>    'RSA2私钥',
                    'public_key' =>    'RSA2支付宝公钥',
                )
            ),
            'alipay_web' => array(
                'name' => '支付宝',
                'desc' => '支付宝电脑支付接口',
                'info' => '支付宝安全支付',
                'target' => 'member/AlipayWeb',
                'manage' => 'member/PayLog/info',
                'platform' => 'web',
                'order' => 1,
                'configRule' => array(
                    'partner' =>   'pid',
                    'appid' =>    'Appid',
                    'key' =>    'key密钥',
                    'private_key' =>    'RSA2私钥',
                    'public_key' =>    'RSA2支付宝公钥',
                )
            ),

            'system' => array(
                'name' => '余额支付',
                'desc' => '系统内账户余额支付',
                'info' => '账户余额支付',
                'target' => 'member/System',
                'manage' => 'member/PayLog/info',
                'service' => 'member/Finance',
                'platform' => 'all',
                'internal' => true,
                'order' => 0,
                'configRule' => array(
                    'password' => [
                        'name' => '支付密码',
                        'type' => 'radio'
                    ]
                )
            ),


        );
    }

    /**
     * 获取回调接口
     */
    public function getCallbackPay() {
        return [
            'member' =>  [
                'name' => '会员充值',
                'target' => 'member/PayOrder'
            ]
        ];
    }
}
