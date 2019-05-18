<?php

/**
 * 购物记录
 */

namespace app\mall\api;

use \app\base\api\BaseApi;

class BuyerApi extends BaseApi {

    protected $_middle = 'mall/Buyer';

    public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['id'],
        ])->data()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多记录', 404);
            }
        }, function ($message, $code, $url) {
            $this->error('暂无更多记录', 404);
        });

    }

}
