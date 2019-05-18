<?php

/**
 * 商品评价
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class CommentAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'order/OrderComment';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商品评价',
                'description' => '管理审核商品评价信息',
            ],
            'fun' => [
                'index' => true,
                'status' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'name' => 'name',
            'type' => 'type',
            'status' => 'status',
            'level' => 'level',
        ];
    }

    protected function _indexWhere($whereMaps) {
        $whereMaps['A.app'] = 'mall';

        $whereMaps['name'] = html_clear($whereMaps['name']);
        if ($whereMaps['name']) {
            switch ($whereMaps['type']) {
                case 1:
                    $whereMaps['_sql'][] = "(B.user_id = '{$whereMaps['name']}' OR B.nickname like '%{$whereMaps['name']}%' OR B.tel= '{$whereMaps['name']}' OR B.email= '{$whereMaps['name']}')";
                    break;
                case 2:
                    $whereMaps['_sql'][] = "(D.receive_name = '{$whereMaps['name']}' OR D.receive_tel = '{$whereMaps['name']}')";
                    break;
                case 3:
                    $whereMaps['_sql'][] = "(D.receive_city like '%{$whereMaps['name']}%' OR D.receive_region like '%{$whereMaps['name']}%' OR D.receive_address like '%{$whereMaps['name']}%')";
                    break;
                case 4:
                    $whereMaps['C.goods_name[~]'] = $whereMaps['name'];
                    break;
                case 5:
                    $whereMaps['C.goods_no'] = $whereMaps['name'];
                    break;
                default:
                    $whereMaps['D.order_no'] = $whereMaps['name'];
                    break;
            }
        }
        unset($whereMaps['type']);
        unset($whereMaps['name']);

        if($whereMaps['level']) {
            if($whereMaps['level'] == 1) {
                $whereMaps['_sql'][] = 'A.level <= 1';
            }
            if($whereMaps['level'] == 2) {
                $whereMaps['_sql'][] = 'A.level > 1 AND A.level <= 3';
            }
            if($whereMaps['level'] == 3) {
                $whereMaps['_sql'][] = 'A.level > 3';
            }
        }
        unset($whereMaps['level']);

        if($whereMaps['status']) {
            if($whereMaps['status'] == 1) {
                $whereMaps['A.status'] = 1;
            }
            if($whereMaps['status'] == 2) {
                $whereMaps['A.status'] = 0;
            }
        }
        unset($whereMaps['status']);
        return $whereMaps;
    }

    public function action() {
        $action = request('post', 'action',0, 'intval');
        $ids = request('post', 'ids');
        if(empty($action)) {
            $this->error('请选择操作!');
        }
        if( empty($ids)) {
            $this->error('请选择操作记录!');
        }
        $list = target($this->_model)->loadList([
            'comment_id' => explode(',', $ids),
        ]);
        if(empty($list)) {
            $this->error('没有可处理记录!');
        }
        $idsArray = [];
        foreach ($list as $vo) {
            $idsArray[] = $vo['comment_id'];
        }
        if($action == 1) {
            $status = 1;
        }
        if($action == 2) {
            $status = 0;
        }
        target($this->_model)->where(['comment_id' => $idsArray])->data(['status' => $status])->update();
        $this->success('处理完成!');
    }


}