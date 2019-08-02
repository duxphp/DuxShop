<?php



namespace app\member\api;


class PayApi extends \app\member\api\MemberApi {

    protected $_middle = 'worship/Pay';


    public function index() {
        target($this->_middle, 'middle')->setParams([
            'platform' => PLATFORM,
            'model' => $this->data['model'],
            'type' => $this->data['type'],
            'has_id' => $this->data['has_id'],
            'user_id' => $this->userInfo['user_id'],
        ])->pay()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}
