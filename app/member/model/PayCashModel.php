<?php

/**
 * 资金账户管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayCashModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'cash_id',
    ];

    protected function base($where) {
        return $this->table('pay_cash(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->field(['A.*', 'B.email(user_email)', 'B.tel(user_tel)', 'B.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key] = $this->dataFormat($vo);
        }
        return $list;
    }
    public function showList($data) {
        foreach ($data as $key => $vo) {
            $data[$key]['url'] = url('member/Cash/info', ['no' => $vo['cash_no']]);
            $data[$key]['account'] = substr($vo['account'], -4);
        }
        return $data;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function showInfo($info) {
        if($info) {
            $info['account'] = substr($info['account'], -4);
        }
        return $info;
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info = $this->dataFormat($info);
        }
        return $info;
    }

    private function dataFormat($data) {
        $data['show_name'] = target('member/MemberUser')->getNickname($data['user_nickname'], $data['user_tel'], $data['user_email']);
        $data['show_time'] = date('Y-m-d', $data['create_time']);
        $data['money_tax'] = round($data['tax'] * $data['tax'] / 100, 2);
        if(!$data['status']) {
            $data['show_status'] = '失败';
        }
        if($data['status'] == 1) {
            $data['show_status'] = '处理中';
        }
        if($data['status'] == 2) {
            $data['show_status'] = '成功';
        }
        return $data;
    }

    public function type() {
        return [
            1 => '微信',
            2 => '支付宝',
            3 => '银行卡',
        ];
    }

    /**
     * 转账
     * @param $type
     * @param $userId
     * @param $money
     * @param $no
     * @param $platform
     * @return bool
     */
    public function transfer($type, $userId, $money, $no, $platform) {
        switch ($type) {
            case 1:
                $name = 'wechat';
                break;
            case 2:
                $name = 'alipay';
                break;
        }
        if(empty($name)) {
            $this->error = '请选择提现方式！';
            return false;
        }
        $typeList = target('member/PayConfig')->typeList(true, $platform);
        $payInfo = [];
        foreach ($typeList as $vo) {
            if (strstr($vo['type'], $name) !== false) {
                $payInfo = $vo;
                break;
            }
        }
        if (empty($payInfo)) {
            $this->error = '该支付接口不可用！';
            return false;
        }
        $payNo = target($payInfo['target'], 'pay')->transfer([
            'pay_no' => $no,
            'user_id' => $userId,
            'money' => $money,
            'remark' => '用户【' . $userId . '】余额提现',
        ]);
        if (!$payNo) {
            $this->error = target($payInfo['target'], 'pay')->getError();
            return false;
        }
        return [
            'pay_no' => $payNo,
            'pay_name' => $payInfo['name'],
            'pay_way' => $payInfo['type']
        ];
    }



}