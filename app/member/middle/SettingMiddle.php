<?php

/**
 * 用户设置
 */

namespace app\member\middle;


class SettingMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];


    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();

        return $this->config;
    }

    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => $name,
                'url' => $url
            ]
        ]);
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function putInfo() {
        $data = $this->params;
        if (empty($data)) {
            return $this->stop('账号资料获取失败!');
        }
        foreach ($data as $key => $vo) {
            $data[$key] = html_clear($vo);
        }
        $userId = intval($data['user_id']);
        $avatar = $data['avatar'];

        $data = [
            'user_id' => $userId,
            'nickname' => $data['nickname'],
            'province' => $data['province'],
            'city' => $data['city'],
            'region' => $data['region'],
			'birthday' => $data['birthday'],
            'email' => $data['email'],
            'tel' => $data['tel'],
            'sex' => intval($data['sex']) ? 1 : 0,
        ];

        if (empty($data['nickname'])) {
            return $this->stop('请填写用户昵称!');
        }
        $data['nickname'] = \dux\lib\Str::symbolClear($data['nickname']);
        if (empty($data['nickname'])) {
            return $this->stop('昵称填写不正确，请勿使用符号!');
        }
        if (empty($data['user_id'])) {
            return $this->stop('用户获取错误!');
        };
        $config = $this->getConfig();
        if (in_array($data['nickname'], explode('|', $config['reg_ban_name']))) {
            return $this->stop('当前昵称被禁止使用!');
        }

        if (!target('member/MemberUser')->edit($data)) {
            return $this->stop(target('member/MemberUser')->getError());
        }
        if($avatar) {
            if(strpos($avatar, '/upload/avatar/') === false) {
                if (!target('member/MemberUser')->avatarUser($userId,  $avatar)) {
                    return $this->stop(target('member/MemberUser')->getError());
                }
            }
        }
        return $this->run([], '修改资料成功!');
    }

	protected function putUsername() {
		$userId = $this->params['user_id'];
		$username = $this->params['username'];
		$type = $this->params['type'];
		$userInfo = $this->params['user_info'];
        $data = [];
        $receive = $this->getReceive();
        if (empty($receive)) {
            $receive = $this->params['username'];
        }
        if(empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }
		if($type) {
		    if(empty($receive)) {
		        return $this->stop('请输入邮箱账号!');
            }
			$data['email'] = $username;
            if(!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                return $this->stop('邮箱格式输入错误!');
            }
            $count = target('member/MemberUser')->where(['email' => $username])->count();
            if ($count) {
                return $this->stop('该邮箱已被使用!');
            }
        }else{
            if(empty($receive)) {
                return $this->stop('请输入手机号码!');
            }
			$data['tel'] = $username;
			if(!preg_match('/(^1[3|4|5|6|7|8|9][0-9]{9}$)/', $username)) {
                return $this->stop('手机号码输入错误!');
            }
            $count = target('member/MemberUser')->where(['tel' => $username])->count();
            if ($count) {
                return $this->stop('该手机已被使用!');
            }
		}
        target('member/MemberUser')->beginTransaction();
        if (!target('member/Member', 'service')->checkVerify($receive, $this->params['val_code'], 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }

		$data['user_id'] = $userId;
		if (!target('member/MemberUser')->edit($data)) {
            return $this->stop(target('member/MemberUser')->getError());
        }
		target('member/MemberUser')->commit();
        return $this->run([], '修改帐号成功!');

	}

    protected function putAvatar() {
        $userId = intval($this->params['user_id']);
        return target('member/Upload', 'middle')->setParams([
            'user_id' => $userId,
            'width' => 256,
            'height' => 256
        ])->post()->export(function ($data) use($userId) {
            $file = reset($data);
            if (!target('member/MemberUser')->avatarUser($userId,  $file['file'])) {
                return $this->stop(target('member/MemberUser')->getError());
            }
            return $this->run($data, '头像修改成功!');
        }, function ($message) {
            return $this->stop($message);
        });
    }

    protected function putPassword() {
        $userId = intval($this->params['user_id']);
        $oldPassword = $this->params['old_password'];
        $password = $this->params['password'];
        $password2 = $this->params['password2'];
        $userInfo = target('member/MemberUser')->getInfo($userId);
        if (md5($oldPassword) <> $userInfo['password']) {
            return $this->stop('原始密码输入不正确!');
        }
        if (empty($password)) {
            return $this->stop('请输入新密码!');
        }
        if ($password <> $password2) {
            return $this->stop('两次密码输入不正确!');
        }
        $data = [];
        $data['user_id'] = $userId;
        $data['password'] = md5($password);
        if (!target('member/MemberUser')->edit($data)) {
            return $this->stop('密码修改失败!');
        }
        return $this->run([], '密码修改成功!');
    }

    private function getReceive() {
        $type = intval($this->params['val_type']);
        $userInfo = $this->params['user_info'];
        if (!$type) {
            $receive = $userInfo['tel'];
        } else {
            $receive = $userInfo['email'];
        }
        return $receive;
    }

    protected function putPayPassword() {
        $userId = $this->params['user_id'];
        $password = trim($this->params['password']);
        if(empty($password)) {
            return $this->stop('支付密码不能为空!');
        }
        if(strlen($password) <> 6 || !is_numeric($password)) {
            return $this->stop('支付密码只能为6位纯数字!');
        }
        $receive = $this->getReceive();
        if (empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }
        target('member/PayAccount')->beginTransaction();
        if (!target('member/Member', 'service')->checkVerify($receive, $this->params['val_code'], 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        $data = [];
        $data['user_id'] = $userId;
        $data['password'] = md5($password);
        if (!target('member/PayAccount')->where(['user_id' => $userId])->data($data)->update()) {
            return $this->stop(target('member/PayAccount')->getError());
        }
        target('member/PayAccount')->commit();
        return $this->run([], '修改支付密码成功!');

    }

    protected function getCode() {
        $receive = $this->getReceive();
        if (empty($receive)) {
            $receive = $this->params['receive'];
        }
        if(empty($receive)) {
            if(!$this->params['val_type']) {
                return $this->stop('手机号码未绑定！');
            }else {
                return $this->stop('邮箱账号未绑定！');
            }
        }
        if (!target('member/Member', 'service')->getVerify($receive, '', 0, 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        return $this->run([], '验证码已发送,请注意查收!');
    }
}
