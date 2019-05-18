<?php

/**
 * 消费排行
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;


class MemberRankingAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MemberUser';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '消费排行',
                'description' => '会员消费排行榜',
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

        switch ($order) {
            case 2:
                $orderSql = 'total_num desc';
                break;
            case 3:
                $orderSql = 'total_goods desc';
                break;
            default:
            case 1:
                $orderSql = 'total_money desc';
                break;
        }


        if ($startTime) {
            $startTime = strtotime($startTime);
        }
        if ($stopTime) {
            $stopTime = strtotime($stopTime . ' 23:59:59');
        }

        $where = [];

        if($keyword) {
            $where['OR'] = [
                'B.nickname[~]' => $keyword,
                'B.email' => $keyword,
                'B.tel' => $keyword,
            ];
        }

        $limit = 20;
        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $limit);


        $list = $model->table('member_user(A)')
            ->join('member_role(B)', ['B.role_id', 'A.role_id'])
            ->join('member_grade(C)', ['C.grade_id', 'A.grade_id'])
            ->field([
                'A.*',
                'B.name(role_name)',
                'C.name(grade_name)',
                'IFNULL((SELECT SUM(pay_price + delivery_price - refund_price) as order_money FROM {pre}order WHERE order_user_id = A.user_id AND order_status = 1 AND pay_status = 1 AND pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime . ' limit 1), 0.00) as total_money',
                'IFNULL((SELECT COUNT(*) as order_num FROM {pre}order WHERE order_user_id = A.user_id AND order_status = 1 AND pay_status = 1 AND pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime . ' limit 1), 0) as total_num',
                'IFNULL((SELECT COUNT(*) as goods_num FROM {pre}order_goods as order_goods INNER JOIN {pre}order as `order` ON order_goods.order_id = order.order_id WHERE order.order_user_id = A.user_id AND order.order_status = 1 AND order.pay_status = 1 AND pay_time >= ' . $startTime . ' AND pay_time <= ' . $stopTime . ' AND order_goods.service_status < 2 limit 1), 0) as total_goods',
            ])
            ->where($where)
            ->limit($pageData['limit'])
            ->order($orderSql . ',user_id desc')
            ->select();

        $page = request('', 'page', 1, 'intval');
        $page = $page - 1;

        foreach ($list as $key => $vo) {
            if(bccomp($vo['total_num'], 0, 2) === 1) {
                $list[$key]['avg_price'] = price_calculate($vo['total_money'], '/', $vo['total_num']);
            }else {
                $list[$key]['avg_price'] = 0.00;
            }
            $list[$key]['ranking'] = $page * $limit + $key + 1;
            $list[$key]['show_name'] = target('member/MemberUser')->getNickname($vo['nickname'], $vo['tel'], $vo['email']);
            $list[$key]['avatar'] = target('member/MemberUser')->getAvatar($vo['avatar']);
        }

        $this->assign('list', $list);
        $this->assign('page', $pageData['html']);
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