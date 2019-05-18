<?php

/**
 * 系统基础
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class MemberApi extends BaseApi {

    protected $userId = 0;
    protected $userInfo = [];

    public function __construct() {
        parent::__construct();
        $this->checkLogin();
    }

    /**
     * 检测登录
     */
    private function checkLogin() {
        if (!target('member/MemberUser')->checkUser($_SERVER['HTTP_AUTHUID'], $_SERVER['HTTP_AUTHTOKEN'])) {
            $this->error(target('Member/MemberUser')->getError(), 401);
        }
        $this->userId = $_SERVER['HTTP_AUTHUID'];
        $this->userInfo = target('Member/MemberUser')->getUser($this->userId);
        define('USER_ID', $this->userId);
    }



}
