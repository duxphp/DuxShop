<?php
namespace app\warehouse\model;

/**
 * 供货商订单
 */

use app\system\model\SystemModel;

class WarehouseSupplierOrderModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'supplier_id',
    ];

    protected function base($where) {
        return $this->table('warehouse_supplier_order(A)')
            ->join('warehouse_supplier(B)', ['B.supplier_id', 'A.supplier_id'])
            ->join('order_goods(C)', ['C.id', 'A.order_goods_id'])
            ->join('order(D)', ['D.order_id', 'C.order_id'])
            ->where((array)$where)
            ->group('A.supplier_id,C.sub_id');
    }

    public function loadList($where = [], $limit = 0, $order = 'A.supplier_id desc') {
        $list = $this->base($where)
            ->field([
                'SUM(C.goods_price * C.goods_qty) as total_price',
                'SUM(C.goods_qty) as total_qty',
                'SUM(C.price_total) as total_pay',
                'C.goods_name', 'C.goods_no', 'C.goods_price', 'C.goods_options', 'C.goods_url', 'C.goods_image', 'C.goods_unit',
                'B.name(supplier_name)', 'B.tel(supplier_tel)',
            ])
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['goods_options'] = unserialize($vo['goods_options']);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['goods_options'] = unserialize($info['goods_options']);
            $info['status_data'] = $info;
        }
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.supplier_id'] = $id;
        return $this->getWhereInfo($where);
    }

}