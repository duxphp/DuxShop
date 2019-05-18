<?php

/**
 * 销售统计
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;


class SellRankingAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'mall';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '销售排行',
                'description' => '商品销售排行榜',
            ],
            'fun' => [
                'index' => true,
            ],
        ];
    }

    public function index() {
        $keyword = request('', 'keyword');
        $startTime = request('', 'start_time', 0);
        $stopTime = request('', 'stop_time', 0);
        $order = request('', 'order');

        if (empty($startTime)) {
            $startTime = date('Y-m-d', strtotime('-30 day'));

        }
        if (empty($stopTime)) {
            $stopTime = date('Y-m-d', time());
        }
        $pageMaps = [
            'keyword' => $keyword,
            'start_time' => $startTime,
            'stop_time' => $stopTime,
            'order' => $order,
        ];

        switch ($order) {
            case 2:
                $orderSql = 'total_money desc';
                break;
            case 3:
                $orderSql = 'total_view desc';
                break;
            default:
            case 1:
                $orderSql = 'total_num desc';
                break;
        }


        if ($startTime) {
            $startTime = strtotime($startTime);
        }
        if ($stopTime) {
            $stopTime = strtotime($stopTime . ' 23:59:59');
        }

        $where = [];

        if($keyword) {
            $where['OR'] = [
                'title[~]' => $keyword,
                'goods_no' => $keyword,
            ];
        }

        $limit = 20;
        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $limit);


        $list = $model->table('mall(A)')
            ->field([
                'A.*',
                'IFNULL((SELECT SUM(order_goods.goods_qty) as sale_num FROM {pre}order_goods as order_goods INNER JOIN {pre}order as `order` ON order_goods.order_id = order.order_id WHERE order.order_app = "mall" AND order_goods.has_id = A.mall_id AND order.order_status = 1 AND order.pay_status = 1 AND pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime . ' limit 1), 0) as total_num',
                'IFNULL((SELECT SUM(order_goods.price_total) as sale_money FROM {pre}order_goods as order_goods INNER JOIN {pre}order as `order` ON order_goods.order_id = order.order_id WHERE order.order_app = "mall" AND order_goods.has_id = A.mall_id AND order.order_status = 1 AND order.pay_status = 1 AND pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime . ' limit 1), 0.00) as total_money',
                '(SELECT SUM(num) as num FROM {pre}statis_views WHERE species = "mall" AND has_id = A.mall_id AND date >= ' . date('Ymd', $startTime) . ' AND date <= ' . date('Ymd', $stopTime) . ' limit 1) as total_view',
            ])
            ->where($where)
            ->limit($pageData['limit'])
            ->order($orderSql)
            ->select();

        $page = request('', 'page', 1, 'intval');
        $page = $page - 1;

        foreach ($list as $key => $vo) {
            if ($vo['total_view']) {
                $list[$key]['conver'] = round($vo['total_num'] / $vo['total_view'] * 100, 2);
            } else {
                $list[$key]['conver'] = 0;
            }
            $list[$key]['ranking'] = $page * $limit + $key + 1;
        }

        $this->assign('list', $list);
        $this->assign('page', $pageData['html']);
        $this->assign('pageMaps', $pageMaps);

        $dateParams = [
            [
                'start_time' => date('Y-m-d', time()),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-7 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-15 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-30 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
        ];
        $this->assign('dateParams', $dateParams);

        $this->systemDisplay();
    }

}