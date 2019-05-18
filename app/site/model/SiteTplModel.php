<?php

/**
 * 模板管理
 */
namespace app\site\model;

use app\system\model\SystemModel;

class SiteTplModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'tpl_id',
    ];

    public function _saveBefore($data, $type) {

        $tplData = $_POST['content'];

        foreach ($tplData as $key => $vo) {
            if(empty($vo['data'])) {
                $tplData[$key]['data'] = [];
            }
        }

        $data['content'] = json_encode($tplData, JSON_UNESCAPED_UNICODE);
        return $data;
    }

    public function elementType() {
        $list = hook('service', 'Type', 'Element');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge((array)$data, (array)$value);
        }
        foreach ($data as $key => $vo) {
            $data[$key]['tpl'] = $key;
        }
        return $data;
    }


}