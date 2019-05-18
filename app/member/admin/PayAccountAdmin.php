<?php

/**
 * 资金账户管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayAccountAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayAccount';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '资金账户',
                'description' => '查看操作用户资金',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.tel,B.email,B.nickname'
        ];
    }

    public function _indexOrder() {
        return 'account_id desc';
    }

    public function operate() {
        if (!isPost()) {
            $id = request('get', 'id');
            if (empty($id)) {
                $this->error('参数传递错误!');
            }
            $info = target($this->_model)->getInfo($id);
            if (empty($info)) {
                $this->error('暂无该记录!');
            }
            $this->assign('info', $info);
            $this->systemDisplay();
        } else {
            $post = request('post');
            if (empty($post['remark'])) {
                $this->error('请输入操作理由!');
            }
            $id = request('post', 'id');
            if (empty($id)) {
                $this->error('参数传递错误!');
            }
            $info = target($this->_model)->getInfo($id);
            if (empty($info)) {
                $this->error('暂无该记录!');
            }
            $status = target('member/Finance', 'service')->account([
                'user_id' => $info['user_id'],
                'money' => $post['money'],
                'type' => $post['type'],
            ]);
            if(!$status) {
                $this->error(target('member/Finance', 'service')->getError());
            }
            $accountStatus = target('statis/Finance', 'service')->account([
                'user_id' => $info['user_id'],
                'species' => 'member_account',
                'sub_species' => 'operate',
                'money' => $post['money'],
                'type' => $post['type'],
                'title' => '人工操作',
                'remark' => $post['remark'],
            ]);
            if(!$accountStatus) {
                $this->error(target('statis/Finance', 'service')->getError());
            }
            target($this->_model)->commit();
            $this->success('账户余额处理成功!', url('index'));
        }
    }

}