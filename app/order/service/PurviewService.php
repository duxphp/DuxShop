<?php

namespace app\order\service;
/**
 * 权限接口
 */
class PurviewService {
    /**
     * 获取模块权限
     */
    public function getSystemPurview() {
        return [
            'Config' => [
                'name' => '订单设置',
                'auth' => [
                    'index' => '订单设置',
                ],
            ],
            'ConfigExpress' => [
                'name' => '物流设置',
                'auth' => [
                    'index' => '列表',
                ],
            ],
            'ConfigPrinter' => [
                'name' => '打印机设置',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'ConfigDelivery' => [
                'name' => '运费模板',
                'auth' => [
                    'index' => '列表',
                ],
            ],
            'ConfigWaybill' => [
                'name' => '物流接口',
                'auth' => [
                    'index' => '列表',
                    'setting' => '配置',
                ],
            ],
            'Parcel' => [
                'name' => '配货管理',
                'auth' => [
                    'index' => '列表',
                    'print' => '打印',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Delivery' => [
                'name' => '发货管理',
                'auth' => [
                    'index' => '列表',
                    'print' => '打印',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Receipt' => [
                'name' => '收款管理',
                'auth' => [
                    'index' => '列表',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Comment' => [
                'name' => '评论管理',
                'auth' => [
                    'index' => '列表',
                ],
            ],
            'Refund' => [
                'name' => '退款管理',
                'auth' => [
                    'index' => '列表',
                    'info' => '详情',
                ],
            ],
            'Take' => [
                'name' => '自提点管理',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'Invoice' => [
                'name' => '发票管理',
                'auth' => [
                    'index' => '列表',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'InvoiceClass' => [
                'name' => '发票分类',
                'auth' => [
                    'index' => '列表',
                    'add' => '添加',
                    'edit' => '编辑',
                    'status' => '状态',
                    'del' => '删除',
                ],
            ],
            'OrderStatis' => [
                'name' => '订单统计',
                'auth' => [
                    'index' => '信息',
                ],
            ],
        ];
    }


}
