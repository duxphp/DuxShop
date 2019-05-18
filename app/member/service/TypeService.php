<?php

namespace app\member\service;
/**
 * 类型接口
 */
class TypeService {

    /**
     * 站点配置
     */
    public function getConfigType() {
        return [
            'member' => [
                'name' => '会员配置',
                'type' => 'tpl',
                'list' => [
                    [
                        'name' => '头部背景',
                        'field' => 'style_member_img',
                        'type' => 'image',
                        'help' => '建议上传750*400等比例图片'
                    ],
                    [
                        'name' => '登录背景',
                        'field' => 'style_login_img',
                        'type' => 'image',
                        'help' => '建议上传1080*1920等比例图片'
                    ]
                ]
            ]

        ];
    }

    /**
     * 货币接口
     */
    public function getCurrencyType() {
        return [
            'points' => [
                'name' => '积分',
                'unit' => '分',
                'target' => 'member/Points',
                'hybrid' => true
            ],
        ];
    }

    /**
     * 流水接口
     */
    public function getPayLogType() {
        return [
            'system' => [
                'name' => '余额',
                'url' => 'member/PayLog/index',
            ],
        ];
    }

    public function getFinancialType() {
        return [
            'member' => [
                'name' => '用户',
                'list' => [
                    'member_account' => [
                        'name' => '资金',
                        'list' => [
                            'operate' => [
                                'name' => '人工处理'
                            ],
                            'cash' => [
                                'name' => '提现'
                            ],
                            'recharge' => [
                                'name' => '充值'
                            ]
                        ]
                    ],
                ]
            ],
            'points' => [
                'name' => '积分',
                'list' => [
                    'points_account' => [
                        'name' => '账户',
                        'list' => [
                            'operate' => [
                                'name' => '人工处理'
                            ],
                            'cash' => [
                                'name' => '提现'
                            ]
                        ]
                    ],
                ]

            ]
        ];
    }

    public function getMemberType($userInfo) {
        return [
            [
                'tpl' => 'member-header',
                'sort' => 0,
                'data' => [
                    'avatar' => $userInfo['avatar'],
                    'show_name' => $userInfo['show_name'],
                    'grade_name' => $userInfo['grade_name'],
                    'money' => $userInfo['money'],
                    'point' => $userInfo['point'],
                    'background' => ''
                ],
            ],
            [
                'tpl' => 'text',
                'sort' => 10,
                'data' => [
                    'content' => '会员信息',
                ],
            ],
            [
                'tpl' => 'icon',
                'sort' => 11,
                'data' => (array)[
                    [
                        'icon' => 'daifukuan',
                        'color' => '#ff8b5a',
                        'text' => '我的钱包',
                        'url' => '/pages/wallet/index',
                    ],
                    [
                        'icon' => 'genrenziliao',
                        'color' => '#fbce44',
                        'text' => '个人资料',
                        'url' => '/pages/member/info',
                    ],
                    [
                        'icon' => 'shouhuodizhi',
                        'color' => '#ff5d59',
                        'text' => '收货地址',
                        'url' => '/pages/address/index',
                    ],
                    [
                        'icon' => 'youhuijuan',
                        'color' => '#01a64a',
                        'text' => '优惠券',
                        'url' => '/pages/coupon/member',
                    ],
                ],
            ],
        ];
    }

}

