<?php
namespace app\member\service;
/**
 * 积分处理
 */
class PointsService extends \app\base\service\BaseService {

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
            'force' => false
        ];
        if(empty($data['user_id'])) {
            return $this->error('无法识别用户!');
        }
        if(bccomp($data['money'], 0, 2) === -1) {
            return $this->error('处理积分不正确!');
        }
        $model = target('member/PointsAccount');
        $userInfo = $model->getWhereInfo([
            'A.user_id' => $data['user_id'],
        ]);
        if(empty($userInfo)) {
            $accountData = [
                'user_id' => $data['user_id'],
            ];
            $accountId = target('member/PointsAccount')->add($accountData);
            if(!$accountId) {
                return $this->error('账户创建失败！');
            }
            $userInfo = [
                'money' => 0,
            ];
        }
        //实际操作
        if (!$data['type']) {
            if(bccomp($userInfo['money'], $data['money'], 2) === -1) {
                return $this->error('账户积分不足,无法进行扣除!');
            }
        }
        if ($data['type']) {
            $status = $model->where(['user_id' => $data['user_id']])->setInc('money', $data['money']);
        } else {
            $status = $model->where(['user_id' => $data['user_id']])->setDec('money', $data['money']);
        }
        if (!$status) {
            return $this->error('账户积分处理失败,请稍候再试!');
        }
        if($data['type']){
            $status = $model->where(['user_id' => $data['user_id']])->setInc('charge', $data['money']);
        }else{
            $status = $model->where(['user_id' => $data['user_id']])->setInc('spend', $data['money']);
        }
        if(!$status){
            return $this->error('账户积分处理失败,请稍候再试!');
        }
        return $this->success();
    }

    /**
     * 账户余额
     * @param $userId
     * @return mixed
     */
    public function amountAccount($userId) {
        $info = target('member/PointsAccount')->getWhereInfo(['A.user_id' => $userId]);
        return $info['money'];
    }

}
