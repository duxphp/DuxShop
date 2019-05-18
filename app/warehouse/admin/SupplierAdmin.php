<?php

/**
 * 供货商管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class SupplierAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseSupplier';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '供货商管理',
                'description' => '管理商城商品供货商',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexOrder() {
        return 'supplier_id desc';
    }


}