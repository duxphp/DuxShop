<?php

/**
 * 模板管理
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehousePosTplModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'tpl_id',
    ];

    protected function base($where) {
        return $this->table('warehouse_pos_tpl(A)')
            ->join('warehouse_config_pos(B)', ['B.pos_id', 'A.pos_id'], '>')
            ->field(['A.*',
                'B.type',
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
            'A.tpl_id' => $id
        ]);
    }

}