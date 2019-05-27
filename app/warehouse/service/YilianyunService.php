<?php

namespace app\warehouse\service;
/**
 * 快递鸟查询
 */
class YilianyunService extends \app\base\service\BaseService {

    public function print($orderNo, $data, $driverId, $tpl, $type = 'text') {
        $driverInfo = target('warehouse/WarehousePosDriver')->getInfo($driverId);
        if(empty($driverInfo)) {
            return $this->error('打印机不存在！');
        }
        $config = target('warehouse/WarehouseConfigPos')->getConfig('yilianyun');
        $url = 'https://open-api.10ss.net/print/index';

        $time = time();
        $sign = md5($config['id'] . $time . $config['key']);

        $content = \dux\Dux::view()->fetch($tpl, $data, 1);
        $params = [];
        $params['client_id'] = $config['id'];
        $params['access_token'] = $config['key'];
        $params['machine_code'] = $driverInfo['number'];
        $params['origin_id'] = $orderNo;
        $params['id'] = $this->uuid();
        $params['timestamp'] = $time;
        $params['sign'] = $sign;
        $params['content'] = $content;

        $return = \dux\lib\Http::doPost($url, $params);
        if(empty($return)) {
            return $this->error('打印机繁忙！');
        }
        $return = json_decode($return,true);
        if($return['error']) {
            return $this->error($return['error_description']);
        }

        $data = [
            'driver_id' => $config['pos_type'],
            'pos_no' => $driverId,
            'time' => time(),
            'content' => $content
        ];
        target('warehouse/WarehousePosLog')->add($data);

        return $this->success([
            'id' => $return['body']['id'],
        ]);
    }

    private function uuid($prefix = ""){
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }
}