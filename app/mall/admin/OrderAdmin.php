<?php

/**
 * 订单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\mall\admin;

class OrderAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MallOrder';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '商城订单',
                'description' => '管理商城商品订单',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ],
        ];
    }

    public function _indexParam() {
        return [
            'name' => 'name',
            'status' => 'status',
            'stockout' => 'stockout',
            'type' => 'type',
            'take' => 'take',
            'time_type' => 'time_type',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
            'province' => 'B.receive_province',
            'city' => 'B.receive_city',
            'region' => 'B.receive_region',
            'street' => 'B.receive_street',
        ];
    }

    public function _indexWhere($whereMaps) {
        switch ($whereMaps['status']) {
            case 1:
                $where = 'B.order_status = 1 AND B.pay_status = 0';
                break;
            case 2:
                $where = 'B.order_status = 1 AND B.parcel_status = 0 AND B.pay_status = 1';
                break;
            case 3:
                $where = 'B.order_status = 1 AND B.delivery_status = 0 AND B.parcel_status = 1';
                break;
            case 4:
                $where = 'B.order_status = 1 AND B.delivery_status = 1 AND B.order_complete_status = 0';
                break;
            case 5:
                $where = 'B.order_status = 1 AND B.order_complete_status = 1';
                break;
            case 6:
                $where = 'B.order_status = 0';
                break;
        }
        if (!empty($where)) {
            $whereMaps['_sql'][] = $where;
        }
        unset($whereMaps['status']);

        $whereMaps['name'] = html_clear($whereMaps['name']);
        if ($whereMaps['name']) {
            switch ($whereMaps['type']) {
                case 1:
                    $whereMaps['_sql'][] = "(C.user_id = '{$whereMaps['name']}' OR C.nickname like '%{$whereMaps['name']}%' OR C.tel= '{$whereMaps['name']}' OR C.email= '{$whereMaps['name']}')";
                    break;
                case 2:
                    $whereMaps['_sql'][] = "(B.receive_name = '{$whereMaps['name']}' OR B.receive_tel = '{$whereMaps['name']}')";
                    break;
                case 3:
                    $whereMaps['_sql'][] = "(B.receive_city like '%{$whereMaps['name']}%' OR B.receive_region like '%{$whereMaps['name']}%' OR B.receive_address like '%{$whereMaps['name']}%')";
                    break;
                case 4:
                    $goodsList = target('order/OrderGoods')->loadHasList([
                        '_sql' => 'A.goods_name like "%' . $whereMaps['name'] . '%"',
                    ]);

                    $orderIds = [];
                    foreach ($goodsList as $vo) {
                        $orderIds[] = $vo['order_id'];
                    }
                    $orderIds = array_unique($orderIds);
                    $orderIds = array_filter($orderIds);
                    if ($orderIds) {
                        $whereMaps['_sql'][] = "B.order_id in(" . implode(',', $orderIds) . ")";
                    } else {
                        $whereMaps['B.order_status'] = 3;
                    }
                    break;
                case 5:
                    $goodsList = target('order/OrderGoods')->loadHasList([
                        'A.goods_no' => $whereMaps['name'],
                    ]);
                    $orderIds = [];
                    foreach ($goodsList as $vo) {
                        $orderIds[] = $vo['order_id'];
                    }
                    $orderIds = array_unique($orderIds);
                    $orderIds = array_filter($orderIds);
                    if ($orderIds) {
                        $whereMaps['_sql'][] = "B.order_id in(" . implode(',', $orderIds) . ")";
                    } else {
                        $whereMaps['B.order_status'] = 3;
                    }
                    break;
                case 6:
                    $goodsList = target('order/OrderDelivery')->loadList([
                        'A.delivery_no' => $whereMaps['name'],
                    ]);
                    $orderIds = [];
                    foreach ($goodsList as $vo) {
                        $orderIds[] = $vo['order_id'];
                    }
                    $orderIds = array_unique($orderIds);
                    $orderIds = array_filter($orderIds);
                    if ($orderIds) {
                        $whereMaps['_sql'][] = "B.order_id in(" . implode(',', $orderIds) . ")";
                    } else {
                        $whereMaps['B.order_status'] = 3;
                    }
                    break;
                default:
                    $whereMaps['B.order_no'] = $whereMaps['name'];
                    break;
            }
        }
        unset($whereMaps['type']);
        unset($whereMaps['name']);

        switch ($whereMaps['take']) {
            case 1:
                $whereMaps['B.take_id'] = 0;
                break;
            case 2:
                $whereMaps['_sql'][] = 'B.take_id <> 0';
                break;
        }
        if (!empty($where)) {
            $whereMaps['_sql'][] = $where;
        }
        unset($whereMaps['take']);

        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time']);
        }

        $field = 'B.order_create_time';

        if ($whereMaps['time_type'] == 1) {
            $field = 'B.pay_time';
        }

        if ($startTime) {
            $whereMaps['_sql'][] = $field . ' >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = $field . ' <= ' . $stopTime;
        }

        unset($whereMaps['time_type']);
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        return $whereMaps;
    }

    public function _indexAssign($pageMaps) {
        $orderNo = $pageMaps['order_no'];
        return [
            'order_no' => $orderNo,
            'markiList' => target('warehouse/WarehouseMarki')->loadList(['A.status' => 1]),
            'hookMenu' => [
                [
                    'name' => '全部',
                    'url' => url('index'),
                    'cur' => !isset($pageMaps['status']),
                ],
                [
                    'name' => '待付款',
                    'url' => url('index', ['status' => 1]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 1,
                ],
                [
                    'name' => '待配货',
                    'url' => url('index', ['status' => 2]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 2,
                ],
                [
                    'name' => '待配送',
                    'url' => url('index', ['status' => 3]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 3,
                ],
                [
                    'name' => '配送中',
                    'url' => url('index', ['status' => 4]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 4,
                ],
                [
                    'name' => '已完成',
                    'url' => url('index', ['status' => 5]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 5,
                ],
                [
                    'name' => '已取消',
                    'url' => url('index', ['status' => 6]),
                    'cur' => isset($pageMaps['status']) && $pageMaps['status'] == 6,
                ],
            ],
        ];
    }

    public function _indexOrder() {
        return 'B.order_id desc';
    }

    public function info() {
        $id = request('get', 'id', 0, 'intval');
        if (empty($id)) {
            $this->error404();
        }
        $info = target('mall/MallOrder')->getWhereInfo([
            'B.order_id' => $id,
        ]);
        if (empty($info)) {
            $this->error404();
        }
        $payData = [];
        if ($info['pay_status']) {
            $payList = target('member/PayConfig')->typeList();
            foreach ($info['pay_data'] as $vo) {
                $payTypeInfo = $payList[$vo['way']];
                $payData[] = array_merge(target('statis/StatisFinancialLog')->getInfo($vo['id']), ['pay_type' => $payTypeInfo['name']]);
            }
        }

        $deliveryList = target('order/OrderDelivery')->loadList([
            'A.order_id' => $info['order_id'],
        ]);


        $markiDeliveryList = target('warehouse/WarehouseMarkiDelivery')->loadList([
            'A.order_id' => $info['order_id'],
        ]);

        $logList = target('order/OrderLog')->loadList([
            'order_id' => $info['order_id'],
        ]);

        $orderGoods = target('order/OrderGoods')->loadList([
            'order_id' => $info['order_id'],
        ]);

        $remarkList = target('order/OrderRemark')->loadList([
            'order_id' => $info['order_id'],
        ]);

        $status = target('order/Order', 'service')->getManageStatus($info);
        $takeInfo = target('order/OrderTake')->getInfo($info['take_id']);

        $groupLog = [];
        $parcelInfo = target('order/OrderParcel')->getWhereInfo([
            'A.order_id' => $id,
        ]);

        $expressList = target('order/OrderConfigExpress')->loadList([], 0, 'express_id asc');

        $markiList = target('warehouse/WarehouseMarki')->loadList([], 0, 'marki_id asc');

        $this->assign('info', $info);
        $this->assign('payData', $payData);
        $this->assign('status', $status);
        $this->assign('deliveryList', $deliveryList);
        $this->assign('markiDeliveryList', $markiDeliveryList);
        $this->assign('orderGoods', $orderGoods);
        $this->assign('takeInfo', $takeInfo);
        $this->assign('groupLog', $groupLog);
        $this->assign('parcelInfo', $parcelInfo);
        $this->assign('logList', $logList);
        $this->assign('remarkList', $remarkList);
        $this->assign('expressList', $expressList);
        $this->assign('markiList', $markiList);
        $this->systemDisplay();
    }

    public function batch() {
        $status = request('post', 'action');
        $ids = request('post', 'ids');
        $markiId = request('post', 'marki_id');
        if (!$status || empty($ids)) {
            $this->error('请选择操作记录!');
        }

        $orderCount = count(explode(',', $ids));

        $where = [
            '_sql' => 'A.order_id in (' . $ids . ')',
            'A.order_status' => 1,
        ];

        switch ($status) {
        case 1:
            $where['OR'] = [
                'AND #one' => [
                    'A.pay_type' => 1,
                    'A.pay_status' => 1
                ],
                'AND #one' => [
                    'A.pay_type' => 0
                ],
            ];
            $where['A.parcel_status'] = 0;
            break;
        case 2:
            $where['A.parcel_status'] = 1;
            $where['A.delivery_status'] = 0;
            if(empty($markiId)) {
                $this->error('请选择配送员!');
            }
            break;
        case 3:
            $where['A.delivery_status'] = 1;
            $where['A.order_complete_status'] = 0;
            break;
        default:
            $this->error('请选择操作!');

        }

        $list = target('order/Order')->loadList($where);
        $successCount = count($list);

        if (empty($list)) {
            $this->error('没有符合条件的订单处理!');
        }

        $model = target($this->_model);
        $model->beginTransaction();
        foreach ($list as $vo) {
            if ($status == 1) {
                if (!target('order/Order', 'service')->parcelOrder($vo['order_id'], '')) {
                    $model->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
                if (!target('order/Order', 'service')->addLog($vo['order_id'], '订单开始配货', '', USER_ID)) {
                    $model->rollBack();
                    $this->error('订单日志记录失败!');
                }
            }
            if ($status == 2) {
                if (!target('order/Order', 'service')->deliveryOrder($vo['order_id'], '', 2, [
                    'marki_id' => $markiId 
                ])) {
                    $model->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
                if (!target('order/Order', 'service')->addLog($vo['order_id'], '订单发货操作', '', USER_ID)) {
                    $model->rollBack();
                    $this->error('订单日志记录失败!');
                }
            }
            if ($status == 3) {
                if (!target('order/Order', 'service')->confirmOrder($vo['order_id'])) {
                    $model->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
                if (!target('order/Order', 'service')->addLog($vo['order_id'], '人工确认完成订单', '', USER_ID)) {
                    $model->rollBack();
                    $this->error('订单日志记录失败!');
                }
            }
        }

        $model->commit();
        $this->success('总提交' . $orderCount . '个订单，已处理成功' . $successCount . '个订单');
    }

    public function export() {
        $params = $this->_indexParam();
        $pageParams = request();
        $whereMaps = [];
        $pageMaps = [];
        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $value = urldecode($pageParams[$key]);
                $value = \dux\lib\Str::htmlClear($value);
                if ($value === '') {
                    continue;
                }
                $pageMaps[$key] = $value;
                if ($key == 'keyword') {
                    $vals = explode(',', $val);
                    $sql = [];
                    foreach ($vals as $k) {
                        $sql[] = "({$k} like '%{$value}%')";
                    }
                    if (empty($sql)) {
                        continue;
                    }
                    $whereMaps['_sql'][] = '(' . implode(' OR ', $sql) . ')';
                } else {
                    $whereMaps[$val] = $value;
                }
            }
        }

        $whereMaps = $this->_indexWhere($whereMaps);
        $data = target($this->_model)->loadList($whereMaps, 0, 'order_create_time asc');
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getSheet(0);
        //标题
        $worksheet->setCellValue('A1', '订单销售统计')->mergeCells('A1:I1');
        $styleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $worksheet->getStyle('A1')->getFont()->setSize(20);
        $worksheet->getStyle('A1')->applyFromArray($styleCenter);
        $worksheet->getColumnDimension('A')->setWidth(15);
        $worksheet->getColumnDimension('B')->setWidth(20);
        $worksheet->getColumnDimension('D')->setWidth(15);
        $worksheet->getColumnDimension('I')->setWidth(15);
        $worksheet->getColumnDimension('J')->setWidth(15);

        //时间
        if ($pageMaps['start_time'] && $pageMaps['stop_time']) {
            $worksheet->setCellValue('A2', '时间：' . $pageMaps['start_time'] . ' 至 ' . $pageMaps['stop_time'])->mergeCells('A2:I2')->getStyle('A2')->getFont()->setSize(14);
        } else {
            $worksheet->setCellValue('A2', '时间：全部订单')->mergeCells('A2:I2')->getStyle('A2')->getFont()->setSize(14);
        }

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'size' => 12,
            ],
        ];

        $headRow = 3;
        $worksheet->setCellValueByColumnAndRow(1, $headRow, '单号')->getStyleByColumnAndRow(1, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(2, $headRow, '订单')->getStyleByColumnAndRow(2, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(3, $headRow, '商品数量')->getStyleByColumnAndRow(3, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(4, $headRow, '买家')->getStyleByColumnAndRow(4, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(5, $headRow, '金额')->getStyleByColumnAndRow(5, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(6, $headRow, '运费')->getStyleByColumnAndRow(6, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(7, $headRow, '总额')->getStyleByColumnAndRow(7, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(8, $headRow, '状态')->getStyleByColumnAndRow(8, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(9, $headRow, '下单时间')->getStyleByColumnAndRow(9, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(10, $headRow, '完成时间')->getStyleByColumnAndRow(10, $headRow)->applyFromArray($styleArray);

        $i = $headRow;
        foreach ($data as $vo) {
            $i++;
            $worksheet->setCellValueByColumnAndRow(1, $i, $vo['order_no'].' ')->getStyleByColumnAndRow(1, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(2, $i, $vo['order_title'])->getStyleByColumnAndRow(2, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(3, $i, $vo['order_num'])->getStyleByColumnAndRow(3, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(4, $i, $vo['receive_store'] . "\n" . $vo['receive_name'] . ' ' . $vo['receive_tel'] . "\n" . $vo['receive_region'] . ' ' . $vo['receive_street'] . ' ' . $vo['receive_address'])->getStyleByColumnAndRow(4, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(5, $i, $vo['pay_price'])->getStyleByColumnAndRow(5, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(6, $i, $vo['delivery_price'])->getStyleByColumnAndRow(6, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(7, $i, price_calculate($vo['pay_price'], '+', $vo['delivery_price']))->getStyleByColumnAndRow(7, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(8, $i, $vo['status_data']['name'])->getStyleByColumnAndRow(8, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(9, $i, $vo['order_create_time'] ? date("Y-m-d \n H:i:s", $vo['order_create_time']) : $vo['order_create_time'])->getStyleByColumnAndRow(9, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(10, $i, $vo['order_complete_time'] ? date("Y-m-d \n H:i:s", $vo['order_complete_time']) : '未完成')->getStyleByColumnAndRow(10, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

        }
        $footRow = $i + 1;

        $worksheet->setCellValueByColumnAndRow(1, $footRow, '合计')->getStyleByColumnAndRow(1, $footRow)->applyFromArray($styleArray)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow(2, $footRow, '')->getStyleByColumnAndRow(2, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(3, $footRow, '')->getStyleByColumnAndRow(3, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(4, $footRow, '')->getStyleByColumnAndRow(4, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(5, $footRow, '=SUM(E4:E' . $i . ')')->getStyleByColumnAndRow(5, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(6, $footRow, '=SUM(F4:F' . $i . ')')->getStyleByColumnAndRow(6, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(7, $footRow, '=SUM(G4:G' . $i . ')')->getStyleByColumnAndRow(7, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(8, $footRow, '')->getStyleByColumnAndRow(8, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(9, $footRow, '')->getStyleByColumnAndRow(9, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(10, $footRow, '')->getStyleByColumnAndRow(10, $footRow)->applyFromArray($styleArray);

        unset($worksheet);


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="订单销售统计-' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

}