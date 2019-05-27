<?php
namespace app\warehouse\api;

/**
 * 配送订单
 */

class MarkiOrderApi extends \app\member\api\MemberApi {

    protected $_middle = 'warehouse/MarkiOrder';

    public function list() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $this->data['type'] ? $this->data['type'] : 0,
        ])->data()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function bind() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'code' => $this->data['code'],
        ])->bind()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}