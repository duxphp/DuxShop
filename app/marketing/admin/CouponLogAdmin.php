<?php

/**
 * 优惠券记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\marketing\admin;

class CouponLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MarketingCouponLog';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '领券管理',
                'description' => '优惠券领券记录',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'C.nickname,C.tel,C.email',
        ];
    }


    public function _indexAssign($pageMaps) {
        return [
            'typeList' => target('marketing/MarketingCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _addAssign() {
        return [
            'typeList' => target('marketing/MarketingCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _editAssign($info) {
        return [
            'typeList' => target('marketing/MarketingCoupon')->typeList(),
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function _indexMarketing() {
        return 'coupon_id desc';
    }

    protected function _delAfter($id) {
        target('marketing/MarketingCouponLog')->where([
            'coupon_id' => $id
        ])->delete();
    }

}