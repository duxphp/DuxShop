<?php

/**
 * 包邮管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderFreightModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'freight_id'
    ];


    public function _saveBefore($data, $type) {

        if(empty($data['order_money'])) {
            $this->error = '订单金额不正确！';
            return false;
        }

        if($data['exclude_ids']) {
            $data['exclude_ids'] = implode(',', $data['exclude_ids']);
        }else {
            $data['exclude_ids'] = '';
        }

        $freightTime = $_POST['freight_time'];
        if(empty($freightTime)) {
            $this->error = '请选择活动时间！';
            return false;
        }
        $timeData = explode(' - ', $freightTime);
        $timeData = array_map(function ($time) {
            $time = trim($time);
            if(empty($time)) {
                return '';
            }
            return strtotime($time);
        }, $timeData);

        $startTime = $timeData[0];
        $stopTime = $timeData[1];
        if(empty($startTime) || empty($stopTime)) {
            $this->error = '活动时间选择不正确！';
            return false;
        }
        $data['start_time'] = $startTime;
        $data['stop_time'] = $stopTime;

        return $data;
    }

}