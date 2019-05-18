<?php

/**
 * 首页信息
 */

namespace app\index\api;

class IndexApi extends \app\member\api\MemberApi {

    protected $_middle = 'index/Index';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'label' => 'index',
            'user_id' => $this->userInfo['user_id']
        ])->data()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function diy() {
        target($this->_middle, 'middle')->setParams([
            'label' => $this->data['label'],
            'user_id' => $this->userInfo['user_id']
        ])->data()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}