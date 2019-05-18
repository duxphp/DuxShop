<?php

/**
 * 分类管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class ClassAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MallClass';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商品分类',
                'description' => '商品分类管理',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name',
        ];
    }

    protected function _indexWhere($whereMaps) {
        return $whereMaps;
    }

    public function _indexPage() {
        return 10000;
    }

    public function _indexData($where, $limit, $order) {
        return target($this->_model)->loadTreeList($where, $limit, $order);
    }

    protected function _addAssign() {
        return [
            'classList' => target('Mall/MallClass')->loadTreeList(),
        ];
    }

    protected function _editAssign($info) {
        return [
            'classList' => target('Mall/MallClass')->loadTreeList(),
        ];
    }

    protected function _delBefore($id) {
        $cat = target($this->_model)->loadTreeList([], 0, '', $id);
        if ($cat) {
            $this->error('清先删除子分类!');
        }
        $count = target('Mall/Mall')->countList([
            'B.class_id' => $id,
        ]);
        if ($count > 0) {
            $this->error('请先删除该分类下的内容！');
        }
    }

}