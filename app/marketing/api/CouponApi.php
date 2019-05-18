<?php
 
namespace app\marketing\api;
/**
 * 领券中心
 */
class CouponApi extends \app\member\api\MemberApi {

    protected $_middle = 'marketing/Coupon';
    /**
     * 优惠券
     * @method GET
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"data":[{"coupon_id":1 ,"class_id": "1", "type": "mall", "has_id": "20", "rule": null, "url": "", "name": "测试优惠券", "image": "", "money": "100.00", "meet_money": "500", "create_time": "1557567958", "start_time": "1557567909", "end_time": "1560246309", "status": "1", "stock": "100", "receive": "0", "expiry_day": "10", "exchange_type": "", "exchange_price": "0", "del_status": "0", "class_name": "优惠券", "currencyInfo": null }] }]}
     * @field integer $coupon_id 优惠券ID
     * @field integer $class_id 优惠券类型ID
     * @field string $type  类型
     * @field integer $has_id 关联ID
     * @field string $rule 优惠券规则
     * @field string $url  使用链接
     * @field string $name 优惠券名称
     * @field string $image 优惠券图片
     * @field decimal $money 优惠券金额
     * @field decimal $meet_money 满金额
     * @field string $create_time 创建时间
     * @field string $start_time 优惠券开始时间
     * @field string $end_time 结束时间
     * @field integer $status 状态
     * @field integer $stock 库存量
     * @field integer $receive 领取量
     * @field integer $expiry_day  有效天数
     * @field integer $exchange_type 兑换方式
     * @field integer $exchange_price 兑换价格
     * @field integer $del_status 删除状态
     * @field integer $class_name 分类名称
     * @field integer $receive_status  领取状态
     */

	public function index() {
        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userId,
            'class_id' => $this->data['class_id'],
            'id' => $this->data['id'],
        ])->data()->export(function ($data) use ($pageLimit) {
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

    public function classList() {
        target($this->_middle, 'middle')->setParams([
            'type' => $this->data['type'],
            'user_id' => $this->userId,
        ])->data()->classData()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });

    }

	
	public function receive() {
		target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'coupon_id' => $this->data['id']
        ])->receive()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
	}

}
