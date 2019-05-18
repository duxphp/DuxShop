<?php

/**
 * 新人送券
 */
namespace app\marketing\model;

use app\system\model\SystemModel;

class MarketingCouponGiftModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'gift_id',
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
        return $this->table('marketing_coupon_gift(A)')
            ->join('marketing_coupon(B)', ['B.coupon_id', 'A.coupon_id'])
            ->field(['A.*', 'B.name(coupon_name)'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.gift_id desc') {
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
        return $this->getWhereInfo(['A.gift_id' => $id]);

    }

}