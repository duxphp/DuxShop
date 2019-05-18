<?php

/**
 * 订单发货管理
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehouseMarkiDeliveryModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'delivery_id',
    ];

    protected function base($where) {
        return $this->table('warehouse_marki_delivery(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->join('warehouse_marki(C)', ['C.marki_id', 'A.marki_id'])
            ->field(['A.*', 'B.order_app', 'B.order_no', 'B.order_create_time', 'B.order_title', 'B.order_image', 'B.receive_name', 'B.receive_tel', 'B.receive_province', 'B.receive_city', 'B.receive_region', 'B.receive_street', 'B.receive_address', 'B.receive_zip', 'C.name(marki_name)', 'C.tel(marki_tel)'])
            ->where((array) $where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.create_time desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if (empty($list)) {
            return [];
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.delivery_id'] = $id;
        return $this->getWhereInfo($where);
    }

}