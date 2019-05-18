<?php

/**
 * 领券中心
 */

namespace app\marketing\middle;

class CouponMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'marketing/MarketingCoupon';


    protected function meta() {
        $this->setMeta('领券中心');
        $this->setName('领券中心');
        $this->setCrumb([
            [
                'name' => '领券中心',
                'url' => URL,
            ],
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo,
        ]);
    }

    protected function classData() {
        $classList = target('marketing/MarketingCouponClass')->loadList([], 0, 'sort asc, class_id asc');
        $classList = array_merge([['class_id' => 0, 'name' => '全部', 'sort' => 0]], $classList);
        return $this->run([
            'classList' => $classList,
        ]);
    }


    protected function data() {
        $userId = intval($this->params['user_id']);
        $type = $this->params['type'];
        $classId = $this->params['class_id'];
        $id = $this->params['id'];
        $where = [];
        if ($type) {
            $where['A.type'] = $type;
        }
        if ($classId) {
            $where['A.class_id'] = $classId;
        }
        if ($id) {
            $where['A.coupon_id'] = $id;
        }
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $where['A.status'] = 1;
        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'A.coupon_id desc');

        $couponIds = [];
        foreach ($list as $vo) {
            $couponIds[] = $vo['coupon_id'];
        }

        $useIds = [];
        if ($couponIds && $userId) {
            $couponLog = target('marketing/MarketingCouponLog')->loadList([
                '_sql' => 'A.coupon_id in (' . implode(',', $couponIds) . ')',
                'A.user_id' => $userId,
            ]);
            foreach ($couponLog as $vo) {
                $useIds[] = $vo['coupon_id'];
            }
        }

        foreach ($list as $key => $vo) {
            if (in_array($vo['coupon_id'], $useIds)) {
                $list[$key]['receive_status'] = 1;
            } else {
                $list[$key]['receive_status'] = 0;
            }
        }

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'pageList' => $list,
            'pageLimit' => $pageLimit,
        ]);
    }

    protected function receive() {
        $userId = intval($this->params['user_id']);
        $couponId = intval($this->params['coupon_id']);
        $info = target($this->_model)->getInfo($couponId);
        if (empty($userId)) {
            return $this->stop('您尚未登录！');
        }
        if (empty($info)) {
            return $this->stop('该优惠券不存在！');
        }
        if (!$info['status']) {
            return $this->stop('该优惠券已下架！');
        }
        if ($info['start_time'] > time()) {
            return $this->stop('该优惠券未到领取时间！');
        }
        if ($info['end_time'] < time()) {
            return $this->stop('该优惠券已过领取时间！');
        }
        $logInfo = target('marketing/MarketingCouponLog')->getWhereInfo([
            'A.coupon_id' => $couponId,
            'A.user_id' => $userId,
        ]);
        if (!empty($logInfo)) {
            return $this->stop('您已领取过该券！');
        }

        if ($info['exchange_price']) {
            $currencyList = target('member/MemberCurrency')->typeList();
            $status = target($currencyList[$info['exchange_type']]['target'], 'service')->account([
                'user_id' => $userId,
                'money' => $info['exchange_price'],
            ]);
            if (!$status) {
                return $this->stop(target($currencyList[$info['exchange_type']]['target'], 'service')->getError());
            }
            $accountStatus = target('statis/Finance', 'service')->account([
                'user_id' => $info['user_id'],
                'species' => 'credit_coupon',
                'money' => $info['exchange_price'],
                'title' => '优惠券兑换',
                'remark' => '兑换【' . $info['name'] . '】优惠券',
            ]);
            if (!$accountStatus) {
                return $this->stop(target('statis/Finance', 'service')->getError());
            }
        }
        $data = [
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'start_time' => time(),
            'end_time' => time() + $info['expiry_day'] * 86400,
        ];
        if (!target('marketing/MarketingCouponLog')->add($data)) {
            return $this->stop(target('marketing/MarketingCouponLog')->getError());
        }
        $status = target($this->_model)->edit([
            'coupon_id' => $couponId,
            'stock[-]' => 1,
            'receive[+]' => 1,
        ]);
        if (!$status) {
            return $this->stop(target($this->_model)->getError());
        }
        return $this->run([], '领券成功！');


    }


}