<?php

/**
 * 配送员管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class MarkiAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseMarki';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '配送员管理',
                'description' => '管理商城配送员',
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
        return 'marki_id asc';
    }

    public function _editAssign($info) {
        return array(
            'userInfo' => $info['user_id'] ? target('member/MemberUser')->getWhereInfo(['A.user_id' => $info['user_id']]) : [],
        );
    }

}