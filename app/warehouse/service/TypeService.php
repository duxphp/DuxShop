<?php

namespace app\warehouse\service;
/**
 * 类型接口
 */
class TypeService {

    public function getPosType() {
        return [
            'yilianyun' => [
                'name' => '易联云',
                'target' => 'warehouse/Yilianyun',
                'desc' => '请自行申请易联云接口',
                'url' => 'http://dev.10ss.net/',
                'configRule' => [
                    'id' => '应用ID',
                    'key' => '应用密钥',
                ],
            ],
        ];
    }

}
