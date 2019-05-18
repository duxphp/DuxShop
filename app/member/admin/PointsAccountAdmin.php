<?php

/**
 * 积分账户管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PointsAccountAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PointsAccount';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '积分账户',
                'description' => '查看操作用户积分',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.tel'
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
            target($this->_model)->beginTransaction();
            $status = target('member/Points', 'service')->account([
                'user_id' => $info['user_id'],
                'money' => $post['money'],
                'type' => $post['type'],
            ]);
            if(!$status) {
                $this->error(target('member/Points', 'service')->getError());
            }
            $accountStatus = target('statis/Finance', 'service')->account([
                'user_id' => $info['user_id'],
                'species' => 'points_account',
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
            $this->success('账户积分处理成功!', url('index'));
        }
    }
}