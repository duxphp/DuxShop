<?php
  
namespace app\mall\api;

/**
 * 商品详情
 */

class InfoApi extends  \app\member\api\MemberApi {

    protected $_middle = 'mall/Info';

    /**
     * 商品详情
     * @method GET
     * @return integer $code 200
     * @return string $message ok
     * @return json $result {"treeList":[{"mall_id":1,"class_id":1,"brand_id":0,"supplier":0,"pos_id":"","type":"mall", "title":"海鲜产品","subtitle":"","image":"","keyword":"", "description":"...","sort":0,"create_time":"1554703768", "update_time": "1556507129","view": "102","attr_new": "1","attr_hot": "1","attr_rec": "1","attr_free": "1","goods_no": "D772443002428", "sell_price": "46.00","market_price": "88.00","cost_price": "78.00","up_time": "0","down_time": "0","store": "973","weight": "500","unit": "个", "sale": "26","spec_data": "","from_province": "湖南省","from_city": "长沙市","from_region": "jackchen", "freight_type": "0","freight_price": "0.00","freight_tpl": "1","service_status": "1","point_status": "2","invoice_status": "0", "discount_status": "0","class_name": "水产鱼"}]}
     * @field integer $mall_id 商品ID
     * @field integer $class_id 商品分类ID
     * @field integer $brand_id 品牌ID
     * @field integer $supplier_id  供货商ID
     * @field integer $pos_id 推荐位ID
     * @field string $type 类型商品
     * @field string $title 商品名称
     * @field string $subtitle 副标题
     * @field string $image 商品封面图
     * @field string $keyword 关键词 
     * @field string $description 描述 
     * @field integer $sort 顺序 
     * @field string $create_time 创建时间
     * @field string $update_time 更新时间
     * @field integer $view 浏览量
     * @field integer $attr_new 属性-新品
     * @field integer $attr_hot 属性-热门
     * @field integer $attr_rec 属性-推荐
     * @field integer $attr_free 属性-包邮
     * @field string $goods_no 商品编号
     * @field string $sell_price 销售价
     * @field string $market 市场价
     * @field string $cost_price 成本价
     * @field string $up_time 上架时间
     * @field string $down_time 下架时间
     * @field integer $store 库存
     * @field integer $weight 重量 
     * @field string $unit 单位
     * @field integer $sale 销售量
     * @field string $spec_data 规格数据
     * @field string $from_province 出产地-省份
     * @field string $from_city 出产地-城市
     * @field string $from_region 出产地-区域
     * @field integer $freight_type 运费类型
     * @field integer $freight_tpl 运费模板
     * @field string $freight_price 固定运费
     * @field integer $service_status 支持退换货
     * @field integer $point_status  购物送积分
     * @field integer $invoice_status  开具发票 
     * @field integer $discount_status  会员折扣
     * @field string $class_name 商品分类名称
     */

    public function index() {

        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['id'],
            'pro_id' => $this->data['pro_id'],
            'user_id' => $_SERVER['HTTP_AUTHUID'],
            'layer' => 'api'
        ])->classInfo()->data()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        }); 
    }
	
	
	/**
     * 修改购物车商品
     */
	public function spec() {
        target($this->_middle, 'middle')->setParams([
            'mall_id' => $this->data['id'],
            'pro_id' => $this->data['pro_id'],
        ])->spec()->export(function ($data) {
            $this->success('ok', $data);
        }, function ($message, $code, $url) {
            $this->error($message, $code);
        });
    }

}