<?php

/**
 * 系统首页
 */

namespace app\system\admin;

class IndexAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '系统首页',
                'description' => '系统基本信息参数',
            ],
        ];
    }

    /**
     * 首页
     */
    public function index() {

        $curTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $stopTime = time();

        $beforeTime = strtotime(date('Y-m-d', strtotime("-1 day")));
        $beforeStopTime = strtotime(date('Y-m-d'));


        //销售额
        $curOrderMoney = target('order/Order')->table('order')
            ->field(['sum(pay_price + delivery_price - refund_price) as num'])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                '_sql' => 'pay_time >= ' . $curTime . ' AND pay_time <= ' . $stopTime,
            ])->select();
        $curOrderMoney = $curOrderMoney[0]['num'];
        $this->assign('curOrderMoney', $curOrderMoney);
        $curOrderNum = target('order/Order')->table('order')->where([
            'order_status' => 1,
            'pay_status' => 1,
            '_sql' => 'pay_time >= ' . $curTime . ' AND pay_time <= ' . $stopTime,
        ])->count();
        $this->assign('curOrderMoney', price_format($curOrderMoney));
        $this->assign('curOrderNum', intval($curOrderNum));


        $beforeOrderMoney = target('order/Order')->table('order')
            ->field(['sum(pay_price + delivery_price) as num'])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                '_sql' => 'pay_time >= ' . $beforeTime . ' AND pay_time <= ' . $beforeStopTime,
            ])->select();
        $beforeOrderMoney = $beforeOrderMoney[0]['num'];
        $beforeOrderNum = target('order/Order')->table('order')->where([
            'order_status' => 1,
            'pay_status' => 1,
            '_sql' => 'pay_time >= ' . $beforeTime . ' AND pay_time <= ' . $beforeStopTime,
        ])->count();
        $this->assign('beforeOrderMoney', price_format($beforeOrderMoney));
        $this->assign('beforeOrderNum', intval($beforeOrderNum));


        $curUserNum = target('member/MemberUser')->where([
            '_sql' => 'reg_time >= ' . $curTime . ' AND reg_time <= ' . $stopTime,
        ])->count();
        $beforeUserNum = target('member/MemberUser')->where([
            '_sql' => 'reg_time >= ' . $beforeTime . ' AND reg_time < ' . $beforeStopTime,
        ])->count();
        $this->assign('curUserNum', intval($curUserNum));
        $this->assign('beforeUserNum', intval($beforeUserNum));


        $userNum = target('member/MemberUser')->count();
        $this->assign('userNum', $userNum);


        //商品访问量
        $mallViews = target('statis/StatisViews')->where([
            'species' => 'mall',
            '_sql' => 'date >= ' . date('Ymd', $curTime) . ' AND date <= ' . date('Ymd', $stopTime),
        ])->sum('num');
        $oldMallViews = target('statis/StatisViews')->where([
            'species' => 'mall',
            '_sql' => 'date >= ' . date('Ymd', $beforeTime) . ' AND date <= ' . date('Ymd', $beforeStopTime),
        ])->sum('num');
        $this->assign('mallViews', $mallViews);
        $this->assign('oldMallViews', $oldMallViews);
        $mallPeople = target('statis/StatisViews')->query("select COUNT(DISTINCT user_id) as `count`  from `{pre}statis_views` where species = 'mall' and user_id > 0 and date >= " . date('Ymd', $curTime) . " and date <= " . date('Ymd', $stopTime));
        $mallPeople = $mallPeople[0]['count'];
        $oldMallPeople = target('statis/StatisViews')->query("select COUNT(DISTINCT user_id) as `count`  from `{pre}statis_views` where species = 'mall' and user_id > 0 and date >= " . date('Ymd', $beforeTime) . " and date <= " . date('Ymd', $beforeStopTime));
        $oldMallPeople = $oldMallPeople[0]['count'];

        $this->assign('mallPeople', $mallPeople);
        $this->assign('oldMallPeople', $oldMallPeople);

        //商城访问量
        $siteViews = target('statis/StatisViews')->where([
            'species' => 'site',
            '_sql' => 'date >= ' . date('Ymd', $curTime) . ' AND date <= ' . date('Ymd', $stopTime),
        ])->sum('num');
        $oldSiteViews = target('statis/StatisViews')->where([
            'species' => 'site',
            '_sql' => 'date >= ' . date('Ymd', $beforeTime) . ' AND date <= ' . date('Ymd', $beforeStopTime),
        ])->sum('num');
        $this->assign('siteViews', $siteViews);
        $this->assign('oldSiteViews', $oldSiteViews);

        $sitePeople = target('statis/StatisViews')->query("select COUNT(DISTINCT user_id) as `count`  from `{pre}statis_views` where species = 'site' and user_id > 0 and date >= " . date('Ymd', $curTime) . " and date <= " . date('Ymd', $stopTime));
        $sitePeople = $sitePeople[0]['count'];
        $oldSitePeople = target('statis/StatisViews')->query("select COUNT(DISTINCT user_id) as `count`  from `{pre}statis_views` where species = 'site' and user_id > 0 and date >= " . date('Ymd', $beforeTime) . " and date <= " . date('Ymd', $beforeStopTime));
        $oldSitePeople = $oldSitePeople[0]['count'];
        $this->assign('sitePeople', $sitePeople);
        $this->assign('oldSitePeople', $oldSitePeople);


        $orderDeliveryNum = target('order/Order')->table('order')->where([
            'pay_status' => 1,
            'delivery_status' => 0,
        ])->count();
        $this->assign('orderDeliveryNum', $orderDeliveryNum);

        $orderCompleteNum = target('order/Order')->table('order')->where([
            'delivery_status' => 1,
            'order_complete_status' => 0,
        ])->count();
        $this->assign('orderCompleteNum', $orderCompleteNum);

        $orderRefundNum = target('order/OrderRefund')->where([
            'status[!]' => 0,
        ])->count();
        $this->assign('orderRefundNum', $orderRefundNum);

        $time = time();
        $mallNum = target('mall/Mall')->where([
            'status' => 1,
            '_sql' => "(up_time = 0 OR up_time <= {$time}) AND (down_time = 0 OR down_time >= {$time})",
        ])->count();
        $mallOutNum = target('mall/Mall')->where([
            'status' => 0,
            '_sql' => "(up_time = 0 OR up_time >= {$time}) AND (down_time = 0 OR down_time <= {$time})",
        ])->count();
        $this->assign('mallNum', $mallNum);
        $this->assign('mallOutNum', $mallOutNum);


        $startTime = date('Y-m-d 0:0:0', strtotime("-30 day"));
        $stopTime = date('Y-m-d  H:i:s');

        $data = target('order/Order')->query("select from_unixtime(pay_time, '%Y-%m-%d') as pay_date , sum(pay_price + delivery_price) as price_sum  from `{pre}order` where pay_status = 1 and order_status = 1 and pay_time >= " . strtotime($startTime) . " and pay_time <= " . strtotime($stopTime) . " group by pay_date");

        $statsLabel = [];
        for ($i = strtotime($startTime); $i <= strtotime($stopTime); $i += 86400) {
            $statsLabel[] = date('Y-m-d', $i);
        }

        $listOrderData = [];
        foreach ($data as $vo) {
            $listOrderData[$vo['pay_date']] += $vo['price_sum'];
        }

        $orderData = [];
        foreach ($statsLabel as $vo) {
            if ($listOrderData[$vo]) {
                $orderData[] = price_format($listOrderData[$vo]);
            } else {
                $orderData[] = 0;
            }
        }

        $orderBarJs = target('tools/Echarts', 'service')->bar('order-bar', $statsLabel, [
            [
                'name' => '销售总额',
                'data' => $orderData,
            ],
        ], 400);

        $weeksMoney = target('order/Order')->table('order')
            ->field(['sum(pay_price + delivery_price) as num'])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                '_sql' => 'pay_time >= ' . strtotime(date('Y-m-d 0:0:0', strtotime("-7 day"))) . ' AND pay_time <= ' . time(),
            ])->select();
        $weeksMoney = $weeksMoney[0]['num'];

        $monthMoney = target('order/Order')->table('order')
            ->field(['sum(pay_price + delivery_price) as num'])
            ->where([
                'order_status' => 1,
                'pay_status' => 1,
                '_sql' => 'pay_time >= ' . strtotime(date('Y-m-d 0:0:0', strtotime("-30 day"))) . ' AND pay_time <= ' . time(),
            ])->select();
        $monthMoney = $monthMoney[0]['num'];

        $this->assign([
            'orderBarJs' => $orderBarJs,
            'weeksMoney' => price_format($weeksMoney),
            'monthMoney' => price_format($monthMoney),
        ]);


        $this->systemDisplay();
    }

}