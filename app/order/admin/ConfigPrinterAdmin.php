<?php

/**
 * 打印机配置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class ConfigPrinterAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderConfigPrinter';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '打印机设置',
                'description' => '管理快递单打印机',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexOrder() {
        return 'printer_id asc';
    }


}