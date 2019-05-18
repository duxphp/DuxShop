<?php

/**
 * 快递单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class DeliveryAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderDelivery';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '运单管理',
                'description' => '管理订单发货快递',
            ],
            'fun' => [
                'index' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.order_no',
        ];
    }

    public function _indexOrder() {
        return 'A.delivery_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['A.status'] > 3) {
            unset($whereMaps['A.status']);
        }
        return $whereMaps;
    }

    public function _indexAssign() {
        return [
            'printList' => target('order/OrderConfigPrinter')->loadList(['status' => 1])
        ];
    }

    public function action() {
        $status = request('post', 'action',0, 'intval');
        $ids = request('post', 'ids');
        if( empty($ids)) {
            $this->error('请选择操作记录!');
        }
        $list = target($this->_model)->loadList([
            'delivery_id' => explode(',', $ids),
        ]);
        if(empty($list)) {
            $this->error('没有可处理记录!');
        }
        $idsArray = [];
        foreach ($list as $vo) {
            $idsArray[] = $vo['delivery_id'];
        }
        target($this->_model)->where(['delivery_id' => $idsArray])->data(['print_status' => $status])->update();
        $this->success('处理完成!');
    }

    public function printStatus() {
        $id = request('', 'id', 0);
        $status = request('', 'status', 0);
        $status = $status ? $status : 0;
        if (empty($id)) {
            $this->error('参数获取错误!');
        }
        target($this->_model)->edit([
            'delivery_id' => $id,
            'print_status' => $status,
        ]);
        $this->success('更新成功！');
    }

    public function print() {
        $printName = request('', 'type');
        $printStatus = request('', 'print', 0, 'intval');
        $deliveryIds = $_POST['id'];
        if(empty($deliveryIds)) {
            $this->error('请选择快递单！');
        }
        $deliveryList = target('order/OrderDelivery')->loadList([
            'A.delivery_id' => $deliveryIds,
            'A.api_status' => 1
        ]);
        if(empty($deliveryList)) {
            $this->error('该订单无可打印的电子面单！');
        }

        $data = [];
        foreach ($deliveryList as $vo) {
            $data[] = json_decode($vo['api_data'], true);
        }

        $config = target('order/OrderConfig')->getConfig();
        $typeInfo = target('order/OrderConfigWaybill')->typeInfo($config['waybill_type']);
        if(!$typeInfo['waybill']) {
            return $this->error('接口不支持电子面单！');
        }
        $info = target($typeInfo['target'], 'service')->print($data, $printName);
        if(!$info) {
            return $this->error(target($typeInfo['target'], 'service')->getError());
        }

        foreach ($deliveryList as $vo) {
            target('order/OrderDelivery')->edit([
                'delivery_id' => $vo['delivery_id'],
                'print_status' => $printStatus
            ]);
        }
        $this->success($info);
    }


}
