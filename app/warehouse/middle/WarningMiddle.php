<?php

/**
 * 订单管理
 */

namespace app\warehouse\middle;

class WarningMiddle extends \app\base\middle\BaseMiddle {


    protected function warning() {
        $userId = intval($this->params['user_id']);
        $type = intval($this->params['type']);
        $startTime = $this->params['start_time'];
        $stopTime = $this->params['stop_time'];

        $userList = target('sale/SaleUser')->loadList([
            'parent_id' => $userId,
        ]);
        if(empty($userList)) {
            return $this->run([]);
        }
        $userIds = array_column($userList, 'user_id');
        $startTime = $startTime ? $startTime . '  0:0:0' : date("Y-m-d", strtotime("-1 day")) . " 0:0:0";
        $stopTime = $stopTime ? $stopTime . ' 23:59:59' : date("Y-m-d 23:59:59");
        $startTime = strtotime($startTime);
        $stopTime = strtotime($stopTime);

        $where = [];
        $where['order_user_id'] = $userIds;
        if(!$type) {
            $where['order_create_time[>=]'] = $startTime;
            $where['order_create_time[<=]'] = $stopTime;
        }
        $orderList = target('order/Order')->field(['order_user_id'])->where($where)->select();
        $orderIds = array_column($orderList, 'order_user_id');

        $time = time();
        $warningList = target('Warehouse/WarehouseWarning')->loadList([
            'start_time[<=]' => $time,
            'stop_time[>=]' => $time,
        ]);
        $userIds2 = array_column($warningList, 'user_id');
        $orderIds = array_unique(array_merge($orderIds, $userIds2));

        $userIds = array_diff($userIds, $orderIds);

        if(empty($userIds)) {
            return $this->run([]);
        }

        $userList = target('order/Order')
            ->table('member_user(A)')
            ->join('order(B)', ['B.order_user_id', 'A.user_id'], '>')
            ->join('order_address(C)', ['C.user_id', 'A.user_id'], '>')
            ->field(['C.*', 'A.user_id', 'A.nickname', 'A.avatar', 'B.order_create_time'])
            ->where([
                'A.user_id' => $userIds
            ])
            ->group('A.user_id')
            ->order('B.order_create_time asc')
            ->select();

        return $this->run($userList);
    }

    protected function setting() {
        $id = $this->params['id'];
        $day = $this->params['day'];

        if(!$id) {
            return $this->stop('未知参数');
        }
        $startTime = date("Y-m-d 0:0:0");
        $stopTime = date("Y-m-d", strtotime("+{$day} day")) . " 23:59:59";
        $startTime = strtotime($startTime);
        $stopTime = strtotime($stopTime);

        target('WarehouseWarning')->add([
            'user_id' => $id,
            'start_time' => $startTime,
            'stop_time' => $stopTime,
        ]);

        return $this->run([], '设置成功');


    }





}