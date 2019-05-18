<?php

namespace app\marketing\service;
/**
 * 用户接口
 */
class MemberService extends \app\base\service\BaseService {


    public function reg($userId, $nickname) {
        // 新人赠券
        $time = time();
        $where = [];
        $where['A.status'] = 1;
        $where['A.start_time[<=]'] = $time;
        $where['A.stop_time[>=]'] = $time;
        $giftList = target('marketing/MarketingCouponGift')->loadList($where);
        foreach ($giftList as $key => $vo) {
            if(!target('marketing/MarketingCoupon')->giveCoupon($userId, $vo['coupon_id'], 1, 1)) {
                return $this->error(target('marketing/MarketingCoupon')->getError());
            }
        }

        //老带新
        $rec = request('', 'rec');
        if($rec) {
            $recData = explode('_', $rec);
            $recId = $recData[0];
            $recUserId = $recData[1];
            if($recId && $recUserId) {
                $recInfo = target('marketing/MarketingCouponRec')->getInfo($recId);
                if($recInfo) {
                    if(!target('marketing/MarketingCoupon')->giveCoupon($recUserId, $recInfo['old_coupon_id'], 1, 1)) {
                        return $this->error(target('marketing/MarketingCoupon')->getError());
                    }
                    if(!target('marketing/MarketingCoupon')->giveCoupon($userId, $recInfo['new_coupon_id'], 1, 1)) {
                        return $this->error(target('marketing/MarketingCoupon')->getError());
                    }
                    $status = target('marketing/MarketingCouponRecLog')->add([
                        'user_id' => $userId,
                        'rec_user_id' => $recUserId,
                        'rec_id' => $recId,
                        'time' => time()
                    ]);
                    if(!$status) {
                        return $this->error(target('marketing/MarketingCouponRecLog')->getError());
                    }

                }
            }
        }

        return true;
    }

    public function del($id) {
        if (!target('marketing/MarketingCouponLog')->where(['user_id' => $id])->delete()) {
            return false;
        }
        return true;
    }
}

