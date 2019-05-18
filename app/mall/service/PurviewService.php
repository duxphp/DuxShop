<?php

namespace app\mall\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return [
            'Content' => [
                'name' => '商品管理',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Class' => [
                'name' => '商品分类',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Order' => [
                'name' => '订单管理',
                'auth' => [
                    'index' => '列表',
                    'info' => '详情',
                ],
            ],
            'Comment' => [
                'name' => '订单管理',
                'auth' => [
                    'index' => '列表',
                    'status' => '状态',
                ],
            ],
            'SellRanking' => [
                'name' => '销售排行',
                'auth' => [
                    'index' => '信息',
                ],
            ],
            'SellList' => [
                'name' => '销售明细',
                'auth' => [
                    'index' => '信息',
                ],
            ],
        ];
    }


}
