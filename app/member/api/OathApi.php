<?php

/**
 * oath接口
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class OathApi extends BaseApi {

    /**
     * 登录接口
     */
    public function login() {
        $data = target('member/Member', 'service')->loginOauthUser($this->data['type'], $this->data['id'], $this->data['id']);
        if(!$data) {
            $this->success(target('member/Member', 'service')->getError(), $data);
        }
        $this->success('ok', $data);
    }

    /**
     * 登录注册接口
     */
    public function reg() {
        $data = target('member/Member', 'service')->oauthUser($this->data['type'], $this->data['id'], $this->data['id'], $this->data['nickname'], $this->data['avatar']);
        if(!$data) {
            $this->error(target('member/Member', 'service')->getError());
        }
        $this->success('ok', $data);
    }

}
