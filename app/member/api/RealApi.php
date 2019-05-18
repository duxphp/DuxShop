<?php

/**
 * 实名认证
 */

namespace app\member\api;


class RealApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Real';

    /**
     * 实名认证
     * @method GET
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"info":{}, "config":{"val_status": 1, "card_status": 1}}]}
     * 
     */
    public function index() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id']
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }

    public function bind() {
        target($this->_middle, 'middle')->setParams(
            array_merge($this->data, [
                'user_info' => $this->userInfo,
                'val_type' => $this->data['val_type'],
                'user_id' => $this->userInfo['user_id']
            ]))->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->error($message, $code, $url);
        });
    }


}
