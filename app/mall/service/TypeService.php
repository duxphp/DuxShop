<?php

namespace app\mall\service;
/**
 * 类型接口
 */
class TypeService {

    /**
     * 优惠券接口
     */
    public function getCouponType() {
        return [
            'mall' => [
                'name' => '商品',
                'target' => 'mall/Mall',
                'type' => 1,
                'url' => function($couponId) {
                    return '/pages/goods/list?coupon_id=' . $couponId;
                }
            ],
            'class' => [
                'name' => '类目',
                'target' => 'mall/MallClass',
                'type' => 2,
                'url' => function($couponId) {
                    return '/pages/goods/list?coupon_id=' . $couponId;
                }
            ],
        ];
    }

    public function getElementType() {
        return [
            'goods' => [
                'name' => '商品列表',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'class' => 0,
                        'order' => 0,
                        'attr' => 0,
                        'limit' => 4,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/goods'),
                'editTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/goods_edit'),
                'process' => function ($vo) {
                    $time = time();
                    $where = [
                        'status' => 1,
                        '_sql' => "(up_time = 0 OR up_time <= {$time}) AND (down_time = 0 OR down_time >= {$time})",
                    ];
                    if ($vo['style']['class']) {
                        $where['class_id'] = explode(',', target('mall/MallClass')->getSubClassId($vo['style']['class']));
                    }
                    switch ($vo['style']['attr']) {
                    case 1:
                        $where['attr_new'] = 1;
                        break;
                    case 2:
                        $where['attr_hot'] = 1;
                        break;
                    case 3:
                        $where['attr_rec'] = 1;
                        break;
                    case 4:
                        $where['attr_free'] = 1;
                        break;
                    }
                    $order = 'sort desc, mall_id desc';
                    switch ($vo['style']['order']) {
                    case 1:
                        $order = 'sale desc';
                        break;
                    case 2:
                        $order = 'sell_price desc';
                        break;
                    case 3:
                        $order = 'sell_price asc';
                        break;
                    }
                    $mallList = target('mall/Mall')->where($where)->order($order)->limit($vo['style']['limit'])->select();
                    $mallData = [];
                    foreach ($mallList as $vo) {
                        $mallData[] = [
                            'mall_id' => $vo['mall_id'],
                            'image' => $vo['image'],
                            'title' => $vo['title'],
                            'sell_price' => $vo['sell_price'],
                            'sale' => $vo['sale'],
                            'url' => '/pages/goods/detail?mid=' . $vo['mall_id'],
                        ];
                    }
                    return $mallData;
                }
            ],
            'buytips' => [
                'name' => '购物提示',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'limit' => 10,
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/buytips'),
                'editTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/buytips_edit'),
                'data' => function ($vo, $userId) {
                    $list = target('order/OrderGoods')
                    ->table('order_goods(A)')
                    ->join('order(B)', ['A.user_id', 'B.order_user_id'])
                    ->field(['A.goods_name', 'A.goods_qty' , 'A.goods_unit', 'B.receive_name'])
                    ->limit($vo['style']['limit'])
                    ->select();
                    $data = [];
                    foreach ($list as $key => $value) {
                        $data[] = $value['receive_name'] . ' 购买 ' . $value['goods_name'] . ' ' . $value['goods_qty'] . $value['goods_unit'];
                    }
                    return [
                        'buyer' => [
                            'data' => $data,
                        ],
                    ];
                }
            ],
            'quick' => [
                'name' => '快速购买',
                'default' => [
                    'data' => [
                    ],
                    'style' => [
                        'class' => 0,
                        'order' => 0,
                        'attr' => 0,
                        'limit' => 4,
                        'icon' => '',
                        'text' => '立即结算',
                    ],
                ],
                'phoneTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/quick'),
                'editTpl' => \dux\Dux::view()->fetch('app/mall/view/admin/element/quick_edit'),
                'data' => function ($vo, $userId) {
                    $cart = target('order/Cart', 'service')->getCart($userId);
                    $cart['list'] = $cart['list'] ? array_values($cart['list']) : [];
                    return [
                        'cart' => [
                            'style' => [
                                'icon' => $vo['style']['icon'],
                                'text' => $vo['style']['text'],
                            ],
                            'data' => $cart,
                        ],
                    ];
                },
                'process' => function ($vo, $userId) {
                    $time = time();
                    $where = [
                        'status' => 1,
                        '_sql' => "(up_time = 0 OR up_time <= {$time}) AND (down_time = 0 OR down_time >= {$time})",
                    ];
                    if ($vo['style']['class']) {
                        $where['class_id'] = explode(',', target('mall/MallClass')->getSubClassId($vo['style']['class']));
                    }
                    switch ($vo['style']['attr']) {
                    case 1:
                        $where['attr_new'] = 1;
                        break;
                    case 2:
                        $where['attr_hot'] = 1;
                        break;
                    case 3:
                        $where['attr_rec'] = 1;
                        break;
                    case 4:
                        $where['attr_free'] = 1;
                        break;
                    }
                    $order = 'sort desc, mall_id desc';
                    switch ($vo['style']['order']) {
                    case 1:
                        $order = 'sale desc';
                        break;
                    case 2:
                        $order = 'sell_price desc';
                        break;
                    case 3:
                        $order = 'sell_price asc';
                        break;
                    }
                    $mallList = target('mall/Mall')->where($where)->order($order)->limit($vo['style']['limit'])->select();
                    $mallData = [];
                    $mallIds = [];
                    foreach ($mallList as $vo) {
                        $mallIds[] = $vo['mall_id'];
                        $specData = unserialize($vo['spec_data']);
                        if (!empty($specData)) {
                            foreach ($specData as $k => $v) {
                                $specData[$k]['value'] = explode(',', $v['value']);
                            }
                        }
                        $mallData[$vo['mall_id']] = [
                            'mall_id' => $vo['mall_id'],
                            'image' => $vo['image'],
                            'title' => $vo['title'],
                            'market_price' => $vo['market_price'],
                            'sell_price' => $vo['sell_price'],
                            'sale' => $vo['sale'],
                            'unit' => $vo['unit'],
                            'url' => '/pages/goods/detail?mid=' . $vo['mall_id'],
                            'spec_data' => $specData ? $specData : [],
                            'cart_qty' => 0,
                            'spec' => [],
                        ];
                    }
                    if (!empty($mallIds)) {
                        $cart = target('order/Cart', 'middle')->setParams([
                            'user_id' => $userId,
                        ])->data()->export(function ($data) {
                            return $data;
                        }, function ($message, $code) {
                            return [];
                        });
                        $cartList = $cart['list'];

                        $cartData = [];
                        if ($cartList) {
                            foreach ($cartList as $vo) {
                                $cartData[$vo['id']] = [
                                    'qty' => $vo['qty'],
                                    'rowid' => $vo['rowid']
                                ];
                            }
                        }

                        $proList = target('mall/MallProducts')->where([
                            'mall_id' => $mallIds,
                        ])->select();

                        foreach ($proList as $key => $vo) {
                            $specData = unserialize($vo['spec_data']);
                            $k = [];
                            if (!empty($specData)) {
                                foreach ($specData as $v) {
                                    $k[] = $v['id'] . ':' . $v['value'];
                                }
                            }
                            $k = implode(',', $k);
                            $vo['spec_data'] = $specData;
                            $vo['cart_qty'] = intval($cartData[$vo['products_id']]['qty']);
                            $vo['cart_id'] = $cartData[$vo['products_id']]['rowid'];
                            $mallData[$vo['mall_id']]['cart_qty'] += intval($cartData[$vo['products_id']]['qty']);
                            $mallData[$vo['mall_id']]['spec'][$k] = $vo;
                        }
                    }

                    return (array) array_values($mallData);
                }
            ],
        ];
    }
}
