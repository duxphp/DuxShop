<?php

namespace app\warehouse\service;
/**
 * 菜单接口
 */
class MenuService {

    /**
     * 获取菜单结构
     */
    public function getSystemMenu() {
        return array(
            'warehouse' => array(
                'name' => '仓库',
                'icon' => 'cubes',
                'order' => 11,
                'menu' => array(
                    array(
                        'name' => '配送',
                        'order' => 0,
                        'menu' => array(
                            array(
                                'name' => '配送员管理',
                                'url' => url('warehouse/Marki/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '配送订单',
                                'url' => url('warehouse/MarkiDelivery/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '下单预警',
                                'url' => url('warehouse/MarkiWarning/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '取消记录',
                                'url' => url('warehouse/MarkiWarningLog/index'),
                                'order' => 3
                            ),
                        )
                    ),
                    array(
                        'name' => '供货',
                        'order' => 1,
                        'menu' => array(
                            array(
                                'name' => '供货商管理',
                                'url' => url('warehouse/Supplier/index'),
                                'order' => 0
                            ),
                            array(
                                'name' => '供货订单',
                                'url' => url('warehouse/SupplierOrder/index'),
                                'order' => 1
                            ),
                        )
                    ),
                ),
            ),

        );
    }
}

