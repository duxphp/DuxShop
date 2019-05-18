<?php

/**
 * 地区库
 */

namespace app\tools\middle;

class AreaMiddle extends \app\base\middle\BaseMiddle {

    protected function index() {
        $province = $this->params['province'];
        $city = $this->params['city'];
        $region = $this->params['region'];

        $areaFile = ROOT_PATH . 'public/common/js/package/distpicker/area.json';
        $areaData = \json_decode(file_get_contents($areaFile), true);

        $provinceData = $areaData[0];
        if(empty($province)) {
            return $this->run([
                'province' => $provinceData,
            ]);
        }

        $provinceDataFlip = array_flip($provinceData);
        $cityData = $areaData[$provinceDataFlip[$province]];
        if(empty($city)) {
            return $this->run([
                'province' => $provinceData,
                'city' => $cityData
            ]);
        }

        $cityDataFlip = array_flip($cityData);
        $regionData = $areaData[$cityDataFlip[$city]];
        if(empty($region)) {
            return $this->run([
                'province' => $provinceData,
                'city' => $cityData,
                'region' => $regionData
            ]);
        }
        $regionDataFlip = array_flip((array)$regionData);
        $regionId = $regionDataFlip[$region];
        
        $streetFile = ROOT_PATH . 'public/common/js/package/distpicker/area/'.$regionId.'.json';
        if(!is_file($streetFile)) {
            return $this->run([
                'province' => $provinceData,
                'city' => $cityData,
                'region' => $regionData,
                'street' => []
            ]);
        }
        $streetData = \json_decode(file_get_contents($streetFile), true);

        return $this->run([
            'province' => $provinceData,
            'city' => $cityData,
            'region' => $regionData,
            'street' => $streetData
        ]);

    }

}