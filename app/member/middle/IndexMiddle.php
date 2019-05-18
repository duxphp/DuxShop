<?php

/**
 * 会员中心
 */

namespace app\member\middle;


class IndexMiddle extends \app\base\middle\BaseMiddle {


    private $config = [];

    protected function meta($title = '会员中心', $name = '会员中心') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => $name,
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        if ($this->config) {
            return $this->config;
        }
        $this->config = target('member/memberConfig')->getConfig();
        return $this->config;
    }


    protected function data() {
        $this->config = $this->getConfig();
        $userInfo = $this->params['user_info'];
        $platform = $this->params['platform'];

        $return = [];
        return $this->run($return);
    }

    protected function about() {
        $this->config = $this->getConfig();
        return $this->run([
            'content' => html_out($this->config['reg_info'])
        ]);
    }

}