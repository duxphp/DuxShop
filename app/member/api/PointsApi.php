<?php
 
namespace app\member\api;

class PointsApi extends \app\member\api\MemberApi {

    protected $_middle = 'member/Points';
    /**
     * 积分账户
     * @method GET 
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"data":[{"log_id": "34", "user_id": "2", "log_no": "220190507757483", "has_no": "220190507682197", "has_species": "points_order", "sub_species": "reward", "time": "1557212304", "money": "138.00", "title": "订单完成奖励", "remark": "订单完成赠送积分奖励", "pay_no": "", "pay_name": "", "pay_way": "", "type": "1", "user_email": "", "user_tel": "", "user_nickname": "徐健2", "show_name": "徐健2", "show_time": "2019-05-07 14:58"}]} ]}
     * @field integer $log_id  记录ID
     * @field integer $user_id  用户ID
     * @field string  $log_no  流水号
     * @field string  $has_no  关联单号
     * @field string  $has_species  关联种类
     * @field string  $sub_species  关联子类
     * @field string  $time   交易时间
     * @field decimal $money  交易金额
     * @field string  $title  交易名称
     * @field string  $remark 交易备注
     * @field string  $pay_no  交易号
     * @field string  $pay_name  交易名
     * @field string  $pay_way  交易方式
     * @field integer  $type  支出收入类型
     * @field string  $user_email  用户邮箱
     * @field string  $user_tel  用户电话
     * @field string  $user_nickname  用户昵称
     * @field string  $show_name  显示姓名
     * @field string  $show_time  显示时间
     * 
     */
    public function index() {
        $type = $this->data['type'];
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'type' => $type,
            'limit' => $pageLimit,
        ])->data()->export(function ($data) use ($pageLimit) {
            if (!empty($data['pageList'])) {
                $this->success('ok', [
                    'data' => $data['pageList'],
                    'pageData' => $this->pageData($pageLimit, $data['pageList'], $data['pageData']),
                ]);
            } else {
                $this->error('暂无更多记录', 404);
            }
        }, function () {
            $this->error('暂无更多记录', 404);
        });

    }

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'no' => $this->data['no'],
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });
    }

}
