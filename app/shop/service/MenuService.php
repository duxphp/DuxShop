<?php

namespace app\shop\service;
/**
 * 系统菜单接口
 */
class MenuService {
    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return [
            'shop' => [
                'name' => '商品',
                'icon' => 'shopping-bag',
                'order' => 2,
                'menu' => [
                    [
                        'name' => '品牌',
                        'order' => 100,
                        'menu' => [
                            [
                                'name' => '品牌管理',
                                'url' => url('shop/Brand/index'),
                                'order' => 0,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}

