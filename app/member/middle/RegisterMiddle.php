<?php

/**
 * 基础控制器
 */

namespace app\member\middle;


class RegisterMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('注册');
        $this->setName('注册');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '注册',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();

        return $this->config;
    }

    protected function data() {
        $this->config = $this->getConfig();
        $hookField = [];
        $hookList = run('service', 'member', 'regField');

        foreach ($hookList as $app => $vo) {
            if (!empty($vo)) {
                $hookField = array_merge($hookField, $vo);
            }
        }
        return $this->run([
            'hookField' => $hookField,
            'config' => [
                'reg_status' => $this->config['reg_status'],
                'reg_type' => $this->config['reg_type'],
                'reg_agreement' => html_out($this->config['reg_agreement']),
                'verify_image' => $this->config['verify_image'],
                'verify_status' => $this->config['verify_status']
            ]
        ]);
    }

    protected function post() {
        $this->config = $this->getConfig();
        $model = target('member/MemberUser');
        $model->beginTransaction();
        $username = $this->params['username'];
        $password = $this->params['password'];
        $code = $this->params['code'];
        if (empty($username) || empty($password)) {
            return $this->stop('用户名或密码未填写！');
        }
        $agreement = intval($this->params['agreement']);
        if (!$agreement) {
            return $this->stop('请先阅读注册协议!');
        }
        target('member/MemberUser')->beginTransaction();
        $loginData = target('member/Member', 'service')->regUser($username, $password, $code);
        if (!$loginData) {
            target('member/MemberUser')->rollBack();
            return $this->stop(target('member/Member', 'service')->getError());
        }
        $model->commit();

        $loginData = target('member/Member', 'service')->loginUser($username, $password);
        if (!$loginData) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        return $this->run($loginData);
    }

    protected function getCode() {
        $config = $this->getConfig();
        if($config['verify_image']) {
            $imgCode = new \dux\lib\Vcode();
            if (!$imgCode->check($this->params['img_code'], $this->params['img_token'], $this->params['img_time'])) {
                return $this->stop('图片验证码输入不正确!');
            }
        }
        if (!target('member/Member', 'service')->getVerify($this->params['username'])) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        return $this->run();
    }

}