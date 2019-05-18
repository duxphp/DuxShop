<?php

/**
 * 订单列表
 */
namespace app\warehouse\api;

class WarningApi extends \app\member\api\MemberApi {

    protected $_middle = 'warehouse/Warning';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $this->data['type'] ? $this->data['type'] : 0,
            'start_time' => $this->data['start_time'],
            'stop_time' => $this->data['stop_time'],
        ])->warning()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function setting() {
        target($this->_middle, 'middle')->setParams([
            'id' => $this->data['user_id'],
            'day' => $this->data['day'] ? $this->data['day'] : 3,
        ])->setting()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}