<?php
namespace app\warehouse\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return array(
            'Marki' => array(
                'name' => '配送员管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                ),
            ),
            'MarkiDelivery' => array(
                'name' => '配送订单',
                'auth' => array(
                    'index' => '列表',
                ),
            ),
            'Supplier' => array(
                'name' => '供货商管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                ),
            ),
            'SupplierOrder' => array(
                'name' => '供货订单',
                'auth' => array(
                    'index' => '列表',
                ),
            ),
            'PosLog' => array(
                'name' => '打印记录',
                'auth' => array(
                    'index' => '列表',
                    'del' => '删除',
                ),
            ),
            'ConfigPos' => array(
                'name' => '打印接口',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                ),
            ),
            'PosDriver' => array(
                'name' => '设备管理',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                ),
            ),
            'PosTpl' => array(
                'name' => '打印模板',
                'auth' => array(
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'del' => '删除',
                ),
            ),
        );
    }

}
