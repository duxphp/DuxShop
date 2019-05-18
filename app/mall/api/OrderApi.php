<?php
 
namespace app\mall\api;
/**
 * 订单详情
 */
class OrderApi extends \app\member\api\MemberApi {

    protected $_middle = 'mall/Order';

    /**
     * 订单详情
     * @method GET
     * @param integer $user_id 用户ID
     * @param string  $order_no 订单号
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"payData":[{"user_id":2, "has_no": "220190510725242", "time": "1557482291", "money": "40.00", "title": "订单支付", "remark": "订单【220190510725242】支付", "pay_no": "220190510021783", "user_email": "", "user_tel": "", "user_nickname": "徐健", "show_name": "徐健", "show_time": "2019-05-10 17:58", "pay_type": "余额支付"}],     "info":[{"id": 41,"order_id": 41,"order_user_id": "2","order_no":"220190510725242","order_price": "40.00","order_status": "1","order_sum": "1","order_title": "白虾","order_image": "","order_create_time": "1557482288", "order_complete_time": "1557537598","order_close_time": "0", "order_complete_status": "1", "order_ip": "175.10.46.7", "order_remark": "", "receive_name": "xujian", "receive_tel": "15587087521", "receive_province": "河北", "receive_city": "石家庄市", "receive_region": "辛集市", "receive_street": "辛集镇", "receive_school": "","receive_floor": "","receive_address": "测试地址", "pay_discount": "0.00", "pay_price": "40.00",  "pay_time": "1557482291", "pay_status": "1", "discount_coupon": "0.00",  "discount_user": "0.00", "parcel_status": "1"  , "delivery_status": "1", "delivery_price": "0.00", "refund_price": "0.00", "stockout_status": "0", "user_email": "","user_tel": "", "user_nickname": "徐健", "show_name": "徐健",  "total_price": "40.00"}] ,    "orderGoods":[{"goods_no": "D875459294249_0",  "goods_price": "40.00", "goods_cost_price": "25.00", "goods_market_price": "40.00", "goods_weight": "0", "goods_options": [{ "id": 0, "name": "白虾", "value": "3-9钱" }], "goods_name": "白虾",  "goods_image": "", "goods_point": "0.00", "goods_unit": "个", "price_total": "40.00", "price_discount": "0.00", "discount_coupon": "0.00", "discount_user": "0.00", "delivery_id": "0", "delivery_type": "0",  "service_status": "0", "comment_status": "0"}]  }]}
     * @field object $payData 支付信息
     * @field integer $user_id  用户ID
     * @field string $has_no 订单号
     * @field string $time 支付时间
     * @field decimal $money  支付金额
     * @fiedl string $title 支付标题
     * @field string $remark 支付说明
     * @field string $pay_no 支付号
     * @field string $user_email 用户邮箱
     * @field string $user_tel 用户手机号
     * @field string $user_nickname 用户昵称
     * @field string $show_name 显示用户名称
     * @field string $show_time 显示时间
     * @field string $pay_type 支付类型
     * 
     * @field object $info 订单信息
     * @field integer $id  订单ID
     * @field integer $order_id 订单ID
     * @field integer $order_user_id 订单用户ID
     * @field string $order_no  订单号
     * @field decimal $order_price 订单金额
     * @field integer $order_status 订单状态
     * @field integer $order_sum 订单数
     * @field string $order_title 订单标题
     * @field string $order_image 订单图标
     * @field string $order_create_time 订单创建时间
     * @field string $order_complete_time 订单完成时间
     * @field string $order_close_time 订单关闭时间
     * @field integer $order_complete_status 订单完成状态
     * @field string $order_ip 下单IP地址
     * @field string $order_remark 订单说明
     * @field string $receive_name 收货人姓名
     * @field string $receive_tel 收货人电话
     * @field string $receive_province 收货省份
     * @field string $receive_city 收货城市
     * @field string $receive_region 收货区域
     * @field string $receive_street 收货街道
     * @field string $receive_school 收货学校
     * @field string $receive_floor 收货楼栋
     * @field string $receive_address 收货地址
     * @field string $pay_discount 优惠总价
     * @field string $pay_price 支付金额
     * @field string $pay_time 支付时间
     * @field integer $pay_status 支付状态
     * @field string $discount_coupon 优惠券折扣
     * @field string $discount_user 会员折扣
     * @field integer $parcel_status 配货状态
     * @field integer $delivery_status 发货状态
     * @field decimal $delivery_price 运费
     * @field decimal $refund_price 退款金额
     * @field integer $stockout_status  
     * @field string $user_email 用户邮箱
     * @field string $user_tel 用户手机号
     * @field string $user_nickname 用户昵称
     * @field string $show_name 显示用户名称
     * @field decimal $total_price 订单总价
     * 
     * @field object $orderGoods  订单商品信息
     * @field string $goods_no 商品货号
     * @field decimal $goods_price 商品价格
     * @field decimal $goods_cost_price 商品成本单价
     * @field decimal $goods_market_price 商品市场单价
     * @field string  $goods_weight 商品重量
     * @field string  $goods_options 商品规格
     * @field string  $goods_name 商品名称
     * @field string  $goods_image 商品图片
     * @field string  $goods_point 商品积分
     * @field string  $goods_unit 商品单位
     * @field decimal $price_total 商品付款价
     * @field decimal  $price_discount 优惠总价
     * @field decimal $discount_coupon 优惠券折扣
     * @field decimal $discount_user 会员折扣
     * @field integer  $delivery_id 快递单ID
     * @field integer  $delivery_type 物流类型
     * @field integer  $service_status 售后状态
     * @field integer  $comment_status 评论状态
     */

    public function info() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userId,
            'order_no' => $this->data['order_no']
        ])->info()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }


}
