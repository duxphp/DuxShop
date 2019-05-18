<?php

/**
 * 商品管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class ContentAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Mall';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商品管理',
                'description' => '管理商城中的商品信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'class_id' => 'B.class_id',
            'keyword' => 'A.title,A.goods_no',
            'status' => 'status',
            'order' => 'order',
            'supply_id' => 'B.supply_id',
            'brand_id' => 'B.brand_id',
        ];
    }

    protected function _indexWhere($whereMaps) {
        if (isset($whereMaps['status'])) {
            $whereMaps['A.status'] = $whereMaps['status'];
        }
        unset($whereMaps['order']);
        unset($whereMaps['status']);
        return $whereMaps;
    }

    public function _indexOrder() {
        $order = 'A.mall_id desc';
        switch ($_GET['order']) {
            case 1:
                $order = 'A.sell_price desc';
                break;
            case 2:
                $order = 'A.sell_price asc';
                break;
            case 3:
                $order = 'A.sale desc';
                break;
            case 4:
                $order = 'A.sale asc';
                break;
            case 5:
                $order = 'A.store desc';
                break;
            case 6:
                $order = 'A.store asc';
                break;
            case 7:
                $order = 'A.view desc';
                break;
            case 8:
                $order = 'A.view asc';
                break;

        }
        return $order;
    }


    public function _indexAssign($pageMaps) {
        return [
            'classList' => target('mall/MallClass')->loadTreeList(),
            'brandList' => target('shop/ShopBrand')->loadList(),
            'supplierList' => target('warehouse/WarehouseSupplier')->loadList(),
            'hookMenu' => [
                [
                    'name' => '全部',
                    'url' => url('index'),
                    'cur' => !isset($pageMaps['status']),
                ],
                [
                    'name' => '已上架',
                    'url' => url('index', ['status' => 1]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 1,
                ],
                [
                    'name' => '已下架',
                    'url' => url('index', ['status' => 0]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 0,
                ],
            ],
        ];
    }

    public function _indexData($where, $limit, $order) {
        $list = target($this->_model)->loadList($where, $limit, $order);
        $classList = target('mall/MallClass')->loadList();
        foreach ($list as $key => $vo) {
            $cat = new \dux\lib\Category(['class_id', 'parent_id', 'name', 'cname']);
            $pathData = $cat->getPath($classList, $vo['class_id']);
            $path = [];
            foreach ($pathData as $v) {
                $path[] = $v['name'];
            }
            $list[$key]['class_path'] = implode(' > ', $path);
        }
        return $list;
    }

    public function menu() {
        return [
            [
                'name' => '基础信息',
                'url' => 'javascript:;',
                'cur' => true,
            ],
            [
                'name' => '商品参数',
                'url' => 'javascript:;',
                'cur' => false,
            ],
            [
                'name' => '商品详情',
                'url' => 'javascript:;',
                'cur' => false,
            ],
        ];
    }

    public function _addAssign() {
        $classId = request('get', 'class_id', 0, 'intval');

        //运费模板
        $deliveryList = target('order/OrderConfigDelivery')->loadList([], 0, 'delivery_id asc');


        return [
            'hookMenu' => $this->menu(),
            'classList' => target('mall/MallClass')->loadTreeList(),
            'posList' => target('site/SitePosition')->loadList(),
            'brandList' => target('shop/ShopBrand')->loadList(),
            'deliveryList' => $deliveryList,
            'classId' => $classId,
            'proDataJson' => json_encode([]),
            'proHeadJson' => json_encode([]),
            'specJson' => json_encode([]),
            'productNo' => \app\shop\unit\SequenceNumber::get(time(), 12, 'D'),
            'supplierList' => target('warehouse/WarehouseSupplier')->loadList(),
        ];
    }

    public function _editAssign($info) {
        $classId = intval($info['class_id']);
        //运费模板
        $deliveryList = target('order/OrderConfigDelivery')->loadList([], 0, 'delivery_id asc');
        //重组产品数据
        $proData = [];
        $proHead = [];
        $headStatus = false;
        $proList = target('mall/MallProducts')->loadList(['A.mall_id' => $info['mall_id']], 0, 'products_id asc');
        foreach ($proList as $key => $vo) {
            $specData = $vo['spec_data'];
            if (!$headStatus) {
                if (!empty($specData)) {
                    foreach ($specData as $k => $v) {
                        $proHead[$k] = [
                            'id' => $v['id'],
                            'name' => $v['name'],
                        ];
                    }
                }
                $headStatus = true;
            }
            $proData['spec_list'][$key] = $specData;
            $proData['id'][$key] = $vo['products_id'];
            $proData['goods_no'][$key] = $vo['products_no'];
            $proData['barcode'][$key] = $vo['barcode'];
            $proData['sell_price'][$key] = $vo['sell_price'];
            $proData['market_price'][$key] = $vo['market_price'];
            $proData['cost_price'][$key] = $vo['cost_price'];
            $proData['min_num'][$key] = $vo['min_num'];
            $proData['store'][$key] = $vo['store'];
            $proData['weight'][$key] = $vo['weight'];
            $proData['give_point'][$key] = $vo['give_point'];
        }
        return [
            'hookMenu' => $this->menu(),
            'classList' => target('mall/MallClass')->loadTreeList(),
            'posList' => target('site/SitePosition')->loadList(),
            'brandList' => target('shop/ShopBrand')->loadList(),
            'deliveryList' => $deliveryList,
            'classId' => $classId,
            'proDataJson' => json_encode($proData),
            'proHeadJson' => json_encode($proHead),
            'specJson' => json_encode($info['spec_data']),
            'productNo' => \app\shop\unit\SequenceNumber::get(time(), 12, 'D'),
            'supplierList' => target('warehouse/WarehouseSupplier')->loadList(),
        ];
    }

    protected function _indexUrl() {
        return url('index', ['class_id' => request('post', 'class_id')]);
    }

    public function data() {
        $keyword = request('', 'keyword');
        $id = request('', 'id');
        $where = [];
        $where['A.status'] = 1;
        if ($keyword) {
            $where['A.title[~]'] = $keyword;
        }
        if($id) {
            $where['A.mall_id'] = explode(',', $id);
        }
        $listLimit = 20;
        $model = target($this->_model);
        $count = $model->countList($where);
        $pageObj = new \dux\lib\Pagination($count, request('', 'page'), $listLimit);
        $pageData = $pageObj->build();
        $limit = [$pageData['offset'], $listLimit];
        $list = $model->loadList($where, $limit, 'mall_id desc');
        $this->success([
            'list' => $list,
            'pageData' => $pageData,
        ]);
    }


}