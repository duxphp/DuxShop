<?php

/**
 * 积分记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PointsLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'statis/StatisFinancialLog';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '交易记录',
                'description' => '资金收入支出交易记录',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'name' => 'name',
            'type' => 'A.type',
            'species' => 'species',
            'log_no' => 'A.log_no',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
            'user_id' => 'B.user_id',
        ];
    }

    public function _indexOrder() {
        return 'log_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['name']) {
            $whereMaps['OR'] = [
                'B.user_id[~]' => $whereMaps['name'],
                'B.nickname[~]' => $whereMaps['name'],
                'B.tel[~]' => $whereMaps['name'],
                'B.email[~]' => $whereMaps['name'],
            ];
        }
        unset($whereMaps['name']);

        if($whereMaps['A.type'] > 1) {
            unset($whereMaps['A.type']);
        }
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'A.time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.time <= ' . $stopTime;
        }
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        $typeList = target('statis/StatisFinancial')->typeList('points');
        $species = [];
        foreach ($typeList as $key => $value) {
            $species[] = $value['key'];
        }
        $species = array_unique($species);
        $whereMaps['A.has_species'] = $species;

        $species = explode('|', $whereMaps['species']);
        if ($species[0]) {
            $whereMaps['has_species'] = $species[0];
        }

        if ($species[1]) {
            $whereMaps['sub_species'] = $species[1];
        }

        unset($whereMaps['species']);
        return $whereMaps;
    }

    public function _indexAssign() {
        $typeList = target('statis/StatisFinancial')->typeList('points');
        return [
            'typeList' => $typeList,
        ];
    }

    public function info() {
        $id = request('', 'id');
        if(empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target($this->_model)->getInfo($id);
        if(empty($info)) {
            $this->error('暂无该记录!');
        }
        $html = \dux\Dux::view()->fetch('app/member/view/admin/pointslog/info', [
            'info' => $info
        ]);
        $this->success($html);
    }

}