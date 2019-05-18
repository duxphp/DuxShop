<?php

/**
 * 支付设置
 */
namespace app\member\model;

use app\system\model\SystemModel;

class PayConfigModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'config_id',
        'validate' => [
            'type' => [
                'required' => ['', '类型参数获取不正确!', 'must', 'all'],
            ],
        ],
    ];

    /**
     * 获取配置
     * @param $type
     * @return mixed
     */
    public function getConfig($type) {
        $where = array();
        $where['type'] = $type;
        $info = $this->getWhereInfo($where);
        return unserialize($info['setting']);
    }

    public function typeInfo($type) {
        $list = hook('service', 'Pay', 'Type');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array) $data, (array) $value);
        }
        return $data[$type];
    }

    /**
     * 获取服务接口
     * @param bool $filter
     * @param string $platform
     * @param bool $internal
     * @param bool $array
     * @return array|mixed
     */
    public function typeList($filter = false, $platform = 'all', $internal = true, $array = false) {

        $list = hook('service', 'Pay', 'Type');
        $data = array();

        $configList = $this->loadList([], 0, 'sort asc');
        $configData = [];
        foreach ($configList as $vo) {
            $configData[$vo['type']] = $vo;
        }
        foreach ($list as $value) {
            $data = array_merge_recursive((array) $data, (array) $value);
        }
        $data = array_sort($data, 'order');
        foreach ($data as $key => $vo) {
            if ($vo['platform'] != 'all') {
                if ($vo['platform'] != $platform && $platform != 'all') {
                    unset($data[$key]);
                    continue;
                }
            }

            if ($vo['internal'] && !$internal) {
                unset($data[$key]);
                continue;
            }
            $setting = unserialize($configData[$key]['setting']);
            $data[$key]['password'] = $setting['password'];
            $data[$key]['type'] = $configData[$key]['type'];
            $data[$key]['status'] = $configData[$key]['status'];
            if ($filter) {
                if (!$configData[$key]['status']) {
                    unset($data[$key]);
                }
            }

        }

        if($array) {
            $newArray = [];
            foreach ($data as $key => $vo) {
                $newArray[] = $vo;
            }
            $data = $newArray;
        }

        return $data;
    }

    /**
     * 获取回调接口
     */
    public function callbackList() {
        $list = hook('service', 'Pay', 'Callback');
        $data = array();
        foreach ($list as $value) {
            $data = array_merge_recursive((array) $data, (array) $value);
        }
        return $data;
    }

}