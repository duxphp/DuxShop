<?php

/**
 * 模板管理
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehousePosLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    protected function base($where) {
        return $this->table('warehouse_pos_log(A)')
            ->join('warehouse_pos_driver(B)', ['B.driver_id', 'A.driver_id'], '>')
            ->join('warehouse_config_pos(C)', ['C.pos_id', 'B.pos_id'], '>')
            ->field(['A.*',
                'B.name(driver_name)',
                'C.type',
            ])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        $typeList = target('warehouse/WarehouseConfigPos')->typeList();
        foreach ($list as $key => $vo) {
            $list[$key]['type_name'] = $typeList[$vo['type']]['name'];
        }
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        return $info;
    }

    public function getInfo($id) {
        return $this->getWhereInfo([
            'A.log_id' => $id
        ]);
    }

}