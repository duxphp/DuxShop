<?php

/**
 * 供货商
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehouseSupplierModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'supplier_id',
    ];

}