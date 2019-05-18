<?php

namespace app\article\service;
/**
 * 类型接口
 */
class TypeService {


    public function getElementType() {
        return [
            'article' => [
                'name' => '文章列表',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'class' => 0,
                        'limit' => 4,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/article/view/admin/element/article'),
                'editTpl' => \dux\Dux::view()->fetch('app/article/view/admin/element/article_edit'),
                'process' => function($vo) {
                    $where = [
                        'status' => 1,
                    ];
                    if($vo['style']['class']) {
                        $where['class_id'] = explode(',', target('mall/MallClass')->getSubClassId($vo['style']['class']));
                    }
                    $mallList = target('article/Article')->where($where)->order('sort desc, article_id desc')->limit($vo['style']['limit'])->select();
                    $mallData = [];
                    foreach ($mallList as $vo) {
                        $mallData[] = [
                            'article_id' => $vo['article_id'],
                            'image' => $vo['image'] ? $vo['image'] : url('controller/tools/Placeholder/index', ['width' => 60, 'height' => 60], true),
                            'title' => $vo['title'],
                            'description' => $vo['description'],
                            'url' => '/pages/article/content?aid=' . $vo['article_id']
                        ];
                    }
                    return $mallData;
                }
            ],

            'notice' => [
                'name' => '滚动文章',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'class' => 0,
                        'limit' => 4,
                        'color' => '#333333',
                        'bgColor' => '#ffffff',
                        'icon' => ''
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/article/view/admin/element/notice'),
                'editTpl' => \dux\Dux::view()->fetch('app/article/view/admin/element/notice_edit'),
                'process' => function($vo) {
                    $where = [
                        'status' => 1,
                    ];
                    if($vo['style']['class']) {
                        $where['class_id'] = explode(',', target('mall/MallClass')->getSubClassId($vo['style']['class']));
                    }
                    $mallList = target('article/Article')->where($where)->order('sort desc, article_id desc')->limit($vo['style']['limit'])->select();
                    $mallData = [];
                    foreach ($mallList as $vo) {
                        $mallData[] = [
                            'article_id' => $vo['article_id'],
                            'image' => $vo['image'] ? $vo['image'] : url('controller/tools/Placeholder/index', ['width' => 60, 'height' => 60], true),
                            'title' => $vo['title'],
                            'description' => $vo['description'],
                            'url' => '/pages/article/content?aid=' . $vo['article_id']
                        ];
                    }
                    return $mallData;
                }
            ],

        ];
    }
}

