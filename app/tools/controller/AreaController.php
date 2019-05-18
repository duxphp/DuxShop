<?php

/**
 * 地区库
 */

namespace app\tools\controller;


class AreaController {

    /**
     * 首页
     */
    public function index() {
        target('tools/Area', 'middle')->setParams([
            'province' => request('', 'province'),
            'city' => request('', 'city'),
            'region' => request('', 'region')
        ])->index()->export(function ($data) {
            \dux\Dux::header(200, function() use ($data) {
                if(!headers_sent()) {
                    header('Content-Type: application/json;charset=utf-8;');
                }
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            });
        });
    }

}