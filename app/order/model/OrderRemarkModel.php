<?php

/**
 * 订单备注管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderRemarkModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'remark_id',
    ];

    protected function base($where) {
        $base = $this->table('order_remark(A)')
            ->join('system_user(B)', ['B.user_id', 'A.user_id']);
        $field = ['A.*', 'B.nickname', 'B.username'];
        return $base->field($field)->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.remark_id desc')
            ->select();
        return $list;
    }

}