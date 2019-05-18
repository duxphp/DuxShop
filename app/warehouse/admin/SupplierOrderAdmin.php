<?php

/**
 * 供货商订单
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class SupplierOrderAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseSupplierOrder';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '供货商订单',
                'description' => '管理供货商订单信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'status' => 'status',
            'time_type' => 'time_type',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
            'province' => 'B.receive_province',
            'city' => 'B.receive_city',
            'region' => 'B.receive_region',
            'street' => 'B.receive_street',
            'marki_id' => 'A.marki_id',
        ];
    }

    public function _indexWhere($whereMaps) {

        switch ($whereMaps['status']) {
        case 1:
            $whereMaps['receive_status'] = 0;
            break;
        case 2:
            $whereMaps['receive_status'] = 1;
            break;
        }
        unset($whereMaps['status']);

        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time']);
        }

        $field = 'D.order_create_time';

        if ($whereMaps['time_type'] == 1) {
            $field = 'B.order_create_time';
        }

        if ($startTime) {
            $whereMaps[$field . '[>=]'] = $startTime;
        }
        if ($stopTime) {
            $whereMaps[$field . '[<=]'] = $stopTime;
        }

        unset($whereMaps['time_type']);
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);


        $whereMaps['D.order_status'] = 1;
        $whereMaps['OR #status'] = [
            'AND #1' => [
                'D.pay_type' => 1,
                'D.pay_status' => 1
            ],
            'AND #2' => [
                'D.pay_type' => 0
            ]
        ];

        return $whereMaps;
    }

    public function _indexOrder() {
        return 'A.id desc';
    }

    public function _indexAssign($pageMaps, $where) {
        $data = target($this->_model)->table('warehouse_supplier_order(A)')
            ->join('warehouse_supplier(B)', ['B.supplier_id', 'A.supplier_id'])
            ->join('order_goods(C)', ['C.id', 'A.order_goods_id'])
            ->join('order(D)', ['D.order_id', 'C.order_id'])
            ->where((array)$where)
            ->field([
                'SUM(C.goods_qty) as total_qty',
                'SUM(C.goods_price) as total_price',
                'SUM(C.price_total) as total_pay',
            ])
            ->select();
        $info = $data[0];
        return [
            'supplierList' => target('warehouse/WarehouseSupplier')->loadList(['status' => 1]),
            'info' => $info
        ];
    }

}