<?php

namespace app\member\service;
/**
 * 发送接口
 */
class SendService {

    /**
     * 获取推送数据
     */
    public function getDataSend() {
        return [
            'member' => [
                'name' => '会员通知',
                'type' => [
                    'recharge' => [
                        'name' => '充值通知',
                        'var' => '用户名,金额,备注,编号,交易名,时间',
                        'remark' => '推荐使用编号"TM00006"的通知模板',
                    ],
                    'cash' => [
                        'name' => '提现申请通知',
                        'var' => '昵称,时间,金额,提现方式',
                        'remark' => '',
                    ],
                    'cashcom' => [
                        'name' => '提现完成通知',
                        'var' => '昵称,时间,金额,提现方式',
                        'remark' => '',
                    ],
                    'cashfail' => [
                        'name' => '提现失败通知',
                        'var' => '昵称,时间,金额,提现方式',
                        'remark' => '',
                    ],
                ],
            ],
            
        ];

    }
}
