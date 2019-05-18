<?php

/**
 * 账户提现
 */

namespace app\member\middle;


class CashMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/PayCash';



    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        $this->params['limit'] = intval($this->params['limit']);
        if ($type == 1) {
            $where['A.status'] = 1;
        }
        if ($type == 2) {
            $where['A.status'] = 2;
        }
        if ($type == 3) {
            $where['A.status'] = 0;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'create_time desc');
        $list = $model->showList($list);

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit,
        ]);
    }

    protected function info() {
        $cashNo = html_clear($this->params['no']);
        $info = target('member/PayCash')->getWhereInfo([
            'A.cash_no' => $cashNo,
            'A.user_id' => intval($this->params['user_id']),
        ]);
        $info = target('member/PayCash')->showInfo($info);
        if (empty($info)) {
            return $this->stop('该提现单不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }

    protected function apply() {
        $cardList = target('member/PayCard')->loadList(['A.user_id' => $this->params['user_id']]);
        $cardList = target('member/PayCard')->showList($cardList);
        $config = target('member/MemberConfig')->getConfig();
        $info = target('member/MemberAccount')->getWhereInfo([
            'A.user_id' => $this->params['user_id'],
        ]);
        return $this->run([
            'config' => [
                'clear_type' => unserialize($config['clear_type']),
                'clear_tax' => $config['clear_tax'],
                'clear_info' => html_out($config['clear_info']),
            ],
            'cardList' => $cardList,
            'info' => $info,
        ]);
    }

    protected function submit() {
        $type = intval($this->params['type']);
        $money = intval($this->params['money']);
        $userId = $this->params['user_id'];
        $platform = $this->params['platform'];
        if (empty($money)) {
            return $this->stop('请输入提现金额！');
        }

        if (bccomp($money, 0, 2) !== 1) {
            return $this->stop('可提现金额不足!');
        }

        $config = target('member/MemberConfig')->getConfig();

        //手续费计算
        if ($config['clear_tax']) {
            $tex = price_calculate($money, '*', $config['clear_tax'] / 100);
        } else {
            $tex = 0;
        }

        //提现方式
        $clearType = unserialize($config['clear_type']);
        if (!in_array($type, $clearType)) {
            return $this->stop('暂不支持该提现方式！');
        }

        //提现条件
        $clearAudit = unserialize($config['clear_audit']);

        //最低提现
        if (bccomp($money, $config['clear_withdraw'], 2) === -1) {
            return $this->stop('最少提现额度为' . $config['clear_withdraw'] . '元');
        }

        //提现次数
        if ($config['clear_num']) {
            $count = target($this->_model)->countList([
                'A.user_id' => $userId,
                'A.status[!]' => 0,
                'A.create_time[>=]' => mktime(0, 0, 0, date('m'), 1, date('Y')),
                'A.create_time[<=]' => mktime(23, 59, 59, date('m'), date('t'), date('Y')),
            ]);
            if ($count >= $config['clear_num']) {
                return $this->stop('当月提现次数已满，请于下月继续提现！');
            }
        }

        //提现额度
        if ($config['clear_withdraw_max']) {
            $sum = target($this->_model)->where([
                'user_id' => $userId,
                'status[!]' => 0,
                'create_time[>=]' => mktime(0, 0, 0, date('m'), 1, date('Y')),
                'create_time[<=]' => mktime(23, 59, 59, date('m'), date('t'), date('Y')),
            ])->sum('money');
            if ($sum >= $config['clear_withdraw_max']) {
                return $this->stop('当月提现额度已满，请于下月继续提现！');
            }
        }

        //免手续费额度
        if ($config['clear_tax_free']) {
            $sum = target($this->_model)->where([
                'user_id' => $userId,
                'status[!]' => 0,
                'create_time[>=]' => mktime(0, 0, 0, date('m'), 1, date('Y')),
                'create_time[<=]' => mktime(23, 59, 59, date('m'), date('t'), date('Y')),
            ])->sum('money');
            $sum = price_calculate($sum, '+', $money);
            if ($sum <= $config['clear_tax_free']) {
                $tex = 0;
            }
        }

        $realInfo = target('member/MemberReal')->where([
            'user_id' => $this->params['user_id'],
        ])->find();
        if ($realInfo['status'] <> 2) {
            return $this->stop('您的账户暂时没有通过实名认证！');
        }
        if ($type == 3) {
            $cardInfo = target('member/PayCard')->getWhereInfo([
                'A.card_id' => $this->params['card_id'],
                'A.user_id' => $this->params['user_id'],
            ]);
            if (empty($cardInfo)) {
                return $this->stop('银行卡选择不正确！');
            }
        } else {
            $cardInfo = [
                'account' => html_clear($this->params['account']),
                'account_name' => $realInfo['name'],
            ];
        }
        if($type == 2 && empty($cardInfo['account'])) {
            return $this->stop('请填写支付宝账号！');
        }

        target($this->_model)->beginTransaction();
        $cashNo = log_no();
        //提现记录
        $data = [
            'cash_no' => $cashNo,
            'user_id' => $userId,
            'money' => $money,
            'tax' => $tex,
            'create_time' => time(),
            'status' => 1,
            'platform' => $platform,
            'type' => $type,
            'bank' => $cardInfo['bank'],
            'bank_label' => $cardInfo['label'],
            'bank_type' => $cardInfo['type'],
            'account' => $cardInfo['account'],
            'account_name' => $cardInfo['account_name'],
        ];
        $cashId = target($this->_model)->add($data);
        if (!$cashId) {
            target($this->_model)->rollBack();
            return $this->stop('提现申请失败!');
        }
        //实付款
        $totalMoney = price_calculate($money, '+', $tex);
        $status = target('member/Finance', 'service')->account([
            'user_id' => $userId,
            'money' => $totalMoney,
            'type' => 0,
        ]);
        if (!$status) {
            return $this->stop(target('member/Finance', 'service')->getError());
        }
        $accountStatus = target('statis/Finance', 'service')->account([
            'user_id' => $userId,
            'species' => 'member_account',
            'sub_species' => 'cash',
            'money' => $totalMoney,
            'type' => 0,
            'title' => '余额提现',
            'remark' => '用户提现扣款',
        ]);
        if (!$accountStatus) {
            return $this->stop(target('statis/Finance', 'service')->getError());
        }
        $userInfo = target('member/MemberUser')->getInfo($userId);
        $cashType = target('member/PayCash')->type();
        //提现无需审核
        if (!in_array($type, $clearAudit) && $type <> 3) {
            //转账操作
            $data = target($this->_model)->transfer($type, $userId, $money, $cashNo, $platform);
            if (!$data) {
                return $this->stop(target($this->_model)->getError());
            }
            target($this->_model)->edit([
                'cash_id' => $cashId,
                'status' => 2,
                'complete_time' => time(),
                'pay_no' => $data['pay_no'],
                'pay_name' => $data['pay_name'],
                'pay_way' => $data['pay_way'],
            ]);
            $msg = '提现处理成功，请等待支付方结算！';
            target('tools/Tools', 'service')->notice('member', 'cashcom', $userId, [
                '昵称' => $userInfo['show_name'],
                '金额' => $money,
                '提现方式' => $cashType[$type],
                '时间' => date('Y-m-d H:i:s', time()),
            ], 'pages/wallet/index');
        } else {
            $msg = '提现申请成功，请等待提现结果！';
            target('tools/Tools', 'service')->notice('member', 'cash', $userId, [
                '昵称' => $userInfo['show_name'],
                '金额' => $money,
                '提现方式' => $cashType[$type],
                '时间' => date('Y-m-d H:i:s', time()),
            ], 'pages/wallet/index');
        }
        target($this->_model)->commit();
        return $this->run([], $msg);
    }


}