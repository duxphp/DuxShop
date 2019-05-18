<?php

/**
 * 销售明细
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;


class SellListAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'order/OrderGoods';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '销售明细',
                'description' => '商品销售记录',
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
                $orderSql = 'A.goods_qty desc';
                break;
            case 3:
                $orderSql = 'A.price_total desc';
                break;
            default:
            case 1:
                $orderSql = 'B.pay_time desc';
                break;
        }

        if ($startTime) {
            $startTime = strtotime($startTime);
        }
        if ($stopTime) {
            $stopTime = strtotime($stopTime . ' 23:59:59');
        }

        $where = [];
        $where['B.pay_status'] = 1;
        if($keyword) {
            $where['OR'] = [
                'A.goods_name[~]' => $keyword,
                'A.goods_no' => $keyword,
            ];
        }
        $where['B.pay_time[>=]'] = $startTime;
        $where['B.pay_time[<=]'] = $stopTime;

        $limit = 20;
        $model = target($this->_model);
        $count = $model->table('order_goods(A)')
            ->join('order(B)', ['A.order_id', 'B.order_id'])
            ->where($where)
            ->count();
        $pageData = $this->pageData($count, $limit);

        $list = $model->table('order_goods(A)')
            ->join('order(B)', ['A.order_id', 'B.order_id'])
            ->field(['A.*', 'B.order_no', 'B.pay_time', 'B.order_status'])
            ->where($where)
            ->limit($pageData['limit'])
            ->order($orderSql)
            ->select();

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