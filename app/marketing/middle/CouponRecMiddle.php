<?php

/**
 * 老带新优惠券
 */

namespace app\marketing\middle;


class CouponRecMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'marketing/MarketingCouponRec';

    protected function data() {
        $type = intval($this->params['type']);
        $where['A.status'] = 1;
        if ($type == 1) {
            $where['_sql'] = 'A.stop_time >= ' . time();
        }
        if ($type == 2) {
            $where['_sql'] = 'A.stop_time < ' . time();
        }
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'rec_id desc');

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function info() {
        $recId = intval($this->params['rec_id']);
        $userId = intval($this->params['user_id']);
        if(empty($recId)) {
            return $this->stop('该活动不存在!');
        }
        $info = target($this->_model)->getInfo($recId);
        if (empty($info) || !$info['status']) {
            return $this->stop('该活动不存在!');
        }
        $logList = target('marketing/MarketingCouponRecLog')->loadList([
            'A.user_id' => $userId
        ]);
        return $this->run([
            'info' => $info,
            'url' => '/pages/index/index?rec='.$info['rec_id'] . '_' . $userId,
            'logList' => $logList
        ]);
    }

}