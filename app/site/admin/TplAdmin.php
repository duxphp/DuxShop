<?php

/**
 * 模板管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\site\admin;

class TplAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'SiteTpl';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '页面管理',
                'description' => '管理编辑系统模板页面',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'status' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'title',
        ];
    }

    public function _indexOrder() {
        return 'tpl_id asc';
    }


    protected function _addAssign() {
        $elementType = target('SiteTpl')->elementType();
        $defaultData = [];
        foreach ($elementType as $key => $vo) {
            $defaultData[$key] = array_merge(['tpl' => $key], $vo['default']);
        }
        return array(
            'elementType' => $elementType,
            'defaultData' => $defaultData,
            'data' => (array)[]
        );
    }

    protected function _editAssign($info) {
        $elementType = target('SiteTpl')->elementType();
        $defaultData = [];
        foreach ($elementType as $key => $vo) {
            $defaultData[$key] = array_merge(['tpl' => $key], $vo['default']);
        }
        $data = (array)json_decode($info['content'], true);
        return array(
            'elementType' => $elementType,
            'defaultData' => $defaultData,
            'data' => $data
        );
    }
    public function editorParsing() {
        $this->success($_POST);
    }

}