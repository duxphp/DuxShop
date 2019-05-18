<?php

/**
 * 退款管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class RefundAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderRefund';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '退款管理',
                'description' => '处理订单产品退货',
            ],
            'fun' => [
                'index' => true,
                'status' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'A.refund_no,D.order_no,A.delivery_no',
            'type' => 'A.status',
        ];
    }

    public function _indexOrder() {
        return 'A.refund_id desc';
    }

    public function info() {
        $id = request('', 'id', 0);
        if (empty($id)) {
            $this->error('参数获取错误!');
        }
        $info = target($this->_model)->getInfo($id);
        $orderInfo = target('order/Order')->getWhereInfo([
            'order_id' => $info['order_id'],
        ]);
        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $info['order_id'],
        ]);
        $remarkList = target('order/OrderRefundRemark')->loadList([
            'refund_id' => $info['refund_id'],
        ]);
        $this->assign('info', $info);
        $this->assign('orderInfo', $orderInfo);
        $this->assign('orderGoods', $orderGoods);
        $this->assign('remarkList', $remarkList);
        $this->systemDisplay();
    }

    public function process() {
        $id = request('', 'id', 0);
        if (empty($id)) {
            $this->error('参数获取错误!');
        }
        $info = target($this->_model)->getInfo($id);
        $orderInfo = target('order/Order')->getWhereInfo([
            'order_id' => $info['order_id'],
        ]);
        if (!$info['status']) {
            $this->error('该退款单无法处理!');
        }
        $status = request('', 'status', 0, 'intval');
        $remark = request('', 'remark', '', 'html_clear');
        if (!$info['status']) {
            $this->error('该退款已处理，无法重复操作!');
        }
        target($this->_model)->beginTransaction();
        if (!$info['type']) {
            //退款
            if ($info['status'] == 1) {
                $status = $status ? 3 : 0;
            } else {
                $this->error('该退款已处理，无法重复操作!');
            }

        } else {
            //退货
            if ($info['status'] == 1) {
                $status = $status ? 2 : 0;
            } else if ($info['status'] == 2) {
                $status = $status ? 3 : 0;
            } else {
                $this->error('该退款已处理，无法重复操作!');
            }
        }

        $save = target($this->_model)->edit([
            'refund_id' => $id,
            'status' => $status,
            'process_remark' => $remark,
            'process_time' => time(),
        ]);
        if (!$save) {
            target($this->_model)->rollBack();
            $this->error('退款处理失败!');
        }

        if ($status == 2) {
            $adminText = '退货申请审核通过';
        }

        if ($status == 3) {
            $adminText = '退款申请审核通过';

            if ($info['type'] == 2) {
                if (!target('order/Order', 'service')->refundOrder($info['order_id'], 1)) {
                    target($this->_model)->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
            } else {
                if (!target('order/Order', 'service')->refundOrder($info['order_goods_id'], 0, $info['price'])) {
                    target($this->_model)->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
            }
        }

        if (!$status) {
            $adminText = '退款申请已拒绝';
            $save = target('order/OrderGoods')->edit([
                'id' => $info['order_goods_id'],
                'service_status' => 0,
            ]);
            if (!$save) {
                target($this->_model)->rollBack();
                $this->error('退款处理失败!');
            }
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], $adminText, $remark, USER_ID)) {
            target($this->_model)->rollBack();
            $this->error('订单日志记录失败!');
        }
        target($this->_model)->commit();
        $this->success('退款处理成功!', url('index'));
    }

    public function remark() {
        $refundId = request('', 'id', '', 'intval');
        $content = request('', 'content', '', 'html_clear');
        if (empty($content)) {
            $this->error('请填写备注内容');
        }
        $status = target('order/OrderRefundRemark')->add([
            'refund_id' => $refundId,
            'content' => $content,
            'user_id' => USER_ID,
            'time' => time(),
        ]);
        if (!$status) {
            $this->error(target('order/OrderRefundRemark')->getError());
        }
        $this->success('备注成功!');
    }


}