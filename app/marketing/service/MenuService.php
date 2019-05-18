<?php

namespace app\marketing\service;
/**
 * 菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return [
            'marketing' => [
                'name' => '营销',
                'icon' => 'trophy',
                'order' => 3,
                'menu' => [
                    [
                        'name' => '优惠券',
                        'order' => 10,
                        'menu' => [
                            [
                                'name' => '优惠券管理',
                                'url' => url('marketing/Coupon/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '优惠券分类',
                                'url' => url('marketing/CouponClass/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '领取记录',
                                'url' => url('marketing/CouponLog/index'),
                                'order' => 2,
                            ],
                        ],
                    ],
                    [
                        'name' => '优惠券活动',
                        'order' => 11,
                        'menu' => [
                            [
                                'name' => '新人赠券',
                                'url' => url('marketing/CouponGift/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '老带新券',
                                'url' => url('marketing/CouponRec/index'),
                                'order' => 0,
                            ],
                        ]
                    ]
                ],
            ],
        ];
    }
}

