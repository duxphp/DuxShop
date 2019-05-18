<?php

/**
 * 取消记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class MarkiWarningLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseWarning';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '取消记录',
                'description' => '预警取消记录',
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
        ];
    }

    public function _indexOrder() {
        return 'warning_id asc';
    }

    public function _indexCount($where) {
        return target('WarehouseWarning')
        ->table('warehouse_warning(A)')
        ->join('member_user(B)', ['B.user_id', 'A.user_id'])
        ->join('order_address(C)', ['C.user_id', 'A.user_id'], '>')
        ->where($where)
        ->group('A.user_id')
        ->count();
    }

    public function _indexData($where, $limit, $order) {
        return target('WarehouseWarning')
        ->table('warehouse_warning(A)')
        ->join('member_user(B)', ['B.user_id', 'A.user_id'])
        ->join('order_address(C)', ['C.user_id', 'A.user_id'], '>')
        ->field(['C.*', 'B.user_id', 'B.nickname', 'B.avatar', 'A.start_time', 'A.stop_time', 'A.remark'])
        ->where($where)
        ->group('A.user_id')
        ->limit($limit)
        ->order('A.warning_id desc')
        ->select();
    }


}