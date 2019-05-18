<?php

/**
 * 优惠券管理
 */

namespace app\marketing\model;

use app\system\model\SystemModel;

class MarketingCouponModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'coupon_id',
        'format' => [
            'create_time' => [
                'function' => ['time', 'add'],
            ],
            'start_time' => [
                'function' => ['strtotime', 'all'],
            ],
            'end_time' => [
                'function' => ['strtotime', 'all'],
            ],
        ]
    ];

    protected function base($where) {
        return $this->table('marketing_coupon(A)')
            ->join('marketing_coupon_class(B)', ['B.class_id', 'A.class_id'])
            ->field(['A.*', 'B.name(class_name)'])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.coupon_id desc') {
        $where['A.del_status'] = 0;
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if (empty($list)) {
            return [];
        }
        $currencyList = target('member/MemberCurrency')->typeList();
        $typeList = $this->typeList(true);

        foreach ($list as $key => $vo) {
            $list[$key]['currencyInfo'] = $currencyList[$vo['exchange_type']];
            $list[$key]['typeInfo'] = $typeList[$vo['type']];
        }
        return $list;
    }

    public function countList($where = array()) {
        $where['A.del_status'] = 0;
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $where['A.del_status'] = 0;
        $info = $this->base($where)->find();
        if ($info) {
            $currencyList = target('member/MemberCurrency')->typeList();
            $typeList = target('marketing/MarketingCoupon')->typeList(true);
            $info['currencyInfo'] = $currencyList[$info['exchange_type']];
            $info['typeInfo'] = $typeList[$info['type']];
        }
        return $info;
    }

    public function getInfo($id) {
        return $this->getWhereInfo(['A.coupon_id' => $id]);

    }


    public function _saveBefore($data, $type) {
        $typeList = target('marketing/MarketingCoupon')->typeList(true);
        $typeInfo = $typeList[$data['type']];
        if (empty($typeInfo)) {
            $this->error = '优惠券类型不存在!';
            return false;
        }
        if ($typeInfo['type'] == 1 || $typeInfo['type'] == 2) {
            if (empty($data['has_id'])) {
                $this->error = '请选择关联信息!';
                return false;
            }
            if (is_array($data['has_id'])) {
                $data['has_id'] = implode(',', $data['has_id']);
            } else {
                $data['has_id'] = $data['has_id'];
            }
        } else {
            $data['has_id'] = '';
        }
        if (empty($data['class_id'])) {
            $this->error = '请选择优惠券分类!';
            return false;
        }
        if (empty($data['image'])) {
            $this->error = '请上传优惠券图片!';
            return false;
        }
        if (empty($data['money'])) {
            $this->error = '请输入优惠券额度!';
            return false;
        }
        if ($data['money'] > $data['meet_money']) {
            $this->error = '优惠券额度不能大于满足费用!';
            return false;
        }
        if (!$data['platform'] && $type = 'add') {
            $data['status'] = 2;
        }

        if ($data['platform'] && $type = 'add') {
            $data['status'] = 1;
        }

        return $data;
    }

    public function typeList($system = false) {
        $list = hook('service', 'Type', 'Coupon');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        foreach ($data as $key => $vo) {
            if (!$system && $vo['system']) {
                unset($data[$key]);
                continue;
            }
            $data[$key]['key'] = $key;
        }

        return $data;
    }


    public function giveCoupon($userIds, $couponId, $num, $show = 0) {
        if(!is_array($userIds)) {
            $userIds = [$userIds];
        }
        $info = $this->where(['coupon_id' => $couponId])->lock(true)->find();
        $count = count($userIds);
        if (!$info) {
            $this->error = '优惠券不存在！';
            return false;
        }
        if (!$info['stock_type'] && $count > $info['stock']) {
            $userIds = array_slice($userIds, 0, $info['stock']);
        }
        $startTime = time();
        $endTime = time() + $info['expiry_day'] * 86400;
        foreach ($userIds as $userId) {
            for ($i = 0; $i <= $num; $i++) {
                if (!$info['stock_type']) {
                    if (!$this->where(['coupon_id' => $couponId, 'stock[>=]' => 1])->data(['stock[-]' => 1])->update()) {
                        continue;
                    }
                }
                $data = [
                    'user_id' => $userId,
                    'coupon_id' => $couponId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'show' => $show
                ];
                if (!target('marketing/MarketingCouponLog')->add($data)) {
                    $this->error = target('marketing/MarketingCouponLog')->getError();
                    return false;
                }
                if (!$this->where(['coupon_id' => $couponId])->data(['receive[+]' => 1])->update()) {
                    return false;
                }
            }
        }
        return true;
    }

}