<?php

/**
 * 老带新人活动
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\marketing\admin;

class CouponRecAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MarketingCouponRec';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '老带新活动',
                'description' => '管理老带新赠券活动',
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
        return 'rec_id DESC';
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