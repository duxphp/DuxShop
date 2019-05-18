<?php

/**
 * 用户管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberUserModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'user_id',
        'validate' => [
            'role_id' => [
                'empty' => ['', '角色不正确!', 'must', 'all'],
            ],
            'email' => [
                'email' => ['', '邮箱填写不正确!', 'value', 'all'],
                'unique' => ['', '已存在相同的用户名!', 'value', 'all'],
            ],
            'tel' => [
                'phone' => ['', '手机号码填写不正确!', 'value', 'all'],
                'unique' => ['', '已存在相同的用户名!', 'value', 'all'],
            ],
            'password' => [
                'len' => ['6,18', '请输入6~18位密码!', 'must', 'add'],
            ]
        ],
        'format' => [
            'nickname' => [
                'function' => ['html_clear', 'all'],
            ],
            'province' => [
                'function' => ['html_clear', 'all'],
            ],
            'city' => [
                'function' => ['html_clear', 'all'],
            ],
            'region' => [
                'function' => ['html_clear', 'all'],
            ],
            'password' => [
                'ignore' => ['', 'edit'],
            ],
            'reg_time' => [
                'function' => ['time', 'add'],
            ],
            'login_time' => [
                'function' => ['time', 'add'],
            ]
        ],
        'into' => '',
        'out' => '',
    ];

    protected function _saveBefore($data) {
        if (empty($data['tel']) && empty($data['email']) && empty($data['nickname'])) {
            $this->error = '手机号或邮箱或昵称必须存在一个';
            return false;
        }
        if ($data['password']) {
            $data['password'] = md5($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }

    protected function base($where) {
        return $this->table('member_user(A)')
            ->join('member_role(B)', ['B.role_id', 'A.role_id'])
            ->join('member_grade(C)', ['C.grade_id', 'A.grade_id'])
            ->field(['A.*', 'B.name(role_name)', 'C.name(grade_name)', 'C.discount'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = $this->getNickname($vo['nickname'], $vo['tel'], $vo['email']);
            $list[$key]['avatar'] = $vo['avatar'] ? $vo['avatar'] : DOMAIN_HTTP . ROOT_URL . '/public/member/images/avatar.jpg';
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = $this->getNickname($info['nickname'], $info['tel'], $info['email']);
            $info['avatar'] = $info['avatar'] ? $info['avatar'] : DOMAIN_HTTP . ROOT_URL . '/public/member/images/avatar.jpg';
        }
        return $info;
    }


    protected function _delBefore($id) {
        target('member/MemberUser')->beginTransaction();
        return true;
    }

    protected function _delAfter($id) {
        $hookList = run('service', 'member', 'del', [$id]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('member/MemberUser')->rollBack();
                $this->error = target($app . '/Member', 'service')->getError();
                return false;
            }
        }
        if (!target('member/PointsAccount')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/PayAccount')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberConnect')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberFeedback')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberFile')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberNotice')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberReal')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('statis/StatisFinancial')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('statis/StatisFinancialLog')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('statis/StatisViews')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('statis/StatisNumber')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        target('member/MemberUser')->commit();
        return true;
    }

    /**
     * 获取当前用户ID
     */
    public function getUid() {
        $login = \dux\Dux::cookie()->get('user_login');
        if(!target('member/MemberUser')->checkUser($login['uid'], $login['token'])) {
            return false;
        }
        return $login['uid'];
    }

    /**
     * 获取账户信息
     * @param $uid
     * @return array|bool
     */
    public function getUser($uid) {
        $infoData = $this->getInfo($uid);
        if (empty($infoData)) {
            $this->error = '用户不存在！';
            return false;
        }
        $accountInfo = target('member/PayAccount')->getWhereInfo(['A.user_id' => $uid]);
        if(empty($accountInfo)) {
            $accountData = [
                'user_id' => $uid,
            ];
            target('member/PayAccount')->add($accountData);
        }
        $pointInfo = target('member/PointsAccount')->getWhereInfo(['A.user_id' => $uid]);
        if(empty($pointInfo)) {
            $pointInfo = [
                'user_id' => $uid,
            ];
            target('member/PointsAccount')->add($pointInfo);
        }
        $realInfo = target('member/MemberReal')->getWhereInfo([
            'A.user_id' => $uid,
            'A.status' => 2
        ]);
        $infoData['real_status'] = $realInfo ? true : false;
        $infoData['money'] = $accountInfo['money'] ? $accountInfo['money'] : 0;
        $infoData['point'] = $pointInfo['money'] ? $pointInfo['money'] : 0;
        return $infoData;
    }

    /**
     * 验证用户登录
     * @param $uid
     * @param $token
     * @return bool
     */
    public function checkUser($uid = '', $token = '') {
        if (empty($uid) || empty($token)) {
            $this->error = '帐号登录失效!';
            return false;
        }
        $info = target('member/MemberUser')->getWhereInfo([
            'user_id' => $uid
        ]);
        if (empty($info)) {
            $this->error = '用户不存在!';
            return false;
        }
        $config = \dux\Config::get('dux.use');
        $verify = sha1($info['password'] . $config['safe_key']);
        if ($token <> $verify) {
            $this->error = '登录验证失败,请重新登录!';
            return false;
        }
        return true;
    }

    /**
     * 获取用户昵称
     * @param $nickname
     * @param $tel
     * @param $email
     * @return mixed
     */
    public function getNickname($nickname, $tel, $email) {
        if ($nickname) {
            return $nickname;
        }
        if ($tel) {
            return $tel;
        }
        if ($email) {
            return $email;
        }
        return '未知';
    }

    public function getAvatar($avatar) {
        if(empty($avatar)) {
            return DOMAIN_HTTP . ROOT_URL . '/public/member/images/avatar.jpg';
        }
        return $avatar;
    }

    /**
     * 生成用户头像
     * @param $userId
     * @param $image
     * @return bool
     * @throws \Exception
     */
    public function avatarUser($userId, $image) {
        $url = 'upload/avatar/' . $userId . '/';
        $dir = ROOT_PATH . $url;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                $this->error = '头像目录没有写入权限!';
                return false;
            }
        }
        if (strpos($image, 'http') !== false && strpos($image, 'https') !== false) {
            $image = realpath(ROOT_PATH . $image);
        }
        $data = \dux\lib\Http::curlGet($image);
        if(empty($data)) {
            return true;
        }
        $name = time();
        $manager = new \Intervention\Image\ImageManager(['driver' => 'imagick']);
        $manager->canvas(128, 128, '#ffffff')
            ->insert($manager->make($data)->resize(128, 128))
            ->save($dir.$name . '.jpg');

        $status = target('member/MemberUser')->edit([
            'user_id' => $userId,
            'avatar' =>  DOMAIN_HTTP . ROOT_URL . '/' . $url . $name . '.jpg',
        ]);
        if (empty($status)) {
            $this->error = '头像保存失败!';
            return false;
        }
        return true;
    }


    /**
     * 获取记录接口
     * @return array
     */
    public function typeList() {
        $list = hook('service', 'Type', 'PayLog');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

}
