<?php

/**
 * 订单发货管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderDeliveryModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'delivery_id',
    ];

    protected function base($where) {
        return $this->table('order_delivery(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(['A.*', 'B.order_no'])
            ->where((array)$where);
    }

    /**
     * 获取分类树
     * @return array
     */
    public function loadList($where = [], $limit = 0, $order = 'A.create_time desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if(empty($list)){
            return [];
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $info['delivery_log'] = unserialize($info['delivery_log']);
            $info['api_data'] = json_decode($info['api_data'], true);
        }
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.delivery_id'] = $id;
        return $this->getWhereInfo($where);
    }

}