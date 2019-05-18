<?php

/**
 * 商品列表
 */

namespace app\mall\middle;

class ListMiddle extends \app\base\middle\BaseMiddle {

    protected $crumb = [];
    protected $classInfo = [];
    protected $listWhere = [];
    protected $listOrder = [];
    protected $listLimit = 20;
    protected $listModel = 0;
    private $tpl = '';

    private function getClass() {
        if ($this->classInfo) {
            return $this->classInfo;
        }
        $classId = $this->params['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->classInfo = target('mall/MallClass')->getInfo($classId);

        return $this->classInfo;
    }

    private function getCrumb() {
        if ($this->crumb) {
            return $this->crumb;
        }
        $classId = $this->params['class_id'];
        if (empty($classId)) {
            return [];
        }
        $this->crumb = target('mall/MallClass')->loadCrumbList($classId);

        return $this->crumb;
    }

    protected function classInfo() {
        $this->classInfo = $this->getClass();
        if (empty($this->classInfo)) {
            return $this->run([
                'classInfo' => $this->classInfo,
                'parentClassInfo' => [],
                'topClassInfo' => [],
            ]);
        }
        if ($this->classInfo['url']) {
            $this->data['url'] = $this->classInfo['url'];

            return $this->stop('跳转', 302, $this->classInfo['url']);
        }
        $this->crumb = $this->getCrumb();
        $parentClassInfo = array_slice($this->crumb, -2, 1);
        if (empty($parentClassInfo)) {
            $parentClassInfo = $this->crumb[0];
        } else {
            $parentClassInfo = $parentClassInfo[0];
        }
        $topClassInfo = $this->crumb[0];

        if ($this->classInfo['tpl_class']) {
            $this->tpl = $this->classInfo['tpl_class'];
        }

        return $this->run([
            'classInfo' => $this->classInfo,
            'parentClassInfo' => $parentClassInfo,
            'topClassInfo' => $topClassInfo,
            'tpl' => $this->tpl,
        ]);
    }


    protected function meta($title = '全部商品', $name = '全部商品', $url = '') {
        $classId = $this->params['class_id'];
        $coupon = $this->params['coupon'];
        if ($classId) {
            $this->crumb = $this->getCrumb();
            $this->classInfo = $this->getClass();
            $this->setMeta($this->classInfo['name'], $this->classInfo['keyword'], $this->classInfo['description']);
            $this->setCrumb($this->crumb);
        } else if ($coupon) {
            $this->setName('优惠券商品');
            $this->setMeta('优惠券商品');
            $this->setCrumb([
                [
                    'name' => '优惠券商品',
                    'url' => URL,
                ],
            ]);
        } else {
            $this->setName($name ? $name : '全部商品');
            $this->setMeta($title ? $title : '全部商品');
            $this->setCrumb([
                [
                    'name' => $name,
                    'url' => $url ? $url : url(),
                ],
            ]);
        }

        return $this->run([
            'pageInfo' => $this->pageInfo,
        ]);
    }

    private $filter = [];

    protected function data() {
        $classId = $this->params['class_id'];

        $keyword = str_len(html_clear(urldecode($this->params['keyword'])), 10, false);
        $listLimit = $this->params['limit'] ? $this->params['limit'] : 20;
        $couponId = $this->params['coupon_id'];
        $where = [];
        if ($classId) {
            $this->classInfo = $this->getClass();
            if (empty($this->classInfo)) {
                return $this->stop('栏目不存在!');
            }
            $classIds = target('mall/MallClass')->getSubClassId($classId);
            if ($classIds) {
                $where['A.class_id'] = explode(',', $classIds);
            }
        }
        if ($couponId) {
            $couponInfo = target('marketing/MarketingCoupon')->getInfo($couponId);
            if ($couponInfo['has_id']) {

                if($couponInfo['type'] == 'mall') {
                    $where['A.mall_id'] = explode(',', $couponInfo['has_id']);
                }

                if($couponInfo['type'] == 'class') {
                    $where['A.class_id'] = explode(',', $couponInfo['has_id']);
                }
            }
        }
        if ($keyword) {
            $where['A.title[~]'] = $keyword;
            target('site/SiteSearch')->stats($keyword, APP_NAME);
        }
        $filter = target('shop/Filter', 'service')->getData('mall');
        $where = array_merge($where, $filter['where']);
        $model = target('mall/Mall');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $listLimit);
        $list = $model->loadList($where, $pageData['limit'], $filter['order']);
        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }

    protected function filter() {
        $service = target('shop/Filter', 'service');
        $priceList = $service->getPriceData();
        $brandList = $service->getBrandData();
        return $this->run([
            'pageParams' => $this->filter['urlParam'],
            'attrList' => $this->filter['attrList'],
            'priceList' => $priceList,
            'brandList' => $brandList,
            'orderList' => $service->getOrderData(),
        ]);
    }

    protected function quick() {
        $classList = target('mall/MallClass')->loadList();
        $userId = intval($this->params['user_id']);
        $cat = new \dux\lib\Category(['class_id', 'parent_id', 'name', 'cname']);
        $topList = target('mall/MallClass')->loadList(['status' => 1, 'parent_id' => 0]);
        foreach ($topList as $key => $vo) {
            $parentClass = $cat->getChild($vo['class_id'], $classList);
            foreach ($parentClass as $k => $v) {
                $subClass = $cat->getChild($v['class_id'], $classList);
                $parentClass[$k]['sub_ids'] = array_column($subClass, 'class_id');
            }
            $topList[$key]['sub_class'] = $parentClass;
        }
        $mallList = target('mall/Mall')->loadList(['A.status' => 1]);
        $data = [];
        $mallIds = array_column($mallList, 'mall_id');
        $mallData = [];
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
            $proList = target('mall/MallProducts')->select();
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

        foreach ($mallList as $key => $vo) {
            $specData = unserialize($vo['spec_data']);
            if (!empty($specData)) {
                foreach ($specData as $k => $v) {
                    $specData[$k]['value'] = explode(',', $v['value']);
                }
            }
            $vo['cart_qty'] = $mallData[$vo['mall_id']]['cart_qty'];
            $vo['spec'] = $mallData[$vo['mall_id']]['spec'];
            $vo['spec_data'] = $specData ? $specData : [];
            $data[$vo['class_id']][] = $vo;
        }
        foreach ($topList as $key => $top) {
            foreach ($top['sub_class'] as $k => $v) {
                $subMall = [];
                $subMall = array_merge($subMall, (array)$data[$v['class_id']]);
                foreach ($v['sub_ids'] as $id) {
                    $subMall = array_merge($subMall, (array)$data[$id]);
                }
                $subMall = array_sort($subMall, 'A.update_time desc, A.create_time');
                $topList[$key]['sub_class'][$k]['mall_list'] = $subMall;
            }
        }
        $cart = target('order/Cart', 'service')->getCart($userId);
        $cart['list'] = $cart['list'] ? array_values($cart['list']) : [];
        return $this->run([
            'mallList' => $topList,
            'cartData' => $cart
        ]);
    }
}