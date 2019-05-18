<?php

namespace app\mall\service;
/**
 * 系统菜单接口
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
                        'name' => '商品',
                        'order' => 2,
                        'menu' => [
                            [
                                'name' => '销售排行',
                                'url' => url('mall/SellRanking/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '销售明细',
                                'url' => url('mall/SellList/index'),
                                'order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            'shop' => [
                'menu' => [
                    [
                        'name' => '普通商品',
                        'order' => 0,
                        'menu' => [
                            [
                                'name' => '商品管理',
                                'url' => url('mall/Content/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '商品分类',
                                'icon' => 'code-fork',
                                'url' => url('mall/Class/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '评价管理',
                                'url' => url('mall/Comment/index'),
                                'order' => 2,
                            ],
                        ],
                    ],
                ],
            ],
            'order' => [
                'menu' => [
                    [
                        'name' => '商品',
                        'order' => 0,
                        'menu' => [
                            [
                                'name' => '订单管理',
                                'url' => url('mall/Order/index'),
                                'order' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}

