<?php

/**
 * 会员设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;


class MemberConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '会员设置',
                'description' => '设置会员中心信息',
            ],
        ];
    }

    public function menu() {
        $menu = [
            [
                'name' => '基础设置',
                'url' => url('index'),
                'cur' => ACTION_NAME == 'index' ? 1 : 0,
            ],
            [
                'name' => '验证码设置',
                'url' => url('verify'),
                'cur' => ACTION_NAME == 'verify' ? 1 : 0,
            ],
            [
                'name' => '提现设置',
                'url' => url('cash'),
                'cur' => ACTION_NAME == 'cash' ? 1 : 0,
            ],
        ];
        return $menu;
    }

    /**
     * 注册设置
     */
    public function index() {
        if (!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $this->assign('info', $info);
            $this->assign('hookMenu', $this->menu());
            $this->assign('roleList', target('member/MemberRole')->loadList());
            $this->systemDisplay();
        } else {
            if (target('MemberConfig')->saveInfo()) {
                $this->success('会员配置成功！');
            } else {
                $this->error('会员配置失败');
            }
        }
    }

    /**
     * 提现设置
     */
    public function cash() {
        if (!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $info['clear_type'] = (array)unserialize($info['clear_type']);
            $info['clear_audit'] = (array)unserialize($info['clear_audit']);
            $this->assign('typeList', target('member/PayCash')->type());
            $this->assign('info', $info);
            $this->assign('hookMenu', $this->menu());
            $this->systemDisplay();
        } else {
            if (target('MemberConfig')->saveInfo()) {
                $this->success('会员配置成功！');
            } else {
                $this->error('会员配置失败');
            }
        }
    }

    /**
     * 验证码设置
     */
    public function verify() {
        if (!isPost()) {
            $info = target('MemberConfig')->getConfig();
            $classSend = target('tools/ToolsSendConfig')->classList();
            $tplData = ['验证码', '有效期'];

            $this->assign('info', $info);
            $this->assign('classSend', $classSend);
            $this->assign('tplData', $tplData);
            $this->assign('hookMenu', $this->menu());
            $this->systemDisplay();
        } else {
            if (target('MemberConfig')->saveInfo()) {
                $this->success('会员配置成功！');
            } else {
                $this->error('会员配置失败');
            }
        }
    }

}