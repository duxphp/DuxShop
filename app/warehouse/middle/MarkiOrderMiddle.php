<?php

namespace app\warehouse\middle;

class MarkiOrderMiddle extends \app\base\middle\BaseMiddle {


    protected function data() {
        $userId = intval($this->params['user_id']);
        $type = intval($this->params['type']);
        $pageLimit = 10;
        $markiInfo = target('warehouse/WarehouseMarki')->getWhereInfo([
            'A.user_id' => $userId,
        ]);
        if(empty($markiInfo)) {
            $this->stop('配送员未绑定！');
        }

        $where = [];
        $where['A.marki_id'] = $markiInfo['marki_id'];

        switch ($type) {
            case 1:
                $where['A.receive_status'] = 1;
                break;
            case 2:
                $where['A.receive_status'] = 0;
                break;
        }

        //查询订单
        $model = target('base/Base');
        $count = $model->table('warehouse_marki_delivery(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->join('member_user(C)', ['C.user_id', 'B.order_user_id'])
            ->where($where)->group('A.order_id')->count();
        $pageData = $this->pageData($count, $pageLimit);
        $data = $model->table('warehouse_marki_delivery(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->join('member_user(C)', ['C.user_id', 'B.order_user_id'])
            ->where($where)
            ->field(['B.*',
                'C.email(user_email)', 'C.tel(user_tel)', 'C.nickname(user_nickname)',
                'A.receive_status', 'A.create_time', 'A.receive_time', 'A.order_id'])
            ->limit($pageData['limit'])
            ->group('A.order_id')
            ->order('B.order_id desc')
            ->select();
        $orderIds = array_column($data, 'order_id');

        if ($orderIds) {
            $subList = $model->table('order_goods')
                ->where([
                    'order_id' => $orderIds,
                ])
                ->order('id asc')
                ->select();
            $subData = [];
            foreach ($subList as $vo) {
                $vo['goods_options'] = unserialize($vo['goods_options']);
                $vo['goods_options'] = $vo['goods_options'] ? $vo['goods_options'] : [];
                $subData[$vo['order_id']][] = $vo;
            }
            foreach ($data as $key => $vo) {
                $data[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['user_nickname'], $vo['user_tel'], $vo['user_email']);
                $data[$key]['status_data'] = target('order/Order', 'service')->getAction($vo);
                $data[$key]['goods_data'] = $subData[$vo['order_id']];
            }
        }
        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $data,
        ]);
    }


}