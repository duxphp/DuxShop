<?php

/**
 * 打印机配置
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderConfigPrinterModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'printer_id',
    ];

}