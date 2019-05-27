<?php

/**
 * 模板管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class PosTplAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehousePosTpl';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '模板管理',
                'description' => '小票打印模板管理',
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
        return 'tpl_id desc';
    }

    public function _addAssign() {
        return array(
            'posList' => target('warehouse/WarehouseConfigPos')->loadlist(),
        );
    }

    public function _editAssign($info) {
        return array(
            'posList' => target('warehouse/WarehouseConfigPos')->loadlist(),
        );
    }

}