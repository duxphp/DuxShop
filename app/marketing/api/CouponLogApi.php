<?php

/**
 * 领券记录
 */

namespace app\marketing\api;

class CouponLogApi extends \app\member\api\MemberApi {

    protected $_middle = 'marketing/CouponLog';

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

}
