<?php

namespace app\marketing\service;
/**
 * 权限接口
 */
class PurviewService {

    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return [
            'Coupon' => [
                'name' => '优惠券管理',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'CouponClass' => [
                'name' => '优惠券分类',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'CouponLog' => [
                'name' => '优惠券记录',
                'auth' => [
                    'index' => '列表',
                    'del' => '删除',
                ],
            ],
            'CouponGift' => [
                'name' => '新人赠券',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'CouponRec' => [
                'name' => '老带新赠券',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
        ];
    }


}
