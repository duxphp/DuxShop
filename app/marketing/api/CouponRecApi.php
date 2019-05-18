<?php

/**
 * 老带新活动
 */

namespace app\marketing\api;

class CouponRecApi extends \app\member\api\MemberApi {

    protected $_middle = 'marketing/CouponRec';

	public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userId,
        ])->meta()->data()->export(function ($data) use ($pageLimit) {
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

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'rec_id' => $this->data['rec_id'],
            'user_id' => $this->userId,
        ])->info()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }

}
