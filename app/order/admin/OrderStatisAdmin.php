<?php

/**
 * 订单统计
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;


class OrderStatisAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'Order';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '订单统计',
                'description' => '订单统计信息',
            ],
            'fun' => [
                'index' => true,
            ],
        ];
    }

    public function index() {
        $pageMaps = [];
        $startTime = request('', 'start_time', 0);
        $stopTime = request('', 'stop_time', 0);

        if (empty($startTime) || empty($stopTime)) {
            $startTime = date('Y-m-d', strtotime("-30 day"));
            $stopTime = date('Y-m-d');
        }
        $pageMaps['start_time'] = $startTime;
        $pageMaps['stop_time'] = $stopTime;

        $startTime = strtotime($startTime);
        $stopTime = strtotime($stopTime . '23:59:59');

        //订单数量统计
        $orderPayNum = target('order/Order')->table('order')->where([
            'order_status' => 1,
            'pay_status' => 1,
            '_sql' => 'pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime,
        ])->count();
        $orderCancelNum = target('order/Order')->table('order')->where([
            'order_status' => 0,
            '_sql' => 'order_close_time >= ' . $startTime . ' AND order_close_time <= ' . $stopTime,
        ])->count();

        $orderNumJs = target('tools/Echarts', 'service')->pie('order-num', '订单数量统计', [
            [
                'name' => '下单量 (' . $orderPayNum . ')',
                'value' => $orderPayNum,
            ],
            [
                'name' => '取消量 (' . $orderCancelNum . ')',
                'value' => $orderCancelNum,
            ],
        ], 230, false);
        $this->assign('orderNumJs', $orderNumJs);

        //销售数量统计
        $salePayNum = target('order/Order')->table('order_goods(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->where([
                'B.order_status' => 1,
                'B.pay_status' => 1,
                'A.service_status[!]' => 2,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('goods_qty');

        $saleCancelNum = target('order/Order')->table('order_goods(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->where([
                'OR' => [
                    'AND #1' => [
                        'B.order_status' => 0,
                        'B.pay_status' => 1,
                    ],
                    'AND #2' => [
                        'A.service_status' => 2,
                    ],
                ],
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('goods_qty');
        $saleNumJs = target('tools/Echarts', 'service')->pie('sale-num', '商品销量统计', [
            [
                'name' => '销售量 (' . $salePayNum . ')',
                'value' => $salePayNum,
            ],
            [
                'name' => '退款量 (' . $saleCancelNum . ')',
                'value' => $saleCancelNum,
            ],
        ], 230, false);
        $this->assign('saleNumJs', $saleNumJs);

        //销售额统计
        $salePayMoney = target('order/Order')->table('order')
            ->where([
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('pay_price');
        $saleDeliveryMoney = target('order/Order')->table('order')
            ->where([
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('delivery_price');
        $saleDiscountMoney = target('order/Order')->table('order')
            ->where([
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('pay_discount');
        $saleRefundMoney = target('order/Order')->table('order')
            ->where([
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->sum('refund_price');
        $saleMoneyJs = target('tools/Echarts', 'service')->pie('sale-money', '销售额统计', [
            [
                'name' => '下单金额 (' . $salePayMoney . ')',
                'value' => $salePayMoney,
            ],
            [
                'name' => '运费金额 (' . $saleDeliveryMoney . ')',
                'value' => $saleDeliveryMoney,
            ],
            [
                'name' => '优惠金额 (' . $saleDiscountMoney . ')',
                'value' => $saleDiscountMoney,
            ],
            [
                'name' => '取消金额 (' . $saleRefundMoney . ')',
                'value' => $saleRefundMoney,
            ],
        ], 230, false);
        $this->assign('saleMoneyJs', $saleMoneyJs);

        $turnover = price_calculate($salePayMoney, '+', $saleDeliveryMoney);
        $turnover = price_calculate($turnover, '-', $saleRefundMoney);
        $this->assign('turnover', $turnover);

        //订单转换率
        $siteViews = target('statis/StatisViews')->where([
            'species' => 'site',
            '_sql' => 'date >= ' . date('Ymd', $startTime) . ' AND date <= ' . date('Ymd', $stopTime),
        ])->sum('num');
        $orderNum = target('order/Order')->table('order')->where([
            '_sql' => 'order_create_time >= ' . $startTime . ' AND order_create_time <= ' . $stopTime,
        ])->count();
        $percentJs = target('tools/Echarts', 'service')->funnel('order-percent', '订单转换率', [
            [
                'name' => '访问量',
                'value' => $siteViews,
            ],
            [
                'name' => '下单量',
                'value' => $orderNum,
            ],
            [
                'name' => '支付量',
                'value' => $orderPayNum,
            ],
        ], 230);
        $this->assign('percentJs', $percentJs);
        $userNum = target('order/Order')->table('order')
            ->field(['COUNT(DISTINCT order_user_id) as num'])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->select();
        $userNum = $userNum[0]['num'];

        if($userNum) {
            $avgPrice = price_calculate($turnover, '/', $userNum);
        }else {
            $avgPrice = 0.00;
        }
        $this->assign('avgPrice', $avgPrice);

        $statsLabel = [];
        for ($i = $startTime; $i <= $stopTime; $i += 86400) {
            $statsLabel[] = date('Y-m-d', $i);
        }

        //交易额统计
        $moneyData = target('order/Order')->table('order')
            ->field(["from_unixtime(pay_time, '%Y-%m-%d') as pay_date", "sum(pay_price + delivery_price - refund_price) as price_sum"])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->group('pay_date')->select();

        $orderList = [];
        foreach ($moneyData as $vo) {
            $orderList[$vo['pay_date']] += $vo['price_sum'];
        }

        $orderData = [];
        foreach ($statsLabel as $vo) {
            if ($orderList[$vo]) {
                $orderData[] = price_format($orderList[$vo]);
            } else {
                $orderData[] = 0;
            }
        }

        $numData = target('order/Order')->table('order')
            ->field(["from_unixtime(pay_time, '%Y-%m-%d') as pay_date", "count(*) as num"])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->group('pay_date')->select();

        $numList = [];
        foreach ($numData as $vo) {
            $numList[$vo['pay_date']] += $vo['num'];
        }

        $orderNumData = [];
        foreach ($statsLabel as $vo) {
            if ($numList[$vo]) {
                $orderNumData[] = intval($numList[$vo]);
            } else {
                $orderNumData[] = 0;
            }
        }

        $mallData = target('order/Order')->table('order_goods(A)')
            ->join('order(B)', ['B.order_id', 'A.order_id'])
            ->field(["from_unixtime(B.pay_time, '%Y-%m-%d') as pay_date", "SUM(goods_qty) as num"])
            ->where([
                'B.order_status' => 1,
                'B.pay_status' => 1,
                'A.service_status[!]' => 2,
                'pay_time[>=]' => $startTime,
                'pay_time[<=]' => $stopTime,
            ])->group('pay_date')->select();

        $mallList = [];
        foreach ($mallData as $vo) {
            $mallList[$vo['pay_date']] += $vo['num'];
        }

        $mallNumData = [];
        foreach ($statsLabel as $vo) {
            if ($mallList[$vo]) {
                $mallNumData[] = intval($mallList[$vo]);
            } else {
                $mallNumData[] = 0;
            }
        }

        $orderBarJs = target('tools/Echarts', 'service')->bar('order-bar', $statsLabel, [
            [
                'name' => '成交总额',
                'data' => $orderData,
            ],
            [
                'name' => '订单成交量',
                'data' => $orderNumData,
            ],
            [
                'name' => '商品销量',
                'data' => $mallNumData,
            ],
        ], 400);
        $this->assign('orderBarJs', $orderBarJs);

        $dateParams = [
            [
                'start_time' => date('Y-m-d', time()),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-7 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-15 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
            [
                'start_time' => date('Y-m-d', strtotime('-30 day')),
                'stop_time' => date('Y-m-d', time()),
            ],
        ];
        $this->assign('dateParams', $dateParams);
        $this->assign('pageMaps', $pageMaps);

        $this->systemDisplay();
    }

}