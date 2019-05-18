<?php

namespace app\shop\service;
/**
 * 筛选服务
 */
class FilterService {

    private $urlParam = [];
    private $order = '';
    private $brand = 0;
    private $price = [];
    private $model = '';
    private $table = '';

    public function setUrlParam($key, $val) {
        $this->urlParam[$key] = $val;
    }

    public function getData($app) {
        $this->model = $app . '/' . $app;

        $where = [];

        if (!isset($_GET['price'])) {
            $minPrice = request('get', 'min_price');
            $maxPrice = request('get', 'max_price');
            if (isset($_GET['min_price']) && isset($_GET['max_price'])) {
                $_GET['price'] = $minPrice . '_' . $maxPrice;
            }
        }

        $priceAttr = $this->getUrlPrice();
        $minPrice = $priceAttr[0];
        $maxPrice = $priceAttr[1];
        $this->urlParam['min_price'] = $minPrice;
        $this->urlParam['max_price'] = $maxPrice;
        if ($minPrice) {
            $where['_sql'][] = 'B.sell_price >=' . $minPrice;
        }
        if ($maxPrice) {
            $where['_sql'][] = 'B.sell_price <=' . $maxPrice;
        }

        $where['A.status'] = 1;
        $time = time();
        $where['_sql'] = "(A.up_time = 0 OR A.up_time <= {$time}) AND (A.down_time = 0 OR A.down_time >= {$time})";

        $brandId = $this->getUrlBrand();
        if ($brandId) {
            $where['B.brand_id'] = $brandId;
        }

        //排序条件
        switch ($this->getUrlOrder()) {
            case 'sale' :
                $order = 'A.sale desc';
                break;
            case 'sale_toggle':
                $order = 'A.sale asc';
                break;
            case 'price' :
                $order = 'A.sell_price desc';
                break;
            case 'price_toggle':
                $order = 'A.sell_price asc';
                break;
            case 'new_toggle':
                $order = 'A.create_time desc';
                break;
            default :
            case 'new' :
                $order = 'A.sort desc, A.create_time asc';
                break;
        };

        return [
            'where' => $where,
            'order' => $order,
        ];
    }

    private function getUrlOrder() {
        if (!empty($this->order)) {
            return $this->order;
        }
        $order = request('', 'order');
        $orderAttr = ['sale', 'sale_toggle', 'price', 'price_toggle', 'new', 'new_toggle', 'rebate', 'rebate_toggle'];

        if (!in_array($order, $orderAttr)) {
            $order = 'default';
        }
        $this->order = $order;
        return $order;
    }

