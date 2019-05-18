<?php

/**
 * 会员接口
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class InfoApi extends BaseApi {

    /**
     * 会员中心首页
     */
    public function index() {
        $list = hook('service', 'Type', 'Member', [$this->userInfo]);
        $data = [];
        foreach ($list as $value) {
            $data = array_merge((array)$data, (array)$value);
        }
        $data = (array)array_sort($data, 'sort');
        $this->success('ok', [
            'list' => $data
        ]);

    }

    /**
     * 获取用户资料
     * @method Get
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList:[{"user_id": "2", "role_id": "1", "grade_id": "1", "nickname": "徐健", "email": "", "tel": "", "password": " ", "avatar": " ", "province": "", "city": "", "region": "", "reg_time": "1556329929", "login_time": "1556329929", "login_ip": "", "status": "1", "sex": "1", "role_name": "会员", "grade_name": "普通会员", "discount": "90", "show_name": "徐健", "real_status": false, "money": "9742.00", "point": "138.00"}]"}
     * @field integer $user_id 用户ID
     * @field integer $role_id 角色ID
     * @field integer $grade_id 等级ID
     * @field string $nickname 昵称
     * @field string $email 邮箱
     * @field string $tel 电话
     * @field string $password 密码
     * @field string $avatar 头像
     * @field string $province 省份
     * @field string $city 城市
     * @field string $region 区域
     * @field string $reg_time 注册时间
     * @field string $login_time  登录时间
     * @field string $login_ip 登录ip
     * @field integer $status 状态
     * @field integer $sex 性别
     * @field string $role_name 角色名称
     * @field string $role_name 用户等级名称
     * @field decimal $discount 优惠总额
     * @field string $show_name 显示名称
     * @field decimal $money 余额
     * @field decimal $point 积分
     */
    public function info() {
        $info = $this->userInfo;
        if (empty($info)) {
            $this->error(target('Member/MemberUser')->getError());
        }
        $this->success('ok', $info);
    }

    /**
     * 更新用户资料
     * @method POST
     * @param integer $user_id 用户ID
     * @param string $nickname 用户昵称
     * @param string $province 省份
     * @param string $city  城市
     * @param string $region 区域
     * @param string $birthday 出生年月
     * @param string email  邮箱
     * @param string tel  电话
     * @param string sex 性别
     * @return integer $code 200
     * @return string $message 修改资料成功
     * @return json $result[]
     */
    public function update() {
        target('member/Setting', 'middle')->setParams(
            array_merge($this->data, ['user_id' => $this->userId]))->putInfo()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 更新账号
     * @method POST
     * @param integer $user_id 用户ID
     * @param string $username 用户名
     * @param integer $type  类型
     * @return integer $code 200
     * @return string $message ok
     * @return json $result []
     */
    public function username() {
        target('member/Setting', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'user_info' => $this->userInfo,
            'username' => $this->data['username'],
            'type' => $this->data['type'],
            'val_type' => $this->data['type'],
            'val_code' => $this->data['val_code'],
        ])->putUsername()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 获取验证码
     */
    public function getCode() {
        target('member/Setting', 'middle')->setParams([
            'user_info' => $this->userInfo,
            'receive' => $this->data['receive'],
            'val_type' => $this->data['valtype'],
            'img_code' => $this->data['imgcode']
        ])->getCode()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 上传照片
     */
    public function upload() {
        dux_log($_FILES);
        target('member/Upload', 'middle')->setParams([
            'user_id' => $this->userId,
            'water_status' => false,
        ])->post()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 修改密码
     * @method POST
     * @param integer $user_id 用户ID
     * @param string $old_password 旧密码
     * @param string $password  新密码
     * @param string $password2  确认新密码
     * @return integer $code 200
     * @return string $message ok
     * @return json $result []
     */
    public function password() {
        target('member/Setting', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'old_password' => $this->data['old_password'],
            'password' => $this->data['password'],
            'password2' => $this->data['password2'],
        ])->putPassword()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function payPassword() {
        target('member/Setting', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'user_info' => $this->userInfo,
            'val_type' => $this->data['val_type'],
            'val_code' => $this->data['val_code'],
            'password' => $this->data['password'],
        ])->putPayPassword()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


    /**
     * 关于会员
     */
    public function about() {
        target('member/Index', 'middle')->about()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}
