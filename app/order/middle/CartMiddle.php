<?php

/**
 * 购物车编辑
 */

namespace app\order\middle;


class CartMiddle extends \app\base\middle\BaseMiddle {


    /**
     * 媒体信息
     */
    protected function meta() {
        $this->setMeta('购物车');
        $this->setName('购物车');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '购物车',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $list = target('order/Cart', 'service')->getList($this->params['user_id']);
        $info = target('order/Cart', 'service')->getCart($this->params['user_id']);
        $cartData = target('order/Order', 'service')->splitOrder('', $list);

        return $this->run([
            'cartData' => $cartData,
            'list' => $list,
            'info' => $info
        ]);
    }

    protected function total() {
        $this->params['user_id'] = intval($this->params['user_id']);
        $info = target('order/Cart', 'service')->getCart($this->params['user_id']);
        return $this->run([
            'info' => [
                'number' => $info['number']
            ]
        ]);
    }

    protected function put() {
        if (empty($this->params['rowid'])) {
            return $this->stop('商品信息获取失败,请重试!');
        }
        $this->params['qty'] = intval($this->params['qty']);
        $this->params['user_id'] = intval($this->params['user_id']);
        $info = target('order/Cart', 'service')->getInfo($this->params['user_id'], $this->params['rowid']);

        $callback = target($info['app'] . '/' . $info['app'], 'service')->putCart($info, $this->params['user_id'], $this->params['qty']);
        if(!$callback) {
            return $this->stop(target($info['app'] . '/' . $info['app'], 'service')->getError());
        }
        $this->params['qty'] = $callback['qty'];
        if (!$this->params['qty']) {
            if (!target('order/Cart', 'service')->del($this->params['user_id'], explode(',', $this->params['rowid']))) {
                return $this->stop(target('order/Cart', 'service')->getError());
            }
        }else {
            $cartData = [];
            $cartData['rowid'] = $this->params['rowid'];
            $cartData['qty'] = $this->params['qty'];
            if (!target('order/Cart', 'service')->update($this->params['user_id'], $cartData)) {
                return $this->stop(target('order/Cart', 'service')->getError());
            }
        }
        $cart = target('order/Cart', 'service')->getCart($this->params['user_id']);
        $cart['list'] = $cart['list'] ? array_values($cart['list']) : [];
        return $this->run([
            'rowid' => $this->params['rowid'],
            'cart' => $cart
        ]);
    }

    protected function checked() {
        $checked = $this->params['checked'];
        $uncheck = $this->params['uncheck'];

        $checked = array_filter(explode(',', $checked));
        $uncheck = array_filter(explode(',', $uncheck));

        $cartData = [];
        foreach ($checked as $rowId) {
            $cartData[] = [
                'rowid' => $rowId,
                'checked' => 1,
            ];
        }
        foreach ($uncheck as $rowId) {
            $cartData[] = [
                'rowid' => $rowId,
                'checked' => 0
            ];
        }
        if($cartData) {
            if (!target('order/Cart', 'service')->update($this->params['user_id'], $cartData)) {
                return $this->stop(target('order/Cart', 'service')->getError());
            }
        }
        return $this->run();
    }

    protected function delete() {
        $this->params['user_id'] = intval($this->params['user_id']);
        if (empty($this->params['rowid'])) {
            return $this->stop('商品信息获取失败,请重试!');
        }
        if (!target('order/Cart', 'service')->del($this->params['user_id'], explode(',', $this->params['rowid']))) {
            return $this->stop(target('order/Cart', 'service')->getError());
        }
        return $this->run();
    }

    protected function clear() {
        $this->params['user_id'] = intval($this->params['user_id']);
        if (!target('order/Cart', 'service')->clear($this->params['user_id'])) {
            return $this->stop(target('order/Cart', 'service')->getError());
        }
        return $this->run();
    }

}
