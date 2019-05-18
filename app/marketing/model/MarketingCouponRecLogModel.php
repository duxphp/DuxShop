<?php

/**
 * 老带新赠券记录
 */
namespace app\marketing\model;

use app\system\model\SystemModel;

class MarketingCouponRecLogModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'log_id',
    ];

    protected function base($where) {
        return $this->table('marketing_coupon_rec_log(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'])
            ->join('member_user(C)', ['C.user_id', 'A.rec_user_id'])
            ->field([
                'A.*',
                'B.nickname(user_nickname)', 'B.tel(user_tel)', 'B.email(user_email)', 'B.avatar(user_avatar)',
                'C.nickname(rec_nickname)', 'B.tel(rec_tel)', 'B.email(rec_email)', 'B.avatar(rec_avatar)',
            ])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.rec_id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if(empty($list)){
            return [];
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        return $info;
    }

    public function getInfo($id) {
        return $this->getWhereInfo(['A.log_id' => $id]);

    }

}