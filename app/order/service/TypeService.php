<?php

namespace app\order\service;
/**
 * 类型接口
 */
class TypeService {

    public function getWaybillType() {
        return [
            'kdniao' => [
                'name' => '快递鸟',
                'target' => 'order/Kdniao',
                'desc' => '请自行申请快递鸟接口',
                'url' => 'http://www.kdniao.com/',
                'waybill' => true,
                'configRule' => [
                    'id' => '商户ID',
                    'key' => 'API密钥',
                ],
            ],
        ];
    }

    public function getMemberType($userInfo) {
        return [
            [
                'tpl' => 'order',
                'sort' => 1,
                'data' => [
                    'url' => '/pages/order/index',
                    'list' => [
                        [
                            'icon' => 'daifukuan',
                            'name' => '待付款',
                            'url' => '/pages/order/index?nav=1',
                            'number' => $this->countOrder(1, $userInfo['user_id']),
                        ],
                        [
                            'icon' => 'daifahuo',
                            'name' => '待配送',
                            'url' => '/pages/order/index?nav=2',
                            'number' => $this->countOrder(2, $userInfo['user_id']),
                        ],
                        [
                            'icon' => 'daishouhuo',
                            'name' => '配送中',
                            'url' => '/pages/order/index?nav=3',
                            'number' => $this->countOrder(3, $userInfo['user_id']),
                        ],
                        [
                            'icon' => 'tuihuanhuo',
                            'name' => '退换货',
                            'url' => '/pages/refund/index',
                            'number' => 0,
                        ],
                    ],
                ],

            ],
        ];
    }

    private function countOrder($type, $userId) {
        $where = [];
        $where['order_status'] = 1;
        $where['order_user_id'] = $userId;
        switch ($type) {
            case 1:
                $where['pay_type'] = 1;
                $where['pay_status'] = 0;
                $where['delivery_status'] = 0;
                break;
            case 2:
                $where['_sql'][] = '(pay_type = 0 OR pay_status = 1)';
                $where['delivery_status'] = 0;
                break;
            case 3:
                $where['delivery_status'] = 1;
                $where['order_complete_status'] = 0;
                break;
            case 4:
                $where['order_complete_status'] = 1;
                $where['comment_status'] = 0;
                break;
        }
        $model = target('order/Order');
        return $model->countList($where);
    }

    public function getFinancialType() {
        return [
            'member' => [
                'list' => [
                    'member_order' => [
                        'name' => '订单',
                        'list' => [
                            'pay' => [
                                'name' => '支付',
                            ],
                            'refund' => [
                                'name' => '退款',
                            ],
                            'discount' => [
                                'name' => '优惠',
                            ],
                        ],
                    ],
                ],
            ],
            'points' => [
                'list' => [
                    'points_coupon' => [
                        'name' => '优惠券兑换',
                    ],
                    'points_order' => [
                        'name' => '订单',
                        'list' => [
                            'pay' => [
                                'name' => '支付',
                            ],
                            'refund' => [
                                'name' => '退款',
                            ],
                            'reward' => [
                                'name' => '奖励',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
