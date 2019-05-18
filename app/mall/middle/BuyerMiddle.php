<?php

/**
 * 购物记录
 */

namespace app\mall\middle;

class BuyerMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'order/OrderGoods';

    protected function data() {
        $mallId = intval($this->params['mall_id']);
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $where = [];
        $where['B.order_app'] = 'mall';
        $where['A.has_id'] = $mallId;

        $model = target($this->_model);
        $count = $model->table('order_goods(A)')
        ->join('order(B)', ['B.order_id', 'A.order_id'])
        ->join('member_user(C)', ['C.user_id', 'B.order_user_id'])
        ->where((array)$where)
        ->select();
        $pageData = $this->pageData($count, $pageLimit);
        
        $list = $model->table('order_goods(A)')
        ->join('order(B)', ['B.order_id', 'A.order_id'])
        ->join('member_user(C)', ['C.user_id', 'B.order_user_id'])
        ->field(['A.goods_qty', 'B.receive_name', 'C.avatar', 'B.order_create_time'])
        ->where((array)$where)
        ->limit($pageData['limit'])
        ->order('id desc')
        ->select();

        $number = $model->table('order_goods(A)')
        ->join('order(B)', ['B.order_id', 'A.order_id'])
        ->where((array)$where)
        ->group('A.user_id')
        ->count();

        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit,
            'number' => $number
        ]);
    }

}