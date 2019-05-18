<?php

/**
 * 订单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class OrderAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Order';
    private $orderInfo = [];
    private $orderId = 0;
    private $orderStatus = [];

    public function __construct() {
        parent::__construct();
        $orderId = request('', 'order_id', 0);
        if (empty($orderId)) {
            $this->error('参数错误！');
        }
        $orderInfo = target('order/Order')->getInfo($orderId);
        if (empty($orderInfo)) {
            $this->error('订单不存在！');
        }
        $this->orderInfo = $orderInfo;
        $this->orderId = $orderId;
        $this->orderStatus = target('order/Order', 'service')->getManageStatus($orderInfo);
    }

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '订单管理',
                'description' => '提供系统中订单功能',
            ],
        ];
    }

    /**
     * 确认付款
     */
    public function pay() {
        $orderInfo = $this->orderInfo;
        if (!$this->orderStatus['pay']) {
            $this->error('该订单无法进行支付!');
        }
        $type = intval(request('post', 'type'));
        $remark = request('post', 'remark', '', 'html_clear');
        $model = target($this->_model);
        $orderPay = target('order/OrderPay')->getWhereInfo([
            'user_id' => $orderInfo['order_user_id'],
            'order_ids' => $orderInfo['order_id'],
        ]);
        if (empty($orderPay)) {
            $orderPayNo = target('order/Order', 'service')->addPay($this->userInfo['user_id'], $orderInfo['order_id'], 'system');
            if (!$orderPayNo) {
                $this->error(target('order/Order', 'service')->getError());
            }
        } else {
            $orderPayNo = $orderPay['pay_no'];
        }

        $totalPrice = $orderInfo['total_price'];

        $model->beginTransaction();

        if ($type) {
            $data = [];
            $data['user_id'] = $orderInfo['order_user_id'];
            $data['type'] = 0;
            $data['money'] = $totalPrice;
            $status = target('member/Finance', 'service')->account($data);
            if (!$status) {
                $this->error(target('member/Finance', 'service')->getError());
            }
        }

        if (!target('order/Order', 'service')->payOrder($orderPayNo, $totalPrice, $type ? '账号支付' : '线下付款', '')) {
            $model->rollBack();
            $this->error(target('order/Order', 'service')->getError());
            return false;
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], '人工确认付款', $remark, USER_ID)) {
            $model->rollBack();
            $this->error('订单日志记录失败!');
        }

        if (!$orderInfo['pay_type']) {
            $receiptInfo = target('order/OrderReceipt')->getWhereInfo([
                'A.order_id' => $orderInfo['order_id'],
            ]);
            if (!empty($receiptInfo) && !$receiptInfo['status']) {
                $data = [];
                $data['receipt_id'] = $receiptInfo['receipt_id'];
                $data['money'] = $totalPrice;
                $data['remark'] = $remark;
                $data['receipt_time'] = time();
                $data['status'] = 1;
                if (!target('order/OrderReceipt')->edit($data)) {
                    $this->error('收款状态更改失败!');
                }
            }
        }

        $model->commit();
        $this->success('付款操作成功!');
    }

    /**
     * 订单配货
     */
    public function parcel() {
        $orderInfo = $this->orderInfo;
        if (!$this->orderStatus['parcel']) {
            $this->error('该订单无法进行配货!');
        }
        $post = request('post');
        $model = target($this->_model);
        $model->beginTransaction();
        if (!target('order/Order', 'service')->parcelOrder($this->orderId, $post['remark'])) {
            $model->rollBack();
            $this->error(target('order/Order', 'service')->getError());
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], '订单开始配货', $post['remark'], USER_ID)) {
            $model->rollBack();
            $this->error('订单日志记录失败!');
        }
        $model->commit();
        $this->success('发货操作成功!');

    }

    /**
     * 确认发货
     */
    public function delivery() {
        $orderInfo = $this->orderInfo;
        if (!$this->orderStatus['delivery']) {
            $this->error('该订单无法进行发货!');
        }
        $post = request('post');

        $model = target($this->_model);
        $model->beginTransaction();
        if (!target('order/Order', 'service')->deliveryOrder($this->orderId, $post['id'], $post['delivery_type'], [
            'single_type' => $post['single_type'],
            'name' => $post['name'],
            'no' => $post['no'],
            'remark' => $post['remark'],
            'marki_id' => $post['marki_id'] 
        ])) {
            $model->rollBack();
            $this->error(target('order/Order', 'service')->getError());
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], '订单发货操作', $post['remark'], USER_ID)) {
            $model->rollBack();
            $this->error('订单日志记录失败!');
        }
        $model->commit();
        $this->success('发货操作成功!');
    }

    public function complete() {
        $orderInfo = $this->orderInfo;
        if (!$this->orderStatus['complete']) {
            $this->error('该订单无法完成操作!');
        }
        $post = request('post');
        $model = target($this->_model);
        $model->beginTransaction();
        if (!target('order/Order', 'service')->confirmOrder($this->orderId)) {
            $model->rollBack();
            $this->error(target('order/Order', 'service')->getError());
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], '人工确认完成订单', $post['remark'], USER_ID)) {
            $model->rollBack();
            $this->error('订单日志记录失败!');
        }
        $model->commit();
        $this->success('确认收货成功!');
    }

    public function close() {
        $orderInfo = $this->orderInfo;
        $post = request('post');
        $type = request('post', 'type', 0, 'intval');
        $model = target($this->_model);
        $model->beginTransaction();
        if (!target('order/Order', 'service')->cancelOrder($this->orderId, $type)) {
            $model->rollBack();
            $this->error(target('order/Order', 'service')->getError());
        }
        $model->commit();
        target('order/Order', 'service')->addLog($orderInfo['order_id'], '人工取消订单', $post['remark'], USER_ID);
        $this->success('取消订单成功!');

    }

    public function remark() {
        $orderInfo = $this->orderInfo;
        $content = request('', 'content', '', 'html_clear');
        if(empty($content)) {
            $this->error('请填写备注内容');
        }
        $status = target('order/OrderRemark')->add([
            'order_id' => $orderInfo['order_id'],
            'content' => $content,
            'user_id' => USER_ID,
            'time' => time()
        ]);
        if(!$status) {
            $this->error(target('order/OrderRemark')->getError());
        }
        $this->success('备注成功!');
    }



}