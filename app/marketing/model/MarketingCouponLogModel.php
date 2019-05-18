<?php

/**
 * 优惠券记录
 */
namespace app\marketing\model;

use app\system\model\SystemModel;

class MarketingCouponLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    protected function base($where) {
        return $this->table('marketing_coupon_log(A)')
            ->join('marketing_coupon(B)', ['B.coupon_id', 'A.coupon_id'])
            ->join('member_user(C)', ['C.user_id', 'A.user_id'])
            ->field([ 'B.*', 'A.*','C.email(user_email)', 'C.tel(user_tel)', 'C.nickname(user_nickname)'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order('A.log_id desc')
            ->select();
        if(empty($list)) {
            return [];
        }
        $typeList = target('marketing/MarketingCoupon')->typeList(true);
        foreach($list as $key => $vo) {
            $typeInfo = $typeList[$vo['type']];
            $list[$key]['typeInfo'] = $typeInfo;
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
            $list[$key]['url'] = $typeInfo['url']($vo['coupon_id']);
            $list[$key]['overdue'] = $vo['end_time'] < time() ? true : false;
            $list[$key]['surplus_day'] = ceil(($vo['end_time'] - $vo['start_time']) / 86400);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if($info) {
            $currencyList = target('member/MemberCurrency')->typeList();
            $typeList = target('marketing/MarketingCoupon')->typeList(true);
            $info['currencyInfo'] = $currencyList[$info['exchange_type']];
            $info['typeInfo'] =$typeList[$info['type']];
        }
        return $info;
    }

    public function getInfo($id) {
        $where = [];
        $where['A.log_id'] = $id;
        return $this->getWhereInfo($where);
    }

}