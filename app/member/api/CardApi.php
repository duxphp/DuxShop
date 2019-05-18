<?php

/**
 * 银行卡管理
 */

namespace app\member\api;

class CardApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Card';

    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
        ])->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

    public function data() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
        ])->realInfo()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'card_id' => $this->data['id']
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

    public function bind() {
        $data = $this->data;
        target($this->_middle, 'middle')->setParams(
            array_merge($data, [
                'user_info' => $this->userInfo,
                'val_type' => $data['val_type'],
                'user_id' => $this->userId,
            ]))->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

    public function del() {
        target($this->_middle, 'middle')->setParams([
            'card_id' => $this->data['id'],
            'user_id' => $this->userId,
        ])->del()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

}
