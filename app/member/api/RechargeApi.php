<?php



namespace app\member\api;


class RechargeApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Recharge';

    /**
     * 账户充值
     * @method GET
     * @return integer $code 200
     * @return string $message ok
     * 
     */
    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => PLATFORM,
        ])->data()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public function pay() {
        target($this->_middle, 'middle')->setParams([
            'platform' => PLATFORM,
            'type' => $this->data['type'],
            'money' => $this->data['money'],
            'user_id' => $this->userInfo['user_id'],
        ])->recharge()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

    public  function log() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'limit' => $pageLimit,
        ])->log()->export(function ($data) use ($pageLimit) {
            if(!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            }else {
                $this->error('暂无更多记录', 404);
            }
        }, function () {
            $this->error('暂无更多记录', 404);
        });

    }

}
