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
                    array(
                        'name' => '小票打印',
                        'order' => 2,
                        'menu' => array(
                            /*array(
                                'name' => '打印记录',
                                'url' => url('warehouse/PosLog/index'),
                                'order' => 0
                            ),*/
                            array(
                                'name' => '打印接口',
                                'url' => url('warehouse/ConfigPos/index'),
                                'order' => 1
                            ),
                            array(
                                'name' => '设备管理',
                                'url' => url('warehouse/PosDriver/index'),
                                'order' => 2
                            ),
                            array(
                                'name' => '打印模板',
                                'url' => url('warehouse/PosTpl/index'),
                                'order' => 3
                            ),
                        )
                    ),
                ),
            ),

        );
    }
}

