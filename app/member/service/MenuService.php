<?php

namespace app\member\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return [
            'statis' => [
                'menu' => [
                    [
                        'name' => '会员',
                        'order' => 3,
                        'menu' => [
                            [
                                'name' => '消费排行',
                                'url' => url('member/MemberRanking/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '增长趋势',
                                'url' => url('member/MemberTrend/index'),
                                'order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'member' => [
                'name' => '会员',
                'icon' => 'users',
                'order' => 98,
                'menu' => [
                    [
                        'name' => '会员',
                        'order' => 0,
                        'menu' => [
                            [
                                'name' => '会员管理',
                                'url' => url('member/MemberUser/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '角色管理',
                                'url' => url('member/MemberRole/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '会员等级',
                                'url' => url('member/MemberGrade/index'),
                                'order' => 2,
                            ],
                            [
                                'name' => '实名制管理',
                                'url' => url('member/MemberReal/index'),
                                'order' => 3,
                            ],
                        ],
                    ],
                    [
                        'name' => '设置',
                        'order' => 10,
                        'menu' => [
                            [
                                'name' => '会员设置',
                                'url' => url('member/MemberConfig/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '会员验证码',
                                'url' => url('member/MemberVerify/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '支付接口',
                                'url' => url('member/PayConf/index'),
                                'order' => 2,
                            ],
                            [
                                'name' => '通知设置',
                                'url' => url('tools/SendData/index'),
                                'order' => 3,
                            ],
                        ],
                    ],
                ],
            ],
            'account' => [
                'name' => '财务',
                'icon' => 'bank',
                'order' => 98,
                'menu' => [
                    [
                        'name' => '用户',
                        'order' => 1,
                        'menu' => [
                            [
                                'name' => '财务账户',
                                'url' => url('member/PayAccount/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '交易记录',
                                'url' => url('member/PayLog/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '银行管理',
                                'url' => url('member/PayBank/index'),
                                'order' => 3,
                            ],
                            [
                                'name' => '银行卡管理',
                                'url' => url('member/PayCard/index'),
                                'order' => 4,
                            ],
                            [
                                'name' => '提现管理',
                                'url' => url('member/PayCash/index'),
                                'order' => 5,
                            ],
                        ],
                    ],
                    [
                        'name' => '积分',
                        'order' => 2,
                        'menu' => [
                            [
                                'name' => '积分账户',
                                'url' => url('member/PointsAccount/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '积分记录',
                                'url' => url('member/PointsLog/index'),
                                'order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