    public function getOrderData() {
        $order = $this->getUrlOrder();
        return [
            'default' => [
                'name' => '默认',
                'url' => $this->getUrl([
                    'order' => '',
                ]),
                'single_url' => $this->getUrl([
                    'order' => '',
                ]),
                'up' => false,
                'down' => false,
                'cur' => $order == 'default' ? true : false,
            ],
            'new' => [
                'name' => '新品',
                'url' => $this->getUrl([
                    'order' => ($order == 'new') ? 'new_toggle' : 'new',
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'new') ? '' : 'new',
                ]),
                'up' => ($order == 'new') ? true : false,
                'down' => ($order == 'new_toggle') ? true : false,
                'cur' => ($order == 'new' || $order == 'new_toggle') ? true : false,
            ],
            'sale' => [
                'name' => '销量',
                'url' => $this->getUrl([
                    'order' => ($order == 'sale') ? 'sale_toggle' : 'sale',
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'sale') ? '' : 'sale',
                ]),
                'up' => ($order == 'sale') ? true : false,
                'down' => ($order == 'sale_toggle') ? true : false,
                'cur' => ($order == 'sale' || $order == 'new_toggle') ? true : false,
            ],
            'price' => [
                'name' => '价格',
                'url' => $this->getUrl([
                    'order' => ($order == 'price') ? 'price_toggle' : 'price',
                ]),
                'single_url' => $this->getUrl([
                    'order' => ($order == 'price') ? '' : 'price',
                ]),
                'up' => ($order == 'price') ? true : false,
                'down' => ($order == 'price_toggle') ? true : false,
                'cur' => ($order == 'price' || $order == 'price_toggle') ? true : false,
            ],
        ];
    }

    private function getUrlBrand() {
        if (!empty($this->brand)) {
            return $this->brand;
        }
        $brand = intval(request('', 'brand'));
        $this->brand = $brand;
        return $this->brand;
    }

    public function getBrandData() {
        $brandList = target($this->model)->field(['brand_id'])->select();

        if (empty($brandList)) {
            return [];
        }
        $brandIds = [];
        foreach ($brandList as $brand) {
            if ($brand['brand_id']) {
                $brandIds[] = $brand['brand_id'];
            }
        }
        $brandIds = array_unique($brandIds);
        if (empty($brandIds)) {
            return [];
        }
        $list = target('shop/ShopBrand')->where([
            '_sql' => 'brand_id IN(' . implode(',', $brandIds) . ')',
        ])->select();
        if (empty($list)) {
            return [];
        }
        $brandId = $this->getUrlBrand();

        foreach ($list as $key => $vo) {
            $list[$key]['url'] = $this->getUrl(['brand' => $vo['brand_id']]);
            if ($brandId == $vo['brand_id']) {
                $list[$key]['cur'] = true;
            } else {
                $list[$key]['cur'] = false;
            }
        }
        array_unshift($list, [
            'brand_id' => '0',
            'name' => '不限',
            'cur' => $brandId ? false : true,
            'url' => $this->getUrl(['brand' => '']),
        ]);

        return $list;
    }

    private function getUrlPrice() {
        if (!empty($this->price)) {
            return $this->price;
        }
        $priceAttrData = request('get', 'price');
        $priceAttr = explode('_', $priceAttrData, 2);
        $minPrice = intval($priceAttr[0]);
        $maxPrice = intval($priceAttr[1]);
        $this->price = [$minPrice, $maxPrice];
        return $this->price;
    }

    public function getPriceData() {
        $showPriceNum = 5;
        $goodsPrice = target($this->model)->field(['MIN(sell_price)(min)', 'MAX(sell_price)(max)'])->find();

        if ($goodsPrice['min'] < 0) {
            return [];
        }
        $minPrice = ceil($goodsPrice['min']);

        //商品价格计算
        $result = ['0~' . $minPrice];
        $perPrice = floor(($goodsPrice['max'] - $minPrice) / ($showPriceNum - 1));
        if ($perPrice > 0) {
            for ($addPrice = $minPrice + 1; $addPrice < $goodsPrice['max'];) {
                $stepPrice = $addPrice + $perPrice;
                $stepPrice = substr(intval($stepPrice), 0, 1) . str_repeat('9', (strlen(intval($stepPrice)) - 1));
                $result[] = $addPrice . '~' . $stepPrice;
                $addPrice = $stepPrice + 1;
            }
        }

        $priceAttr = $this->getUrlPrice();
        $minPrice = $priceAttr[0];
        $maxPrice = $priceAttr[1];
        if ($priceAttr[0] >= $minPrice && $priceAttr[1] <= $maxPrice) {
            $cur = true;
        } else {
            $cur = false;
        }
        $priceList = [];
        $priceList[] = [
            'name' => '不限',
            'url' => $this->getUrl(['price' => '']),
            'value' => '0_0',
            'cur' => $cur,
        ];
        foreach ($result as $key => $vo) {
            $arr = explode('~', $vo);
            if (($minPrice || $maxPrice) && ($minPrice >= $arr[0] && $maxPrice <= $arr[1])) {
                $cur = true;
            } else {
                $cur = false;
            }
            $priceList[] = [
                'name' => $vo,
                'value' => $arr[0] . '_' . $arr[1],
                'url' => $this->getUrl(['price' => $arr[0] . '_' . $arr[1]]),
                'cur' => $cur,
            ];
        }
        return $priceList;
    }

    private function getUrl($urlParam = []) {
        return url('index', array_filter(array_merge($this->urlParam, $urlParam)));
    }
}

