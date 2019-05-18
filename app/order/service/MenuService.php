<?php

namespace app\order\service;
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
                        'name' => '订单',
                        'order' => 1,
                        'menu' => [
                            [
                                'name' => '销售统计',
                                'url' => url('order/OrderStatis/index'),
                                'order' => 2,
                            ],
                        ],
                    ],
                ],
            ],
            'order' => [
                'name' => '订单',
                'icon' => 'truck',
                'order' => 10,
                'menu' => [
                    [
                        'name' => '设置',
                        'order' => 98,
                        'menu' => [
                            [
                                'name' => '订单设置',
                                'url' => url('order/Config/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '物流列表',
                                'url' => url('order/ConfigExpress/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '运费模板',
                                'url' => url('order/ConfigDelivery/index'),
                                'order' => 2,
                            ],
                            [
                                'name' => '物流接口',
                                'url' => url('order/ConfigWaybill/index'),
                                'order' => 3,
                            ],
                            [
                                'name' => '运单打印机',
                                'url' => url('order/ConfigPrinter/index'),
                                'order' => 4,
                            ],
                        ],
                    ],
                    [
                        'name' => '售后',
                        'order' => 99,
                        'menu' => [
                            [
                                'name' => '退款管理',
                                'url' => url('order/Refund/index'),
                                'order' => 0,
                            ],
                        ],
                    ],
                    [
                        'name' => '处理',
                        'order' => 100,
                        'menu' => [
                            [
                                'name' => '配货管理',
                                'url' => url('order/Parcel/index'),
                                'order' => 1,
                            ],
                            [
                                'name' => '运单管理',
                                'url' => url('order/Delivery/index'),
                                'order' => 2,
                            ],
                            [
                                'name' => '收款管理',
                                'url' => url('order/Receipt/index'),
                                'order' => 3,
                            ],
                            [
                                'name' => '自提点管理',
                                'url' => url('order/Take/index'),
                                'order' => 4,
                            ],
                        ],
                    ],
                    [
                        'name' => '发票',
                        'order' => 101,
                        'menu' => [
                            [
                                'name' => '发票管理',
                                'url' => url('order/Invoice/index'),
                                'order' => 0,
                            ],
                            [
                                'name' => '发票分类',
                                'url' => url('order/InvoiceClass/index'),
                                'order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

