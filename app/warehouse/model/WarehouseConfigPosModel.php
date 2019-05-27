<?php

/**
 * 小票设置
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehouseConfigPosModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'pos_id',
        'validate' => [
            'type' => [
                'required' => ['', '类型参数获取不正确!', 'must', 'all'],
            ],
        ],
    ];

    public function loadList() {
        $list = parent::loadList();
        $typeList = $this->typeList();
        foreach ($list as $key => $vo) {
            $list[$key]['name'] = $typeList[$vo['type']]['name'];
        }
        return $list;
    }

    /**
     * 获取配置
     * @param $type
     * @return mixed
     */
    public function getConfig($type) {
        $where = array();
        $where['type'] = $type;
        $info = $this->getWhereInfo($where);
        return json_decode($info['setting'], true);
    }

    /**
     * 获取服务接口
     */
    public function typeList() {
        $list = hook('service', 'Type', 'Pos');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

    /**
     * 属性信息
     * @param $type
     * @return mixed
     */
    public function typeInfo($type) {
        $list = $this->typeList();
        return $list[$type];

    }


}