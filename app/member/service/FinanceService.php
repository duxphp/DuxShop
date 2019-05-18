<?php

namespace app\member\service;
/**
 * 财务处理
 */
class FinanceService extends \app\base\service\BaseService {

    /**
     * 操作账户
     * @param $data
     * @return bool
     */
    public function account($data) {
        $data = [
            'user_id' => intval($data['user_id']),
            'money' => price_format($data['money']),
            'type' => isset($data['type']) ? intval($data['type']) : 1,
        ];
        if (empty($data['user_id'])) {
            return $this->error('无法识别用户!');
        }
        if (bccomp($data['money'], 0, 2) === -1) {
            return $this->error('处理金额不正确!');
        }
        $model = target('member/PayAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $data['user_id'],
        ], true);
        if (empty($userInfo)) {
            $accountData = [
                'user_id' => $data['user_id'],
            ];
            $accountId = target('member/PayAccount')->add($accountData);
            if (!$accountId) {
                return $this->error('账户创建失败！');
            }
            $userInfo = [
                'money' => 0,
            ];
        }
        if (!$data['type']) {
            if (bccomp($userInfo['money'], $data['money'], 2) === -1) {
                return $this->error('账户余额不足,无法进行扣除!');
            }
        }
        if ($data['type']) {
            $status = $model->where(['user_id' => $data['user_id']])->setInc('money', $data['money']);
        } else {
            $status = $model->where(['user_id' => $data['user_id']])->setDec('money', $data['money']);
        }
        if (!$status) {
            return $this->error('账户资金操作失败,请稍候再试!');
        }

        if ($data['type']) {
            $status = $model->where(['user_id' => $data['user_id']])->setInc('charge', $data['money']);
        } else {
            $status = $model->where(['user_id' => $data['user_id']])->setInc('spend', $data['money']);
        }
        if (!$status) {
            return $this->error('账户资金操作失败,请稍候再试!');
        }
        return true;
    }

    /**
     * 账户余额
     * @param $userId
     * @return int
     */
    public function amountAccount($userId) {
        $info = target('member/PayAccount')->getWhereInfo(['A.user_id' => $userId]);
        return $info['money'] ? $info['money'] : 0;
    }
}
