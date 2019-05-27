<?php

namespace app\member\service;
/**
 * 会员处理
 */
class MemberService extends \app\base\service\BaseService {


    /**
     * 登录用户
     * @param string $username
     * @param string $password
     * @param string $type
     * @param string $platform
     * @return bool
     */
    public function loginUser($username = '', $password = '', $type = 'all', $platform = 'web') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入帐号和密码!');
        }
        $config = target('member/MemberConfig')->getConfig();
        if (empty($type)) {
            $type = $config['reg_type'];
        }
        $type = strtolower($type);
        if ($type <> 'tel' && $type <> 'email' && $type <> 'all') {
            return $this->error('登录类型错误!');
        }
        if ($type == 'all') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                $type = 'tel';
            } else {
                $type = 'email';
            }
        }
        if ($type == 'tel') {
            if (!preg_match('/(^1[0-9]{10}$)/', $username)) {
                return $this->error('手机号码错误!');
            }
        }
        if ($type == 'email') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱账号不正确!');
            }
        }
        if (!preg_match('/^[a-zA-Z\d_]{6,18}$/', $password)) {
            return $this->error('密码必须为6-12个字符！');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $type => $username,
        ]);
        if (empty($info)) {
            return $this->error('帐号或者密码输入错误!');
        }
        $password = md5($password);
        if ($info['password'] <> $password) {
            return $this->error('帐号或者密码输入错误!');
        }

        $data = [];
        $data['user_id'] = $info['user_id'];
        $data['login_time'] = time();
        $data['login_ip'] = \dux\lib\Client::getUserIp();
        if (!target('member/MemberUser')->edit($data)) {
            return $this->error('系统繁忙,请稍后登录!');
        }

        $config = \dux\Config::get('dux.use');
        $loginData = [];
        $loginData['uid'] = $info['user_id'];
        $loginData['token'] = sha1($password . $config['safe_key']);

        $hookList = run('service', 'member', 'login', [$info['user_id'], $platform]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('member/MemberUser')->rollBack();
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }
        $loginData['data'] = target('member/MemberUser')->getUser($info['user_id']);

        return $this->success($loginData);
    }


    /**
     * 获取账户类型
     * @param $username
     * @return bool
     */
    public function getUserType($username) {
        if (empty($username)) {
            return $this->error('请输入接收号码!');
        }

        if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
        switch ($username) {
            case 'tel':
                if (!preg_match('/(^1[0-9]{10}$)/', $username)) {
                    return $this->error('手机号码错误!');
                }
                break;
            case 'email' :
                if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                    return $this->error('邮箱账号不正确!');
                }
                break;
        }
        return $this->success($type);
    }

    /**
     * 重置密码
     * @param $username
     * @param $password
     * @param $code
     * @return bool
     */
    public function forgotUser($username = '', $password = '', $code = '') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入新密码!');
        }
        if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
        if ($type == 'tel') {
            if (!preg_match('/(^1[0-9]{10}$)/', $username)) {
                return $this->error('手机号码错误!');
            }
        }
        if ($type == 'email') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱账号不正确!');
            }
        }

        $password = trim($password);
        if (!preg_match('/^[a-zA-Z\d_]{6,18}$/', $password)) {
            return $this->error('密码必须为6-12个字符！');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $type => $username,
        ]);
        if (empty($info)) {
            return $this->error('该用户不存在!');
        }

        $status = $this->checkVerify($username, $code);
        if (!$status) {
            return $status;
        }
        $data = [];
        $data['user_id'] = $info['user_id'];
        $data['password'] = md5($password);
        if (!target('member/MemberUser')->edit($data)) {
            return $this->error('重置密码失败,请稍候再试!');
        }
        return $this->success();
    }

    /**
     * 用户注册
     * @param string $username
     * @param string $password
     * @param string $code
     * @param string $nickname
     * @return bool
     */
    public function regUser($username = '', $password = '', $code = '', $nickname = '') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入帐号和密码!');
        }
        $config = target('member/MemberConfig')->getConfig();
        $type = $config['reg_type'];
        $userType = $this->getUserType($username);
        if (!$userType) {
            return false;
        }
        if ($userType <> $type && $type <> 'all') {
            $this->error('该账号类型禁止注册');
        }

        $password = trim($password);
        if (strlen($password) < 6 || strlen($password) > 18) {
            return $this->error('密码必须为6-12个字符！');
        }

        if ($config['reg_status'] == 0) {
            return $this->error('系统暂时停止开放注册!');
        }
        if (in_array($username, explode('|', $config['reg_ban_name']))) {
            return $this->error('当前账户名被禁止注册!');
        }
        if ($config['reg_ban_ip']) {
            if (in_array(\dux\lib\Client::getUserIp(), explode('|', $config['reg_ban_ip']))) {
                return $this->error('当前IP被禁止注册!');
            }
        }
        if ($config['verify_status']) {
            if (!$this->checkVerify($username, $code)) {
                return false;
            }
        }
        $role = intval($config['reg_role']);
        if (empty($role)) {
            return $this->error('未设置正确角色!');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $userType => $username,
        ]);
        if (!empty($info)) {
            return $this->error('该用户已被注册!');
        }
        $data = [];
        $data[$userType] = $username;
        $data['password'] = $password;
        $data['role_id'] = intval($config['reg_role']);
        $userId = target('member/MemberUser')->saveData('add', $data);

        if (!$userId) {
            return $this->error(target('member/MemberUser')->getError());
        }

        //注册账户

        $hookList = run('service', 'member', 'reg', [$userId, $nickname ? $nickname : $username]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }

        $config = \dux\Config::get('dux.use');
        $loginData = [];
        $loginData['uid'] = $userId;
        $loginData['token'] = sha1(md5($password) . $config['safe_key']);
        $loginData['data'] = target('member/MemberUser')->getUser($userId);

        return $this->success($loginData);
    }

    /**
     * 更新资料
     * @param $uid
     * @param array $data
     * @return bool
     */
    public function updateUser($uid, $data = []) {
        if (empty($data)) {
            return $this->error('账号资料获取失败!');
        }
        foreach ($data as $key => $vo) {
            $data[$key] = html_clear($vo);
        }
        $data = [
            'user_id' => $uid,
            'nickname' => $data['nickname'],
            'province' => $data['province'],
            'city' => $data['city'],
            'region' => $data['region'],
            'email' => $data['email'],
            'tel' => $data['tel'],
            'sex' => intval($data['sex']) ? 1 : 0,
            'birthday' => $data['birthday'] ? strtotime($data['birthday']) : 0,
        ];
        if (empty($data['nickname'])) {
            return $this->error('请填写用户昵称!');
        }
        $data['nickname'] = \dux\lib\Str::symbolClear($data['nickname']);
        if (empty($data['nickname'])) {
            return $this->error('昵称填写不正确，请勿使用符号昵称!');
        }
        if (empty($data['user_id'])) {
            return $this->error('用户获取错误!');
        }
        $info = target('member/MemberUser')->getUser($uid);


        $config = target('member/MemberConfig')->getConfig();
        if (in_array($data['nickname'], explode('|', $config['reg_ban_name']))) {
            return $this->error('当前昵称被禁止使用!');
        }
        $count = target('member/MemberUser')->where(['nickname' => $data['nickname'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
        if ($count) {
            return $this->error('该昵称已被使用!');
        }


        if (!empty($info['email'])) {
            unset($data['email']);
        }
        if (!empty($info['tel'])) {
            unset($data['tel']);
        }
        if (!empty($data['email'])) {
            if (!filter_var($info['email'], \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱格式输入错误!');
            }
            $count = target('member/MemberUser')->where(['email' => $data['emall'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
            if ($count) {
                return $this->error('该邮箱已被使用!');
            }
        }

        if (!empty($data['tel'])) {
            if (!preg_match('/(^1[3|4|5|6|7|8|9][0-9]{9}$)/', $info['tel'])) {
                return $this->error('手机号码输入错误!');
            }
            $count = target('member/MemberUser')->where(['tel' => $data['tel'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
            if ($count) {
                return $this->error('该手机已被使用!');
            }
        }

        if (!target('member/MemberUser')->edit($data)) {
            return $this->error(target('member/MemberUser')->getError());
        }
        return $this->success($data);
    }

    /**
     * 用户名获取账号信息
     * @param $username
     * @return bool
     */
    public function isUser($username) {
        $info = target('member/MemberUser')->getWhereInfo([
            'tel' => $username,
        ]);
        if (empty($info)) {
            $info = target('member/MemberUser')->getWhereInfo([
                'email' => $username,
            ]);
        }
        if (empty($info)) {
            return $this->error('用户不存在');
        }
        return $this->success();

    }

    /**
     * 获取验证码
     * @param string $receive
     * @param string $content
     * @param int $code
     * @param int $type
     * @param string $verifyType
     * @return bool
     */
    public function getVerify($receive = '', $content = '', $code = 0, $type = 0, $verifyType = '') {
        if (empty($receive)) {
            return $this->error('接受账号不正确');
        }
        $userType = $this->getUserType($receive);
        if (!$userType) {
            return false;
        }
        if (empty($verifyType)) {
            if ($userType == 'tel') {
                $verifyType = 'sms';
            }
            if ($userType == 'email') {
                $verifyType = 'mail';
            }
        }
        $config = target('member/MemberConfig')->getConfig();
        $typeInfo = target('tools/ToolsSendConfig')->defaultType($verifyType);

        if (!target($typeInfo['target'], 'send')->check(['receive' => $receive])) {
            return $this->error(target($typeInfo['target'], 'send')->getError());
        }
        $info = target('member/MemberVerify')->where([
            'receive' => $receive,
            'type' => $type,
        ])->order('verify_id desc')->find();
        if (!empty($info)) {
            if ($info['time'] + intval($config['verify_second']) > time()) {
                return $this->error($config['verify_second'] . '秒内无法再次获取验证码!');
            }
            $where = [];
            $where['receive'] = $receive;
            $where['type'] = $type;
            $where['_sql'] = 'time > ' . (time() - $info['verify_minute'] * 60);
            $count = target('member/MemberVerify')->where($where)->count();
            if ($count >= $config['verify_minute_num']) {
                return $this->error('验证码获取太频繁,请过段时间再试!');
            }
        }
        if (!$code) {
            $code = $this->getCode(6);
        }
        target('member/MemberVerify')->beginTransaction();
        $data = [];
        $data['time'] = time();
        $data['code'] = $code;
        $data['receive'] = $receive;
        $data['expire'] = $config['verify_expire'];
        $data['type'] = $type;
        $data['status'] = 0;
        if (!target('member/MemberVerify')->add($data)) {
            target('member/MemberVerify')->rollBack();
            return $this->error('验证码获取失败,请稍候再试!');
        }
        $siteConfig = target('site/SiteConfig')->getConfig();
        if (empty($content)) {
            if ($verifyType == 'sms') {
                $content = $config['verify_sms_tpl'];
            } else {
                $content = $config['verify_mail_tpl'];
            }
            $contentConfig = unserialize($content);
            if(!empty($contentConfig)) {
                $content = $contentConfig;
            }
        }
        $params = [];
        $expire = $config['verify_expire'] / 60;
        if (!is_array($content)) {
            $content = str_replace('[验证码]', $code, $content);
            $content = str_replace('[有效期]', $expire, $content);
        } else {
            $tmpData = $content['data'];
            $params['tpl'] = $content['id'];
            foreach ($tmpData['key'] as $k => $v) {
                if(empty($v) || empty($tmpData['val'][$k])) {
                    continue;
                }
                if($v == '验证码') {
                    $params[$tmpData['val'][$k]] = $code;
                }else if($v == '有效期') {
                    $params[$tmpData['val'][$k]] = $expire;
                }else {
                    $params[$tmpData['val'][$k]] = $v;
                }
            }
            $content = '';
        }

        $status = target('tools/Tools', 'service')->sendMessage([
            'receive' => $receive,
            'class' => $verifyType,
            'title' => $siteConfig['info_name'] . '会员验证码',
            'content' => $content,
            'param' => $params,
        ]);
        if (!$status) {
            target('member/MemberVerify')->rollBack();
            return $this->error(target('tools/Tools', 'service')->getError());
        }
        target('member/MemberVerify')->commit();
        return $this->success();
    }

    /**
     * 验证验证码
     * @param $receive
     * @param $code
     * @param int $type
     * @return bool
     */
    public function checkVerify($receive, $code, $type = 0) {
        $info = target('member/MemberVerify')->where([
            'receive' => $receive,
            'code' => trim($code),
            'type' => $type,
        ])->order('verify_id desc')->find();
        if (empty($info)) {
            return $this->error('验证码不正确!');
        }
        if ($info['status']) {
            return $this->error('验证码已使用!');
        }
        if ($info['time'] + $info['expire'] < time()) {
            return $this->error('验证码已过期!');
        }
        $data = [];
        $data['verify_id'] = $info['verify_id'];
        $data['status'] = 1;
        target('member/MemberVerify')->edit($data);
        return $this->success();
    }

    /**
     * 生成验证码
     * @param int $length
     * @return int
     */
    public function getCode($length = 6) {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }

    /**
     * 账户充值
     * @param $rechargeNo
     * @param $money
     * @param $payName
     * @param string $payNo
     * @param string $payWay
     * @return bool
     */
    public function payRecharge($rechargeNo, $money, $payName, $payNo = '', $payWay = '') {
        if (empty($rechargeNo) || empty($money) || empty($payName)) {
            return $this->error('充值信息错误!');
        }
        $info = target('member/PayRecharge')->getWhereInfo([
            'recharge_no' => $rechargeNo,
        ]);

        if (empty($info)) {
            return $this->error('充值单不存在！');
        }
        if ($money < $info['money']) {
            return $this->error('充值金额不正确!');
        }

        $payNo = $payNo ? $payNo : log_no();

        $status = target('member/Finance', 'service')->account([
            'user_id' => $info['user_id'],
            'money' => $money,
        ]);
        if (!$status) {
            return $this->error(target('member/Finance', 'service')->getError());
        }
        $accountStatus = target('statis/Finance', 'service')->account([
            'user_id' => $info['user_id'],
            'species' => 'member_account',
            'sub_species' => 'recharge',
            'money' => $money,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'pay_way' => $payWay,
            'type' => 1,
            'title' => '在线充值',
        ]);
        if (!$accountStatus) {
            return $this->error(target('statis/Finance', 'service')->getError());
        }

        $status = target('member/PayRecharge')->where([
            'recharge_id' => $info['recharge_id'],
        ])->data([
            'status' => 1,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'complete_time' => time(),
        ])->update();
        if (!$status) {
            return $this->error('充值支付失败!');
        }

        $hookList = run('service', 'member', 'recharge', [$info['user_id'], $money, $payNo, $payName]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }

        target('tools/Tools', 'service')->notice('member', 'recharge', $info['user_id'], [
            '用户名' => $info['show_name'],
            '金额' => $money,
            '编号' => $payNo,
            '交易名' => $payName,
            '时间' => date('Y-m-d H:i:s', time()),

        ], 'pages/wallet/index');

        return $this->success();
    }

    /**
     * 第三方登录注册
     * @param $type
     * @param $unionId
     * @param $openId
     * @param string $nickname
     * @param string $avatar
     * @return bool
     */
    public function oauthUser($type, $unionId, $openId, $nickname = '', $avatar = '') {
        if ((empty($unionId) && empty($openId)) || empty($type)) {
            return $this->error('平台ID为空');
        }
        //判断关联信息
        $info = target('member/MemberConnect')->getWhereInfo([
            'open_id' => $openId,
            'type' => $type,
        ]);
        target('member/MemberUser')->beginTransaction();
        if (empty($info)) {
            target('member/MemberConnect')->where(['type' => $type])->lock()->select();
            $data = [
                'union_id' => $unionId,
                'open_id' => $openId,
                'type' => $type,
                'data' => serialize([
                    'nickname' => $nickname,
                    'avatar' => $avatar,
                ]),
            ];
            $connentId = target('member/MemberConnect')->data($data)->insert();
            if (!$connentId) {
                return $this->error('保存登录数据失败!');
            }
            $userId = 0;
        } else {
            $connentId = $info['connect_id'];
            $userId = $info['user_id'];
        }
        //判断开放用户
        if ($unionId && empty($userId)) {
            $unionInfo = target('member/MemberConnect')->getWhereInfo([
                'union_id' => $unionId,
                'user_id[>]' => 0,
            ]);
            if ($unionInfo) {
                $userId = $unionInfo['user_id'];
            }
        }
        //创建新用户
        if (empty($userId)) {
            $logInfo = $this->regOauthUser($nickname, $avatar);
            if (!$logInfo) {
                return false;
            }
            $userId = $logInfo['uid'];
        }else {
            target('member/MemberUser')->edit([
                'user_id' => $userId,
                'avatar' =>  $avatar,
            ]);
        }
        //创建关联
        if (empty($info)) {
            if (!$this->connectUser($connentId, $userId)) {
                return false;
            }
        }
        target('member/MemberUser')->commit();

        $userInfo = target('member/MemberUser')->getUser($userId);
        $password = $userInfo['password'];
        $config = \dux\Config::get('dux.use');
        $loginData = [];
        $loginData['uid'] = $userId;
        $loginData['token'] = sha1($password . $config['safe_key']);
        $loginData['data'] = $userInfo;
        return $this->success([
            'status' => 'login',
            'data' => $loginData,
        ]);
    }

    /**
     * 关联账户信息
     * @param $connectId
     * @param $userId
     * @return bool
     */
    public function connectUser($connectId, $userId) {
        if (empty($connectId)) {
            return $this->error('关联信息不正确!');
        }
        if (empty($userId)) {
            return $this->error('用户信息无法获取！');
        }
        $data = [
            'connect_id' => $connectId,
            'user_id' => $userId,
        ];
        if (!target('member/MemberConnect')->edit($data)) {
            return $this->error('关联登录信息失败!');
        }
        return $this->success();
    }

    /**
     * 第三方登录
     * @param $type
     * @param $unionId
     * @param $openId
     * @return bool
     */
    public function loginOauthUser($type, $unionId, $openId) {
        if ((empty($unionId) && empty($openId)) || empty($type)) {
            return $this->error('平台ID为空');
        }
        $info = target('member/MemberConnect')->getWhereInfo([
            'OR' => [
                'union_id' => $unionId,
                'open_id' => $openId,
            ],
            'type' => $type,
        ]);
        if (empty($info)) {
            return $this->success([
                'status' => 'reg',
            ]);
        }
        $userId = $info['user_id'];
        $userInfo = target('member/MemberUser')->getInfo($userId);
        if (empty($userInfo)) {
            return $this->success([
                'status' => 'reg',
            ]);
        }
        if ($info['union_id'] <> $unionId) {
            target('member/MemberConnect')->edit([
                'connect_id' => $info['connect_id'],
                'union_id' => $unionId,
            ]);
        }
        $password = $userInfo['password'];
        $userInfo = target('member/MemberUser')->getUser($userId);
        $config = \dux\Config::get('dux.use');
        $loginData = [];
        $loginData['uid'] = $userId;
        $loginData['token'] = sha1($password . $config['safe_key']);
        $loginData['data'] = $userInfo;
        return $this->success([
            'status' => 'login',
            'data' => $loginData,
        ]);
    }

    /**
     * 注册第三方账号
     * @param string $nickname
     * @param string $avatar
     * @return array|bool
     */
    public function regOauthUser($nickname = '', $avatar = '') {
        $config = target('member/MemberConfig')->getConfig();
        if (empty($nickname)) {
            $nickname = \dux\lib\Str::randStr(7);
        }
        $nickname = str_len($nickname, 50, '');
        $password = \dux\lib\Str::randStr(15);
        $data = [];
        $data['nickname'] = $nickname;
        $data['avatar'] = $avatar;
        $data['password'] = $password;
        $data['role_id'] = intval($config['reg_role']);

        $userId = target('member/MemberUser')->saveData('add', $data);
        if (!$userId) {
            return $this->error(target('member/MemberUser')->getError());
        }
        $hookList = run('service', 'member', 'reg', [$userId, $nickname, $avatar]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }
        $config = \dux\Config::get('dux.use');
        $password = md5(\dux\lib\Str::randStr(15));
        $token = sha1(md5($password) . $config['safe_key']);
        return [
            'uid' => $userId,
            'token' => $token,
        ];
    }


}
