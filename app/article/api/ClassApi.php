<?php

namespace app\article\api;

/**
 * 文章分类
 */

use \app\base\api\BaseApi;

class ClassApi extends BaseApi {

    protected $_middle = 'article/Category';

    /**
     * 文章分类列表
     * @method GET
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"class_id": 1, "parent_id": 0, "name": "分类名称", "subname": "副分类名称", "image": "", "keyword": "", "description": "", "sort": 0}]}
     * @field integer $class_id 分类ID 
     * @field integer $parent_id 上级ID
     * @field string $name 分类名称 
     * @field string $subname 副分类名称 
     * @field string $image 栏目图 
     * @field string $keyword 关键词 
     * @field string $description 描述 
     * @field integer $sort 顺序 
     */
    public function index() {
        target($this->_middle, 'middle')->treeList()->export(function ($data) {
            $this->success('ok', $data['treeList']);
        });

    }

    

}