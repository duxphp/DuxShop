<?php

/**
 * 公众号相关
 */

namespace app\wechat\middle;

class WechatMiddle extends \app\base\middle\BaseMiddle {

    public function wechat() {
        return target('wechat/Wechat', 'service')->init();
    }

    //永久二维码
    protected function perpetual($params) {
        $data = target('wechat/Wechat', 'service')->QrcodePerpetual($params);
        if(!$data) {
            return $this->stop(target('wechat/Wechat', 'service')->getError());
        }
        return $this->run($data);
    }

    //临时二维码
    protected function tmp($path, $parameter, $size = 430) {
        $data = target('wechat/Wechat', 'service')->QrcodeTmp($path, $parameter, $size);
        if(!$data) {
            return $this->stop(target('wechat/Wechat', 'service')->getError());
        }
        return $this->run($data);
    }

}
