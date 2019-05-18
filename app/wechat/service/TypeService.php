<?php

namespace app\wechat\service;
/**
 * 类型接口
 */
class TypeService {

    public function getElementType() {
        return [
            'focus' => [
                'name' => '公众号关注',
                'default' => [
                    'data' => [
                        'name' => '公众号名称',
                        'desc' => '公众号描述',
                        'logo' => '',
                        'qrcode' => '',
                        'url' => '',
                    ],
                    'style' => [
                        'float' => 1,
                    ],
                ],
                'hidden' => function () {
                    $userId = intval($_SERVER['HTTP_AUTHUID']);
                    if ($userId) {
                        $info = target('member/MemberConnect')->getWhereInfo([
                            'user_id' => $userId,
                            'type' => 'wechat',
                        ]);
                        if (empty($info)) {
                            return false;
                        }
                        $target = target('wechat/Wechat', 'service');
                        $target->init();
                        $wechat = $target->wechat();
                        $info = $wechat->user->get($info['open_id']);
                        if ($info['subscribe']) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    return false;
                },
                'phoneTpl' => \dux\Dux::view()->fetch('app/wechat/view/admin/element/focus'),
                'editTpl' => \dux\Dux::view()->fetch('app/wechat/view/admin/element/focus_edit'),
            ],
            'service' => [
                'name' => '客服',
                'default' => [
                    'data' => [
                        'url' => '',
                    ],
                    'style' => [
                        'icon' => '',
                        'type' => 'left',
                        'distance' => 10,
                        'top' => 10,
                        'size' => 30,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/wechat/view/admin/element/service'),
                'editTpl' => \dux\Dux::view()->fetch('app/wechat/view/admin/element/service_edit'),
                'data' => function ($vo, $userId) {
                    return [
                        'service' => $vo,
                    ];
                }
            ],
        ];
    }
}
