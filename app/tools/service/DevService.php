<?php

namespace app\tools\service;

class DevService {

    /**
     * 京东地区库转换
     *
     * @return void
     */
    public function jdArea() {
        $provinceFile = ROOT_PATH . 'public/common/js/package/area/level1.json';
        $cityFile = ROOT_PATH . 'public/common/js/package/area/level2.json';
        $countryFile = ROOT_PATH . 'public/common/js/package/area/level3.json';
        $townFile = ROOT_PATH . 'public/common/js/package/area/level4.json';
        $str = file_get_contents($provinceFile);
        $provinceData = json_decode($str, true);
        $provinceData = [$provinceData];
        $str = file_get_contents($cityFile);
        $cityData = json_decode($str, true);
        $str = file_get_contents($countryFile);
        $countryData = json_decode($str, true);
        $data = $provinceData + $cityData + $countryData;

        file_put_contents(ROOT_PATH . 'public/common/js/package/distpicker/area.json', json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    public function jdStreet() {
        $townFile = ROOT_PATH . 'public/common/js/package/area/level4.json';
        $str = file_get_contents($townFile);
        $townData = json_decode($str, true);
        foreach($townData as $key => $vo) {
            if(empty($vo)) {
                continue;
            }
            file_put_contents(ROOT_PATH . 'public/common/js/package/distpicker/area/'.$key.'.json', json_encode($vo, JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * 人人商城地区库转换
     *
     * @return void
     */
    public function parsingArea() {
        $areaFile = ROOT_PATH . 'public/common/js/package/area/AreaNew.xml';
        $str = file_get_contents($areaFile);
        $xml = simplexml_load_string($str);
        $xmljson = json_encode($xml);
        $xml = json_decode($xmljson, true);

        // 重置数据
        foreach ($xml['province'] as $key => $province) {
            if ($province['city']['@attributes']) {
                $xml['province'][$key]['city'] = [$province['city']];
            }
        }
        foreach ($xml['province'] as $key => $province) {
            foreach ($province['city'] as  $k => $city) {
                if($city['county']['@attributes']) {
                    $xml['province'][$key]['city'][$k]['county'] = [$city['county']];
                }
            }
        }
        $areaData = [];
        foreach ($xml['province'] as $key => $province) {
            if ($key == 0) {
                continue;
            }
            // 省
            $areaData[0][$province['@attributes']['code']] = $province['@attributes']['name'];
            // 市
            foreach ($province['city'] as  $city) {
                $areaData[$province['@attributes']['code']][$city['@attributes']['code']] = $city['@attributes']['name'];
                foreach ($city['county'] as $county) {
                    $areaData[$city['@attributes']['code']][$county['@attributes']['code']] = $county['@attributes']['name'];
                }
            }
            
        }

        file_put_contents(ROOT_PATH . 'public/common/js/package/distpicker/area.json', json_encode($areaData, JSON_UNESCAPED_UNICODE));

    }

    public function parsingAreaList() {


        $areaFile = ROOT_PATH . 'public/common/js/package/area/list/*/*.xml';
        $fileList = glob($areaFile);
        foreach($fileList as $file) {
            $this->_parsingList($file);
        }

    }

    private function _parsingList($file) {
        $str = file_get_contents($file);
        $xml = simplexml_load_string($str);
        $xmljson = json_encode($xml);
        $xml = json_decode($xmljson, true);

        // 重置数据
        if ($xml['city']['county']['@attributes']) {
            $xml['city']['county'] = [$xml['city']['county']];
        }
        
        foreach ($xml['city']['county'] as $key => $county) {
            if($county['street']['@attributes']) {
                $xml['city']['county'][$key]['street'] = [$county['street']];
            }
        }

        $data = [];

        foreach ($xml['city']['county'] as $key => $county) {
            if(empty($county['street'])) {
                continue;
            }
            foreach($county['street'] as $k => $v) {
                $data[$county['@attributes']['code']][$v['@attributes']['code']] = $v['@attributes']['name'];
            }
        }
        if(empty($data)) {
            return false;
        }

        file_put_contents(ROOT_PATH . 'public/common/js/package/distpicker/area/'.$xml['city']['@attributes']['code'].'.json', json_encode($data, JSON_UNESCAPED_UNICODE));


    }

}
