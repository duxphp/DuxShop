<?php

namespace app\order\service;
/**
 * 发送接口
 */
class SendService {

    /**
     * 获取推送数据
     */
    public function getDataSend() {
        return [
            'order' => [
                'name' => '订单通知',
                'type' => [
                    'pay' => [
                        'name' => '支付通知',
                        'var' => '用户名,订单编号,订单标题,下单时间,订单金额,支付类型,支付号,支付金额,支付时间',
                        'remark' => '微信推荐使用编号"OPENTM207498902"的通知模板'
                    ],
                    'delivery' => [
                        'name' => '发货通知',
                        'var' => '用户名,订单编号,订单标题,下单时间,快递费用,发货方式,快递名称,快递单号,发货时间,收件信息',
                        'remark' => '微信推荐使用编号"OPENTM414956350"的通知模板'
                    ],
                    'complete' => [
                        'name' => '确认通知',
                        'var' => '用户名,订单编号,订单标题,订单金额,下单时间,确认时间,订单金额,发货时间',
                        'remark' => '微信推荐使用编号"OPENTM202314085"的通知模板'
                    ],
                ]
            ],
        ];

    }
}

