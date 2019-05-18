<?php

/**
 * 商品分类
 */

namespace app\mall\api;

use \app\base\api\BaseApi;

class ClassApi extends BaseApi {

    protected $_middle = 'mall/Category';

    /**
     * 商品分类列表
     * @method Get
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"class_id":1,"parent_id":0,"name":"分类名称","subname":"副分类名称","image":"","keyword":"","description":"","sort":1}]}
     * @field integer $class_id 分类ID
     * @field integer $parent_id 上级ID
     * @field string $name 分类名称
     * @field string $subname 副分类名称
     * @field string $image 分类图片
     * @field string $keyword 关键词
     * @field string $description 分类描述
     * @field integer $sort 排序
     */
    public function index() {
        target($this->_middle, 'middle')->setParams()->treeList()->export(function ($data) {
            $this->success('ok', $data['treeList']);
        });
    }

}
