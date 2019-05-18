<?php

/**
 * 购物车
 */

namespace app\order\api;

class CartApi extends \app\member\api\MemberApi {

    public function index() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function total() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->total()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function submit() {
        if (!isPost()) {
            target('order/CartSubmit', 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'add_id' => $this->data['add_id'],
                'take_id' => $this->data['take_id'],
                'quick' => $this->data['quick'],
                'type' => request('get', 'type'),
                'group' => request('get', 'group'),
            ])->data()->export(function ($data) {
                $this->success('ok', $data);
            }, function ($message, $code) {
                $this->error($message, $code);
            });
        } else {
            $payType = isset($_POST['pay_type']) ? $_POST['pay_type'] : 1;
            $payType = $payType ? 1 : 0;
            target('order/CartSubmit', 'middle')->setParams([
                'user_id' => $this->userInfo['user_id'],
                'add_id' => $this->data['add_id'],
                'group' => $this->data['group'],
                'quick' => $this->data['quick'],
                'coupon_id' => $this->data['coupon_id'],
                'take_id' => $this->data['take_id'],
                'take_type' => $this->data['take_type'],
                'remark' => $this->data['remark'],
                'invoice' => $this->data['invoice'],
                'invoice_type' => $this->data['invoice_type'],
                'invoice_class' => $this->data['invoice_class'],
                'invoice_name' => $this->data['invoice_name'],
                'invoice_label' => $this->data['invoice_label'],
                'receive_name' => $this->data['receive_name'],
                'receive_tel' => $this->data['receive_tel'],
                'receive_address' => $this->data['receive_address'],
                'gift' => $this->data['gift'],
                'sale_code' => $this->data['sale_code'],
                'pay_type' => $payType,
            ])->post()->export(function ($data) {
                $this->success('订单提交成功,请选择付款方式!', $data);
            }, function ($message, $code) {
                $this->error($message, $code);
            });
        }
    }

    public function take() {
        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'add_id' => $this->data['add_id'],
        ])->take()->export(function ($data) {
            $this->success('', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function coupon() {
        target('order/CartSubmit', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->Coupon()->export(function ($data) {
            $this->success('', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function num() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => $this->data['rowid'],
            'qty' => $this->data['qty'],
        ])->put()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function checked() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'checked' => $this->data['checked'],
            'uncheck' => $this->data['uncheck'],
        ])->checked()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function del() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'rowid' => $this->data['rowid'],
        ])->delete()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function clear() {
        target('order/Cart', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->clear()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}
