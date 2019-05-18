<?php

namespace app\site\service;
/**
 * 类型接口
 */
class TypeService {

    /**
     * 元素接口
     */
    public function getElementType() {
        return [
            'text' => [
                'name' => '文本',
                'default' => [
                    'data' => [
                        'content' => '这是一段默认文本',
                        'url' => 'http://www.moupu.com',
                    ],
                    'style' => [
                        'fontSize' => 32,
                        'lineHeight' => 32,
                        'textAlign' => 'center',
                        'bgColor' => "#f5f5f5",
                        'color' => "#333333",
                        'lrPadding' => 10,
                        'tbPadding' => 20,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/text'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/text_edit'),
            ],
            'swiper' => [
                'name' => '轮播图',
                'default' => [
                    'data' => [
                        [
                            'image' => '',
                            'text' => '',
                            'url'   => ''
                        ]
                    ],
                    'style' => [
                        'indicatorDots' => true,
                        'autoplay' => true,
                        'interval' => 5000,
                        'vertical' => false,
                        'height' => 360,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/swiper'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/swiper_edit'),
                'editJs' => file_get_contents(ROOT_PATH . 'app/site/view/admin/element/swiper.js'),
                'process' => function($data) {
                    $newData = [];
                    foreach($data['data'] as $vo) {
                        $newData[] = $vo;
                    }
                    return $newData;
                }
            ],
            'empty' => [
                'name' => '占位符',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'height' => 20,
                        'bgColor' => '#ffffff',
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/empty'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/empty_edit'),
            ],
            'search' => [
                'name' => '搜索框',
                'default' => [
                    'data' => [
                        'placeholder' => '请输入开始搜索'
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/search'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/search_edit'),
            ],
            'menu' => [
                'name' => '图片菜单',
                'default' => [
                    'data' => [
                        [
                            'image' => '',
                            'url' => '',
                            'text' => '',
                        ]
                    ],
                    'style' => [
                        'column' => 4,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/menu'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/menu_edit'),
                'editJs' => file_get_contents(ROOT_PATH . 'app/site/view/admin/element/menu.js'),
                'process' => function($data) {
                    $newData = [];
                    foreach($data['data'] as $vo) {
                        $newData[] = $vo;
                    }
                    return (array)$newData;
                }
            ],
            'image' => [
                'name' => '图片',
                'default' => [
                    'data' => [
                        'image' => '',
                        'url' => '',
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/image'),
                'editTpl' => \dux\Dux::view()->fetch('app/site/view/admin/element/image_edit'),
            ],

        ];
    }
}

