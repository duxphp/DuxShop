<?php

namespace app\mall\service;
/**
 * 订单操作
 */
class OrderService extends \app\base\service\BaseService {

    private $model = 'mall/MallOrder';

    /**
     * 刷新购物车
     * @param $userId
     * @param $info
     * @return bool
     */
    public function refreshCart($userId, $info) {
        if (empty($info)) {
            return $this->success();
        }

        $ids = [];
        foreach ($info['items'] as $key => $vo) {
            $ids[] = $vo['id'];
        }
        $where = [];
        $where['_sql'] = 'A.products_id in (' . implode(',', $ids) . ')';
        $proList = target('mall/MallProducts')->loadList($where);

        $proData = [];
        foreach ($proList as $key => $vo) {
            $proData[$vo['products_id']] = $vo;
        }

        $emptyError = 0;
        $storeError = 0;
        $priceError = 0;

        foreach ($info['items'] as $key => $vo) {
            if (empty($proData[$vo['id']]) || $vo['qty'] < 1 || !$proData[$vo['id']]['status']) {
                $emptyError = 1;
                target('order/Cart', 'service')->del($userId, $vo['rowid']);
                continue;
            }
            if ($vo['qty'] > $proData[$vo['id']]['store']) {
                $storeError = 1;
                target('order/Cart', 'service')->del($userId, $vo['rowid']);
                continue;
            }
            if ($vo['price'] <> $proData[$vo['id']]['sell_price']) {
                $priceError = 1;
                target('order/Cart', 'service')->del($userId, $vo['rowid']);
                continue;
            }
        }

        if ($emptyError) {
            return $this->error('您购买的产品已下架，已取消!');
        }
        if ($storeError && $priceError) {
            return $this->error('您购买的产品由于库存和价格更改,请重新下单!');
        }
        if ($storeError) {
            return $this->error('您购买的产品由于库存不足,请重新下单!');
        }
        if ($priceError) {
            return $this->error('您购买的产品由于价格更改,请重新下单!');
        }

        return $this->success();
    }

    /**
     * 提交订单
     * @param array $data
     * @param array $goodsData
     * @return bool
     */
    public function addOrder($data = [], $goodsData = [], $addInfo = []) {
        if (empty($data) || empty($goodsData)) {
            return $this->error('订单数据为空!');
        }
        $id = target($this->model)->add([
            'order_id' => $data['order_id'],
        ]);
        if (!$id) {
            return $this->error(target($this->model)->getError());
        }

        foreach ($goodsData as $vo) {
            $qty = $vo['goods_qty'];
            //限购处理
            $proInfo = target('mall/MallProducts')->getMallInfo([
                'A.products_id' => $vo['sub_id']
            ]);
            if(!target('mall/Mall')->purchase($proInfo, $vo['user_id'], $qty, 0)) {
                return $this->error(target('mall/Mall')->getError());
            }

            //库存处理
            target('mall/MallProducts')->where(['products_id' => $vo['sub_id']])->lock(true)->find();
            if (!target('mall/MallProducts')->where(['products_id' => $vo['sub_id'], 'store[>=]' => $vo['goods_qty']])->data(['store[-]' => $vo['goods_qty']])->update()) {
                return $this->error('您购买的商品库存不足！');
            }
            if (!target('mall/MallProducts')->where(['products_id' => $vo['sub_id']])->data(['sale[+]' => $vo['goods_qty']])->update()) {
                return $this->error('销量处理失败！');
            }
            target('mall/Mall')->where(['mall_id' => $vo['has_id']])->lock(true)->find();
            if (!target('mall/Mall')->where(['mall_id' => $vo['has_id'], 'store[>=]' => $vo['goods_qty']])->data(['store[-]' => $vo['goods_qty']])->update()) {
                return $this->error('您购买的商品库存不足！');
            }
            if (!target('mall/Mall')->where(['mall_id' => $vo['has_id']])->data(['sale[+]' => $vo['goods_qty']])->update()) {
                return $this->error('销量处理失败！');
            }
        }
        return $this->success($id);
    }

    /**
     * 支付订单检查
     * @param $orderList
     * @return bool
     */
    public function checkOrder($orderList) {
        return $this->success();
    }

    /**
     * 付款成功回调
     * @param $orderList
     * @return bool
     */
    public function payOrder($orderList) {
        return true;
    }

    /**
     * 取消订单操作
     * @param $orderInfo
     * @return bool
     */
    public function cancelOrder($orderInfo) {
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id'],
        ]);
        foreach ($orderGoods as $vo) {
            if (!target('mall/MallProducts')->where(['products_id' => $vo['sub_id']])->setInc('store', $vo['goods_qty'])) {
                return $this->error('库存处理失败！');
            }
            if (!target('mall/MallProducts')->where(['products_id' => $vo['sub_id']])->setDec('sale', $vo['goods_qty'])) {
                return $this->error('销量处理失败！');
            }
            if (!target('mall/Mall')->where(['mall_id' => $vo['has_id']])->setInc('store', $vo['goods_qty'])) {
                return $this->error('库存处理失败！');
            }
            if (!target('mall/Mall')->where(['mall_id' => $vo['has_id']])->setDec('sale', $vo['goods_qty'])) {
                return $this->error('销量处理失败！');
            }
        }
        return true;
    }

    /**
     * 订单发货
     * @param $orderInfo
     * @return bool
     */
    public function deliveryOrder($orderInfo) {
        return true;
    }

    /**
     * 订单完成
     * @param $orderInfo
     * @return bool
     */
    public function confirmOrder($orderInfo) {
        $point = 0;
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $orderInfo['order_id'],
            'service_status' => 0,
        ]);
        foreach ($orderGoods as $vo) {
            if($vo['extend']['point_type'] == 0 && bccomp($vo['extend']['give_point'], 0, 2) === 1) {
                $point = price_calculate($point, '+', $vo['extend']['give_point'] * $vo['goods_qty']);
            }
            if($vo['extend']['point_type'] == 1) {
                $point = price_calculate($point, '+', $vo['price_total']);
            }
        }

        if (bccomp($point, 0, 2) !== 1) {
            return true;
        }

        $status = target('member/Points', 'service')->account([
            'user_id' => $orderInfo['order_user_id'],
            'money' => $point,
        ]);
        if (!$status) {
            return $this->error(target('member/Points', 'service')->getError());
        }

        $status = target('statis/Finance', 'service')->account([
            'user_id' => $orderInfo['order_user_id'],
            'species' => 'points_order',
            'sub_species' => 'reward',
            'no' => $orderInfo['order_no'],
            'money' => $point,
            'title' => '订单完成奖励',
            'remark' => '订单完成赠送积分奖励',
        ]);
        if (!$status) {
            return $this->error(target('statis/Finance', 'service')->getError());
        }

        return true;
    }

}

