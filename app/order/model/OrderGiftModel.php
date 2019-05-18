<?php

/**
 * 赠品管理
 */
namespace app\order\model;

use app\system\model\SystemModel;

class OrderGiftModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'gift_id'
    ];


    public function _saveBefore($data, $type) {

        if(empty($data['image'])) {
            $this->error = '请上传封面图！';
            return false;
        }

        if($data['type']) {
            if(empty($_POST['mall_ids'])) {
                $this->error = '请选择关联商品！';
                return false;
            }
            $data['mall_ids'] = implode(',', $_POST['mall_ids']);
            $data['order_money'] = 0;
        }else {
            if(empty($data['order_money'])) {
                $this->error = '请设置订单金额！';
                return false;
            }
            $data['mall_ids'] = '';
        }

        $hasIds = $_POST['has_ids'];
        if(empty($hasIds)) {
            $this->error = '请选择赠品！';
            return false;
        }
        $data['has_ids'] = implode(',', $_POST['has_ids']);

        $giftTime = $_POST['gift_time'];
        if(empty($giftTime)) {
            $this->error = '请选择活动时间！';
            return false;
        }
        $timeData = explode(' - ', $giftTime);
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