<?php

/**
 * 货品管理
 */
namespace app\mall\model;

use app\system\model\SystemModel;

class MallProductsModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'products_id'
    ];

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->table('mall_products(A)')
            ->join('mall(B)', ['B.mall_id', 'A.mall_id'])
            ->field(['B.*', 'A.*'])
            ->where($where)->order('products_id asc')->limit($limit)->select();
        foreach ($list as $key => $vo) {
            $list[$key]['spec_data'] = unserialize($vo['spec_data']);
        }
        return $list;
    }

    public function getMallInfo($where) {
       $info = $this->table('mall_products(A)')
            ->join('mall(B)', ['B.mall_id', 'A.mall_id'])
            ->field(['B.*', 'A.*'])
            ->where($where)->find();

       if(empty($info)) {
           return [];
       }
        $info['spec_data'] = unserialize($info['spec_data']);
        return $info;
    }

    public function getWhereInfo($where, $lock = false) {
        return $this->where($where)->lock($lock)->find();
    }

    public function getInfo($id, $lock = false) {
        return $this->getWhereInfo([
            'products_id' => $id
        ], $lock);
    }


}