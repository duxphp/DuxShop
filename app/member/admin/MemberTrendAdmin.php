<?php

/**
 * 会员统计
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;


class MemberTrendAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberUser';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '会员统计',
                'description' => '统计会员数量信息',
            ],
            'fun' => [
                'index' => true,
            ],
        ];
    }

    public function index() {
        $keyword = request('', 'keyword');
        $startTime = request('', 'start_time', 0);
        $stopTime = request('', 'stop_time', 0);
        $order = request('', 'order');

        if (empty($startTime)) {
            $startTime = date('Y-m-d', strtotime('-30 day'));

        }
        if (empty($stopTime)) {
            $stopTime = date('Y-m-d', time());
        }
        $pageMaps = [
            'keyword' => $keyword,
            'start_time' => $startTime,
            'stop_time' => $stopTime,
            'order' => $order,
        ];
        if ($startTime) {
            $startTime = strtotime($startTime);
        }
        if ($stopTime) {
            $stopTime = strtotime($stopTime . ' 23:59:59');
        }

        //会员数量
        $userAllNum = target('member/MemberUser')->countList();
        $this->assign('userAllNum', $userAllNum);

        $userNum = target('member/MemberUser')->countList([
            'reg_time[>=]' => $startTime,
            'reg_time[<=]' => $stopTime,
        ]);
        $this->assign('userNum', $userNum);

        //会员注册趋势
        $statsLabel = [];
        for ($i = $startTime; $i <= $stopTime; $i += 86400) {
            $statsLabel[] = date('Y-m-d', $i);
        }
        $data = target('member/MemberUser')
            ->field(["from_unixtime(reg_time, '%Y-%m-%d') as reg_date", "count(*) as num"])
            ->where([
                'reg_time[>=]' => $startTime,
                'reg_time[<=]' => $stopTime,
            ])->group('reg_date')->select();
        $numList = [];
        foreach ($data as $vo) {
            $numList[$vo['reg_date']] += $vo['num'];
        }
        $numData = [];
        foreach ($statsLabel as $vo) {
            if ($numList[$vo]) {
                $numData[] = intval($numList[$vo]);
            } else {
                $numData[] = 0;
            }
        }
        $numBarJs = target('tools/Echarts', 'service')->bar('num-bar', $statsLabel, [
            [
                'name' => '注册会员',
                'data' => $numData,
            ],
        ], 400);
        $this->assign('numBarJs', $numBarJs);


        $this->assign('pageMaps', $pageMaps);

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

        $this->systemDisplay();
    }

}