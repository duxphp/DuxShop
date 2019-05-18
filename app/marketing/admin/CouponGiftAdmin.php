<?php

/**
 * 新人券活动
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\marketing\admin;

class CouponGiftAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MarketingCouponGift';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '新人赠券',
                'description' => '管理新用户注册赠券',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name'
        ];
    }

    public function _indexOrder() {
        return 'gift_id DESC';
    }

    public function _addAssign() {
        return [
            'couponList' => target('marketing/MarketingCoupon')->loadList()
        ];
    }

    public function _editAssign($info) {
        return [
            'couponList' => target('marketing/MarketingCoupon')->loadList()
        ];
    }

    public function _indexPage() {
        return 100;
    }

}