<?php

namespace app\order\service;
/**
 * 快递鸟查询
 */
class KdniaoService extends \app\base\service\BaseService {

    private $sandbox = false;

    /**
     * 查询快递
     * @param $name
     * @param $label
     * @param $number
     * @return bool
     */
    public function query($name, $label, $number) {
        $config = target('order/OrderConfigWaybill')->getConfig('kdniao');
        if (empty($config)) {
            return $this->error('配置不存在!');
        }
        $id = $config['id'];
        $key = $config['key'];
        $requestData = json_encode([
            'ShipperCode' => $label,
            'LogisticCode' => $number,
        ]);
        $data = [
            'EBusinessID' => $id,
            'RequestType' => 1002,
            'RequestData' => urlencode($requestData),
            'DataType' => 'JSON',
        ];
        $data['DataSign'] = urlencode(base64_encode(md5($requestData . $key)));
        $raw = \dux\lib\Http::doPost('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $data);
        if (empty($raw)) {
            return $this->error('暂无物流信息');
        }

        $json = json_decode($raw, true);
        if (empty($json['Traces'])) {
            return $this->error($json['Reason'] ? $json['Reason'] : '暂无物流信息');
        }

        $list = $json['Traces'];
        $traces = [];
        foreach ($list as $vo) {
            $traces[] = [
                'date' => $vo['AcceptTime'],
                'msg' => $vo['AcceptStation'],
            ];
        }
        $traces = array_reverse($traces);
        return $this->success($traces);
    }

    public function place($express, $userId, $orderNo, $goodsData, $name, $tel, $province, $city, $area, $address, $code, $remark = '', $notice = 1, $transType = 1, $payType = 1) {
        if (!$this->sandbox) {
            $api = 'http://api.kdniao.com/api/EOrderService';
        } else {
            $api = 'http://sandboxapi.kdniao.com:8080/kdniaosandbox/gateway/exterfaceInvoke.json';
        }

        $config = target('order/OrderConfigWaybill')->getConfig('kdniao');
        if (empty($config)) {
            return $this->error('配置不存在!');
        }
        $id = $config['id'];
        $key = $config['key'];

        $configExpress = target('order/OrderConfigExpress')->getWhereInfo([
            'name' => $express,
        ]);
        if (empty($configExpress) || !$configExpress['place_status']) {
            return $this->error('该快递不支持电子面单！');
        }

        $orderConfig = target('order/OrderConfig')->getConfig();

        $requestData = [
            'MemberID' => $userId,
            'CustomerName' => $configExpress['customer_name'],
            'CustomerPwd' => $configExpress['customer_pwd'],
            'SendSite' => $configExpress['send_site'],
            'SendStaff' => $configExpress['send_staff'],
            'MonthCode' => $configExpress['month_code'],
            'WareHouseID' => $configExpress['ware_house_id'],
            'TransType' => $transType,
            'ShipperCode' => $configExpress['label'],
            'ThrOrderCode' => $orderNo,
            'OrderCode' => $orderNo,
            'PayType' => $payType,
            'ExpType' => 1,
            'Receiver' => [
                'Name' => $name,
                'Tel' => $tel,
                'PostCode' => $code,
                'ProvinceName' => $province,
                'CityName' => $city,
                'ExpAreaName' => $area,
                'Address' => $address,
            ],
            'Sender' => [
                'Name' => $orderConfig['contact_name'],
                'Tel' => $orderConfig['contact_tel'],
                'PostCode' => $orderConfig['contact_zip'],
                'ProvinceName' => $orderConfig['contact_province'],
                'CityName' => $orderConfig['contact_city'],
                'ExpAreaName' => $orderConfig['contact_region'],
                'Address' => $orderConfig['contact_address'],
            ],
            'IsNotice' => $notice,
            'Weight' => 1,
            'Quantity' => 1,
            'Remark' => $remark,
            'Commodity' => [
            ],
            'IsReturnPrintTemplate' => 1,
            'IsSendMessage' => 0,
        ];

        $weight = 1;
        foreach ($goodsData as $vo) {
            $requestData['Commodity'][] = [
                'GoodsName' => $vo['name'],
                'GoodsCode' => $vo['code'],
                'Goodsquantity' => $vo['num'],
                'GoodsPrice' => $vo['price'],
                'GoodsWeight' => price_calculate($vo['weight'], '/', 1000),
            ];
            $weight += $vo['weight'];
        }
        $weight = price_calculate($weight, '/', 1000);
        $requestData['Weight'] = $weight ? $weight : 1;

        if ($this->sandbox) {
            $requestData['LogisticCode'] = '1234561';
        }

        dux_log($requestData);

        $requestData = json_encode($requestData);
        $data = [
            'EBusinessID' => $id,
            'RequestType' => 1007,
            'RequestData' => urlencode($requestData),
            'DataType' => 'JSON',
        ];
        dux_log($data);
        $data['DataSign'] = urlencode(base64_encode(md5($requestData . $key)));

        $raw = \dux\lib\Http::doPost($api, $data, 5);
        if (empty($raw)) {
            return $this->error('提交失败，请重试！');
        }
        $json = json_decode($raw, true);
        dux_log($json);
        if (!$json['Success']) {
            return $this->error($json['Reason'] ? $json['Reason'] : '下单失败：' . $json['ResultCode']);
        }

        $data = [
            'place_no' => $json['Order']['OrderCode'],
            'express_no' => $json['Order']['LogisticCode'],
            'express_label' => $json['Order']['ShipperCode'],
            'express_tpl' => $json['PrintTemplate'],
            'express_name' => $express,
            'express_number' => $json['UniquerRequestNumber'],
        ];
        return $this->success($data);
    }

    public function print($data, $printName) {
        $config = target('order/OrderConfigWaybill')->getConfig('kdniao');
        if (empty($config)) {
            return $this->error('配置不存在!');
        }

        $requestData = [];
        foreach ($data as $vo) {
            $requestData[] = [
                'OrderCode' => $vo['place_no'],
                'PortName' => $printName,
            ];
        }

        $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $ip = \dux\lib\Client::getUserIp();
        if($ip == '127.0.0.1') {
            $ip = $this->getIp();
        }
        $dataSign = $this->encrypt($ip.$requestData, $config['key']);
        $html = '<form method="POST" target="_blank" action="http://www.kdniao.com/External/PrintOrder.aspx"><input type="text" name="RequestData" value=\'' . $requestData . '\'"/><input type="text" name="EBusinessID" value="' . $config['id'] . '"/><input type="text" name="DataSign" value="' . $dataSign . '"/><input type="text" name="IsPriview" value="1"/></form>';

        return $this->success([
            'type' => 'form',
            'data' => $html,
        ]);
    }

    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }

    private function isPrivateIp($ip) {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    private function getIp() {
        //获取客户端IP
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if (!$ip || $this->isPrivateIp($ip)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://www.kdniao.com/External/GetIp.aspx');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            $output = explode("\n", $output);
            $output = trim($output[0]);
            return $output;
        } else {
            return $res;
        }
    }
}