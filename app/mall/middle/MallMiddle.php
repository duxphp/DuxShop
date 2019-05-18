<?php

/**
 * 商品操作
 */

namespace app\mall\middle;

class MallMiddle extends \app\base\middle\BaseMiddle {

    public function addCart() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $this->params['qty'] = intval($this->params['qty']);
        $qty = $this->params['qty'] ? $this->params['qty'] : 1;
        $this->params['pro_id'] = intval($this->params['pro_id']);
        $this->params['mall_id'] = intval($this->params['mall_id']);
        $rowId = $this->params['row_id'];
        $cartData = target('mall/Mall', 'service')->getCart($this->params['user_id'], $this->params['mall_id'], $this->params['pro_id'], $qty);
        if(!$cartData) {
            return $this->stop(target('mall/Mall', 'service')->getError());
        }

        $keys = target('order/Cart', 'service')->add($this->params['user_id'], $cartData, $rowId);
        if (!$keys) {
            return $this->stop(target('order/Cart', 'service')->getError());
        }
        //关注提醒
        $wechatConfig = target('wechat/WechatConfig')->getConfig();
        if($wechatConfig['mp_focus']) {
            $info = target('member/MemberConnect')->getWhereInfo([
                'user_id' => $this->params['user_id'],
                'type' => 'wechat',
            ]);
            if (!empty($info)) {
                $target = target('wechat/Wechat', 'service');
                $target->init();
                $wechat = $target->wechat();
                $info = $wechat->user->get($info['open_id']);
                if (!$info['subscribe']) {
                    return $this->stop([
                        'name' => $wechatConfig['mp_name'],
                        'desc' => $wechatConfig['mp_desc'],
                        'qrcode' => $wechatConfig['mp_qrcode'],
                    ], 206);
                }
            }
        }

        $cart = target('order/Cart', 'service')->getCart($this->params['user_id']);
        $cartInfo = $cart['list'][$keys[0]];
        $cart['list'] = $cart['list'] ? array_values($cart['list']) : [];
        return $this->run([
            'keys' => $keys,
            'cart' => $cart,
            'num' => $cartInfo['qty']
        ], '加入购物车成功！');
    }

    public function buyCart() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $this->params['qty'] = intval($this->params['qty']);
        $qty = $this->params['qty'] ? $this->params['qty'] : 1;
        $this->params['pro_id'] = intval($this->params['pro_id']);
        $this->params['mall_id'] = intval($this->params['mall_id']);
        $cartData = target('mall/Mall', 'service')->getCart($this->params['user_id'], $this->params['mall_id'], $this->params['pro_id'], $qty);
        if(!$cartData) {
            return $this->stop(target('mall/Mall', 'service')->getError());
        }
        //关注提醒
        $wechatConfig = target('wechat/WechatConfig')->getConfig();
        if($wechatConfig['mp_focus']) {
            $info = target('member/MemberConnect')->getWhereInfo([
                'user_id' => $this->params['user_id'],
                'type' => 'wechat',
            ]);
            if (!empty($info)) {
                $target = target('wechat/Wechat', 'service');
                $target->init();
                $wechat = $target->wechat();
                $info = $wechat->user->get($info['open_id']);
                if (!$info['subscribe']) {
                    return $this->stop([
                        'name' => $wechatConfig['mp_name'],
                        'desc' => $wechatConfig['mp_desc'],
                        'qrcode' => $wechatConfig['mp_qrcode'],
                    ], 206);
                }
            }
        }
        return $this->run([
            'quick' => 'mall-' . $this->params['mall_id']. '-' . $this->params['pro_id'] . '-' . $qty
        ], '购买跳转中...');
    }

    public function addFollow() {
        $this->params['mall_id'] = intval($this->params['mall_id']);
        $this->params['user_id'] = intval($this->params['user_id']);

        if (empty($this->params['mall_id']) && empty($this->params['user_id'])) {
            return $this->stop('商品参数有误!');
        }

        $mallInfo = target('mall/Mall')->getInfo($this->params['mall_id']);
        if (empty($mallInfo)) {
            return $this->stop('该商品不存在!');
        }
        if (!$mallInfo['status']) {
            return $this->stop('该商品已下架!');
        }
        $type = target('shop/Shop', 'service')->addFollow('mall', $mallInfo['mall_id'], $this->params['user_id'], $mallInfo['title'], $mallInfo['image'], $mallInfo['sell_price']);
        if (!$type) {
            $this->stop(target('shop/Shop', 'service')->getError());
        }
        $msg = '';
        if($type == 'inc') {
            if(!target('mall/Mall')->where(['mall_id' => $this->params['mall_id']])->setInc('favorite', 1)) {
                $this->stop('添加收藏失败！');
            }
            $msg = '收藏商品成功！';
        }
        if($type == 'dec') {
            if(!target('mall/Mall')->where(['mall_id' => $this->params['mall_id']])->setDec('favorite', 1)) {
                $this->stop('取消收藏失败！');
            }
            $msg = '取消收藏成功！';
        }
        return $this->run([], $msg);
    }

    public function addFaq() {
        $this->params['mall_id'] = intval($this->params['mall_id']);
        $this->params['user_id'] = intval($this->params['user_id']);
        $this->params['content'] = html_clear($this->params['content']);
        $info = target('mall/Mall')->getInfo($this->params['mall_id']);
        if (empty($info) || !$info['status']) {
            $this->stop('商品不存在！');
        }
        $msg = target('shop/Shop', 'service')->addFaq('mall', $this->params['mall_id'], $this->params['user_id'], $this->params['content']);
        if (!$msg) {
            return $this->stop(target('shop/Shop', 'service')->getError());
        }
        return $this->run([], $msg);
    }

}