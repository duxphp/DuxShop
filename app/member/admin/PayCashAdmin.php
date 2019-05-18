<?php

/**
 * 提现管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayCashAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayCash';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '提现管理',
                'description' => '管理用户提现记录',
            ],
            'fun' => [
                'index' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'C.tel,C.email,C.nickname',
            'cash_no' => 'A.cash_no',
            'status' => 'A.status',
            'type' => 'A.type',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
        ];
    }

    public function _indexOrder() {
        return 'cash_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['A.status'] > 2) {
            unset($whereMaps['A.status']);
        }
        if ($whereMaps['A.type']) {
            $whereMaps['A.type'] = $whereMaps['A.type'] -1;
        }
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'A.create_time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.create_time <= ' . $stopTime;
        }

        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        return $whereMaps;
    }

    protected function _indexAssign($pageMaps) {
        return [
            'typeList' => target($this->_model)->type(),
        ];
    }

    public function complete() {
        $id = request('get', 'id');
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('没有可处理记录!');
        }
        target($this->_model)->beginTransaction();
        $data = [];
        if($info['type'] <> 3) {
            $data = target($this->_model)->transfer($info['type'], $info['user_id'], $info['money'], $info['cash_no'], $info['platform']);
            if (!$data) {
                $this->error(target($this->_model)->getError());
            }
        }
        target($this->_model)->edit([
            'cash_id' => $info['cash_id'],
            'status' => 2,
            'complete_time' => time(),
            'pay_no' => $data['pay_no'],
            'pay_name' => $data['pay_name'],
            'pay_way' => $data['pay_way'],
            'auth_admin' => $this->userInfo['user_id'],
            'auth_time' => time(),
        ]);
        $userInfo = target('member/MemberUser')->getInfo($info['user_id']);
        $cashType = target('sale/SaleAccountCash')->type();
        target('tools/Tools', 'service')->notice('sale', 'cashcom', $info['user_id'], [
            '昵称' => $userInfo['show_name'],
            '金额' => $info['money'],
            '提现方式' => $cashType[$info['type']],
            '时间' => date('Y-m-d H:i:s', time()),
        ], 'pages/wallet/index');
        target($this->_model)->commit();
        $this->success('提现处理完成！');
    }

    public function refused() {
        $id = request('get', 'id');
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('没有可处理记录!');
        }
        target($this->_model)->beginTransaction();
        $data = [
            'auth_admin' => $this->userInfo['user_id'],
            'auth_time' => time(),
            'status' => 0,
            'complete_time' => time()
        ];
        $accountStatus = target('member/Finance', 'service')->account([
            'user_id' => $info['user_id'],
            'money' => $info['money'],
            'type' => 1,
        ]);
        if (!$accountStatus) {
            $this->error(target('member/Finance', 'service')->getError());
        }
        $accountStatus = target('statis/Finance', 'service')->account([
            'user_id' => $info['user_id'],
            'species' => 'member_account',
            'sub_species' => 'cash',
            'no' => $info['cash_no'],
            'money' => $info['money'],
            'type' => 1,
            'title' => '提现退回',
            'remark' => '提现申请失败退回处理',
        ]);
        if (!$accountStatus) {
            $this->error(target('statis/Finance', 'service')->getError());
        }
        target($this->_model)->where(['cash_id' => $info['cash_id']])->data($data)->update();

        $userInfo = target('member/MemberUser')->getInfo($info['user_id']);
        $cashType = target('sale/SaleAccountCash')->type();
        target('tools/Tools', 'service')->notice('sale', 'cashfail', $info['user_id'], [
            '昵称' => $userInfo['show_name'],
            '金额' => $info['money'],
            '提现方式' => $cashType[$info['type']],
            '时间' => date('Y-m-d H:i:s', time()),
        ], 'member/wallet/index');

        target($this->_model)->commit();
        $this->success('提现处理完成！');
    }

    public function export() {
        $params = [
            'C.tel' => request('get', 'keyword'),
            'A.status' => request('get', 'status'),
            'A.type' => request('get', 'type'),
            'A.cash_no' => request('get', 'cash_no'),
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
        ];
        $params = array_filter($params);

        $where = $this->_indexWhere($params);

        $list = target($this->_model)->loadList($where);

        if (empty($list)) {
            $this->error('没有数据需要导出!');
        }

        $table = [];
        $table[] = ['流水号', '实付金额', '用户', '提交时间', '提现类型', '开户行', '银行标识', '账户姓名', '账户', '状态'];

        foreach ($list as $vo) {
            $status = '';
            if (!$vo['status']) {
                $status = '提现失败';
            }
            if ($vo['status'] == 1) {
                $status = '提现中';
            }
            if ($vo['status'] == 2) {
                $status = '提现完成';
            }

            $data = [$vo['cash_no'], $vo['money'] - $vo['tax_money'], $vo['show_name'], date('Y-m-d H:i:s', $vo['create_time']), $vo['type_name'], $vo['bank'], $vo['bank_label'], $vo['account_name'], $vo['account'], $status];
            foreach ($data as $k => $v) {
                $data[$k] = str_replace(',', '_', $v);
            }
            $table[] = $data;
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=data.csv');

        $tableStr = '';
        foreach ($table as $vo) {
            $tableStr .= implode(',', $vo) . PHP_EOL;
        }
        echo iconv('utf-8', 'gbk//TRANSLIT', $tableStr);
    }

}