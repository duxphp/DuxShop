<?php

/**
 * 老带新赠券
 */
namespace app\marketing\model;

use app\system\model\SystemModel;

class MarketingCouponRecModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'rec_id',
        'format' => [
            'start_time' => [
                'function' => ['strtotime', 'all'],
            ],
            'stop_time' => [
                'function' => ['strtotime', 'all'],
            ],
        ]
    ];

    protected function base($where) {
        return $this->table('marketing_coupon_rec(A)')
            ->join('marketing_coupon(B)', ['B.coupon_id', 'A.new_coupon_id'])
            ->join('marketing_coupon(C)', ['C.coupon_id', 'A.old_coupon_id'])
            ->field([
                'A.*',
                'B.name(new_coupon_name)', 'B.meet_money(new_coupon_meet_money)', 'B.money(new_coupon_money)', 'B.image(new_coupon_image)', 'B.expiry_day(new_coupon_meet_day)',
                'C.name(old_coupon_name)', 'C.meet_money(old_coupon_meet_money)', 'C.money(old_coupon_money)', 'C.image(old_coupon_image)', 'C.expiry_day(old_coupon_meet_day)',
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
        return $this->getWhereInfo(['A.rec_id' => $id]);

    }

}