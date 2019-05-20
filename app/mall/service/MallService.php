<?php

namespace app\mall\service;
/**
 * 操作
 */
class MallService extends \app\base\service\BaseService {


    public function getCart($userId, $hasId = 0, $subId = 0, $qty = 1) {
        if ((empty($subId) || empty($hasId)) && empty($userId)) {
            return $this->error('商品参数有误');
        }
        if ($hasId && empty($subId)) {
            $proInfo = target('mall/MallProducts')->getMallInfo(['A.mall_id' => $hasId]);
        } else {
            $proInfo = target('mall/MallProducts')->getMallInfo(['A.products_id' => $subId]);
        }

        if (empty($proInfo) || !$proInfo['status']) {
            return $this->error('该商品不存在或已下架');
        }

        if ($proInfo['down_time']) {
            if ($proInfo['down_time'] < time()) {
                return $this->error('该商品已下架!');
            }
        }

        if (empty($proInfo['store'])) {
            return $this->error('该商品已售完！');
        }

        if ($proInfo['min_num'] < $qty) {
            $qty = $proInfo['min_num'];
        }

        if ($proInfo['store'] < $qty) {
            return $this->error('剩余库存不足!');
        }

        $info = target('mall/Mall')->getInfo($proInfo['mall_id']);

        if ($info['gift_status']) {
            return $this->error('赠品无法直接购买!');
        }

        if ($proInfo['purchase_status'] && $proInfo['purchase_limit']) {
            if(!$this->purchase($proInfo, $userId, $qty, 1)) {
                return false;
            }
        }

        $cartData = [];
        $cartData['item_no'] = $proInfo['products_no'];
        $cartData['type'] = $proInfo['type'];
        $cartData['app'] = 'mall';
        $cartData['app_id'] = $proInfo['mall_id'];
        $cartData['id'] = $proInfo['products_id'];
        $cartData['qty'] = $qty;
        $cartData['price'] = $proInfo['sell_price'];
        $cartData['cost_price'] = $proInfo['cost_price'];
        $cartData['market_price'] = $proInfo['market_price'];
        $cartData['name'] = $proInfo['title'];
        $cartData['options'] = $proInfo['spec_data'];
        $cartData['image'] = $proInfo['image'];
        $cartData['weight'] = $proInfo['weight'];
        $cartData['unit'] = $proInfo['unit'];
        $cartData['min_num'] = $proInfo['min_num'];
        $cartData['url'] = '/pages/goods/detail?mid=' . $proInfo['integral_id'];
        if ($proInfo['type']) {
            $cartData['freight_type'] = $proInfo['freight_type'];
            $cartData['freight_tpl'] = $proInfo['freight_tpl'];
            $cartData['freight_price'] = $proInfo['freight_price'];
        } else {
            $cartData['freight_type'] = 0;
            $cartData['freight_tpl'] = 0;
            $cartData['freight_price'] = 0;
        }
        $cartData['service_status'] = $info['service_status'];
        $cartData['invoice_status'] = $info['invoice_status'];
        $cartData['discount_status'] = $info['discount_status'];
        if ($info['point_status'] == 0) {
            $cartData['extend']['point_give'] = 0;
            $cartData['extend']['point_type'] = 0;
        }
        if ($info['point_status'] == 1) {
            $cartData['extend']['point_give'] = $proInfo['give_point'];
            $cartData['extend']['point_type'] = 0;
        }
        if ($info['point_status'] == 2) {
            $cartData['extend']['point_type'] = 1;
        }

        return $this->success($cartData);
    }


    public function putCart($info, $userId, $qty) {
        $proId = $info['id'];
        $proInfo = target('mall/MallProducts')->getMallInfo([
            'A.products_id' => $proId
        ]);

        //库存处理
        if ($qty > $info['qty'] && $qty > $proInfo['store']) {
            return $this->error('库存不足！');
        }

        //限购处理
        if ($qty > $info['qty'] && $proInfo['purchase_status'] && $proInfo['purchase_limit']) {
            if(!$this->purchase($proInfo, $userId, $qty, 1)) {
                return false;
            }
        }

        //最小购买
        if (!$qty || $qty < $proInfo['min_num']) {
            if (!target('order/Cart', 'service')->del($userId, explode(',', $info['rowid']))) {
                return $this->error(target('order/Cart', 'service')->getError());
            }
            $qty = 0;
        }

        return $this->success(['qty' => $qty]);
    }


    public function purchase($proInfo, $userId, $qty, $type = 1) {
        if ($proInfo['purchase_status'] == 1 && $qty > $proInfo['purchase_limit']) {
            return $this->error('您不能超过限购数量！');
        }
        if ($proInfo['purchase_status'] == 2) {
            $orderGoodsCount = target('order/OrderGoods')
                ->table('order_goods(A)')
                ->join('order(B)', ['B.order_id', 'A.order_id'])
                ->field(['A.goods_qty'])
                ->where([
                    'A.has_id' => $proInfo['mall_id'],
                    'B.order_status' => 1,
                    'B.order_user_id' => $userId,
                    'B.order_app' => 'mall'
                ])->sum('goods_qty');
            $orderGoodsCount = $orderGoodsCount ? $orderGoodsCount : 0;
            $orderGoodsCount = $type ?  $qty + $orderGoodsCount : $orderGoodsCount;
            if($orderGoodsCount > $proInfo['purchase_limit']) {
                return $this->error('您不能超过限购数量！');
            }
        }
        return true;
    }

}

