<?php

/**
 * 订单操作
 */

namespace app\order\api;

class OrderApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Order';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'type' => $this->data['type'],
            'limit' => $pageLimit,
        ])->data()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多', 404);
            }
        }, function ($message, $code, $url) {
            $this->error('暂无更多', 404);
        });
    }

    /**
     * 取消订单
     */
    public function cancel() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'order_no' => $this->data['order_no']
        ])->cancel()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 确认收货
     */
    public function confirm() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'order_no' => $this->data['order_no']
        ])->confirm()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    /**
     * 查看快递
     */
    public function delivery() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'order_no' => $this->data['order_no'],
            'num' => $this->data['num']
        ])->delivery()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }


}
