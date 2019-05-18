<?php

/**
 * 首页信息
 */

namespace app\index\middle;

class IndexMiddle extends \app\base\middle\BaseMiddle {

    protected function data() {
        $label = str_len($this->params['label'], 20);
        $userId = intval($this->params['user_id']);
        $tplInfo = target('site/SiteTpl')->getWhereInfo([
            'label' => $label ? $label : 'index',
        ]);
        $elementType = target('site/SiteTpl')->elementType();
        $tplData = json_decode($tplInfo['content'], true);
        $data = [];
        foreach ($tplData as $key => $vo) {
            if ($elementType[$vo['tpl']]['hidden']) {
                if ($elementType[$vo['tpl']]['hidden']($vo)) {
                    unset($tplData[$key]);
                    continue;
                }
            }
            if ($elementType[$vo['tpl']]['process']) {
                $tplData[$key]['data'] = $elementType[$vo['tpl']]['process']($vo, $userId);
            }
            if ($elementType[$vo['tpl']]['data']) {
                $data = array_merge($data, $elementType[$vo['tpl']]['data']($vo, $userId));
            }
        }
        if($userId) {
            $couponList = target('marketing/MarketingCouponLog')->loadList([
                'A.user_id' => $userId,
                'A.show' => 1
            ]);
            if($couponList) {
                $data['coupon'] = [
                    'style' => (object)[],
                    'data' => $couponList
                ];
                target('marketing/MarketingCouponLog')->where(['user_id' => $userId])->data(['show' => 1])->update();
            }

        }
        return $this->run(array_merge($data, [
            'list' => array_values($tplData),
            'info' => $tplInfo,

        ]));
    }
}
