<?php

/**
 * 配送员管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class MarkiWarningAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseMarki';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '下单预警',
                'description' => '商户下单预警信息',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'A.nickname,C.store,C.name,C.tel',
            'province' => 'C.province',
            'city' => 'C.city',
            'region' => 'C.region',
            'street' => 'C.street',
            'sale_id' => 'E.user_id',
        ];
    }

    public function _indexAssign() {
        $userInfo = [];
        $userId = request('', 'sale_id', 0, 'intval');
        if($userId) {
            $userInfo = target('sale/SaleUser')->getWhereInfo(['A.user_id' => $userId]);
        }
        return [
            'userInfo' => $userInfo
        ];
    }

    public function _indexOrder() {
        return 'marki_id asc';
    }

    public function _indexWhere($whereMaps) {

        $startTime = $startTime ? $startTime : date("Y-m-d", strtotime("-1 day")) . " 0:0:0";
        $stopTime = $stopTime ? $stopTime : date("Y-m-d 23:59:59");
        $startTime = strtotime($startTime);
        $stopTime = strtotime($stopTime);

        $where = [];
        $where['order_create_time[>=]'] = $startTime;
        $where['order_create_time[<=]'] = $stopTime;
        $orderList = target('order/Order')->field(['order_user_id'])->where($where)->select();
        $userIds = array_column($orderList, 'order_user_id');

        if (!empty($userIds)) {
            $whereMaps['A.user_id[!]'] = $userIds;
        }

        $time = time();
        $warningList = target('Warehouse/WarehouseWarning')->loadList([
            'start_time[<=]' => $time,
            'stop_time[>=]' => $time,
        ]);

        $userIds2 = array_column($warningList, 'user_id');
        $userIds = array_unique(array_merge($userIds, $userIds2));

        if (!empty($userIds)) {
            $whereMaps['A.user_id[!]'] = $userIds;
        }
        return $whereMaps;
    }

    public function _indexCount($where) {
        return target('order/Order')
            ->table('member_user(A)')
            ->join('order(B)', ['B.order_user_id', 'A.user_id'], '>')
            ->join('order_address(C)', ['C.user_id', 'A.user_id'], '>')
            ->join('sale_user(D)', ['D.user_id', 'A.user_id'], '>')
            ->join('member_user(E)', ['E.user_id', 'D.parent_id'], '>')
            ->where($where)
            ->group('A.user_id')
            ->count();
    }

    public function _indexData($where, $limit, $order) {
        return target('order/Order')
            ->table('member_user(A)')
            ->join('order(B)', ['B.order_user_id', 'A.user_id'], '>')
            ->join('order_address(C)', ['C.user_id', 'A.user_id'], '>')
            ->join('sale_user(D)', ['D.user_id', 'A.user_id'], '>')
            ->join('member_user(E)', ['E.user_id', 'D.parent_id'], '>')
            ->field(['C.*', 'A.user_id', 'A.nickname', 'A.avatar', 'B.order_create_time', 'E.user_id(parent_user_id)', 'E.nickname(parent_nickname)', 'E.avatar(parent_avatar)',])
            ->where($where)
            ->group('A.user_id')
            ->limit($limit)
            ->order('B.order_create_time asc, A.user_id desc')
            ->select();
    }

    public function tip() {
        $id = request('', 'id', 0, 'intval');
        $day = request('', 'day', 3, 'intval');
        $remark = request('', 'remark', '', 'html_clear');

        if (!$id) {
            $this->error('未知参数');
        }
        $startTime = date("Y-m-d 0:0:0");
        $stopTime = date("Y-m-d", strtotime("+{$day} day")) . " 23:59:59";
        $startTime = strtotime($startTime);
        $stopTime = strtotime($stopTime);

        target('WarehouseWarning')->add([
            'user_id' => $id,
            'start_time' => $startTime,
            'stop_time' => $stopTime,
            'remark' => $remark
        ]);
        $this->success('设置预警成功！');
    }


}