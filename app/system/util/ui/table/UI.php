<?php

/**
 * UI构建器
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\system\util\ui\table;

class UI {

    private $tpl = 'app/system/util/ui/table/layout';

    private $var = [
        'columns' => [],
        'tableList' => [],
    ];

    public function __construct() {

    }


    public function addColumn($name = '', $title = '', $type = '', $default = '', $param = '', $class = '') {
        $data = [
            'name' => $name,
            'title' => $title,
            'type' => $type,
            'default' => $default,
            'param' => $param,
            'class' => $class,
        ];
        $this->var['columns'][] = $data;
    }

    public function setData($data) {
        $this->var['tableList'] = $data;
    }

    public function compile() {
        //处理表格
        $html = '';
        if($this->var['columns']) {
            foreach ($this->var['tableList'] as $vo) {

            }
        }
    }

    public function export() {

        return [
            'tpl' => $this->tpl,
            'data' => $this->compile(),
        ];
    }

}