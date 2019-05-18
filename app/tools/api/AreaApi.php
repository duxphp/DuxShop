<?php

/**
 * 地区库
 */

namespace app\tools\api;

use \app\base\api\BaseApi;

class AreaApi extends BaseApi {

    protected $_middle = 'tools/Area';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'province' => request('', 'province'),
            'city' => request('', 'city'),
            'region' => request('', 'region')
        ])->index()->export(function ($data) {
            $data['province'] = objectToList($data['province']);
            $data['city'] = objectToList($data['city']);
            $data['region'] = objectToList($data['region']);
            $data['street'] = objectToList($data['street']);
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });


    }

}