<?php

/**
 * 配送管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;

class MarkiDeliveryAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'WarehouseMarkiDelivery';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '配送管理',
                'description' => '管理商城配送订单',
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
            'status' => 'status',
            'time_type' => 'time_type',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
            'province' => 'B.receive_province',
            'city' => 'B.receive_city',
            'region' => 'B.receive_region',
            'street' => 'B.receive_street',
            'marki_id' => 'A.marki_id',
        ];
    }

    public function _indexWhere($whereMaps) {
        switch ($whereMaps['status']) {
        case 1:
            $whereMaps['receive_status'] = 0;
            break;
        case 2:
            $whereMaps['receive_status'] = 1;
            break;
        }
        unset($whereMaps['status']);

        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time']);
        }

        $field = 'A.create_time';

        if ($whereMaps['time_type'] == 1) {
            $field = 'B.order_create_time';
        }

        if ($startTime) {
            $whereMaps[$field . '[>=]'] = $startTime;
        }
        if ($stopTime) {
            $whereMaps[$field . '[<=]'] = $stopTime;
        }

        unset($whereMaps['time_type']);
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        return $whereMaps;
    }

    public function _indexOrder() {
        return 'delivery_id desc';
    }

    public function _indexAssign() {
        return [
            'markiList' => target('warehouse/WarehouseMarki')->loadList(['A.status' => 1]),
        ];
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

        $list = target('WarehouseMarkiDelivery')->loadList($whereMaps, 0, 'A.create_time asc, B.order_user_id asc, A.marki_id asc, convert(B.receive_street using gbk) ASC');

        $orderIds = array_column($list, 'order_id');

        $orderGoods = target('order/OrderGoods')->where([
            'order_id' => $orderIds,
        ])->select();

        $orderData = [];
        foreach ($list as $key => $value) {
            $orderData[$value['order_id']] = $value;
        }

        foreach ($orderGoods as $value) {
            $orderData[$value['order_id']]['goods_list'][] = $value;
        }

        $data = [];

        foreach ($orderData as $vo) {
            $label = $vo['receive_province'] . '_' . $vo['receive_city'] . '_' . $vo['receive_region'];
            $data[$label][] = $vo;

        }
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $sheet = 0;
        $num = 0;
        foreach ($data as $label => $list) {
            $label = explode('_', $label);
            $region = end($label);
            $num++;
            if ($sheet >= 1) {
                $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, '(' . $num . ')' . $region);
                $spreadsheet->addSheet($myWorkSheet, $sheet);
                $worksheet = $spreadsheet->getSheet($sheet);
            } else {
                $worksheet = $spreadsheet->getSheet(0);
                $worksheet->setTitle('(' . $num . ')' . $region);
            }
            //标题
            $worksheet->setCellValue('A1', '商品配送单')->mergeCells('A1:H1');
            $styleCenter = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
            $worksheet->getStyle('A1')->getFont()->setSize(20);
            $worksheet->getStyle('A1')->applyFromArray($styleCenter);
            $worksheet->getColumnDimension('A')->setWidth(25);
            $worksheet->getColumnDimension('B')->setWidth(25);
            $worksheet->getColumnDimension('C')->setWidth(8);
            $worksheet->getColumnDimension('D')->setWidth(15);
            $worksheet->getColumnDimension('G')->setWidth(15);

            //时间
            if ($pageMaps['start_time'] && $pageMaps['stop_time']) {
                $worksheet->setCellValue('A2', '时间：' . $pageMaps['start_time'] . ' 至 ' . $pageMaps['stop_time'])->mergeCells('A2:G2')->getStyle('A2')->getFont()->setSize(14);
            } else {
                $worksheet->setCellValue('A2', '时间：全部订单')->mergeCells('A2:G2')->getStyle('A2')->getFont()->setSize(14);
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
            $worksheet->setCellValueByColumnAndRow(1, $headRow, '收货信息')->getStyleByColumnAndRow(1, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(2, $headRow, '商品')->getStyleByColumnAndRow(2, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(3, $headRow, '单位')->getStyleByColumnAndRow(3, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(4, $headRow, '规格')->getStyleByColumnAndRow(4, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(5, $headRow, '数量')->getStyleByColumnAndRow(5, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(6, $headRow, '价格')->getStyleByColumnAndRow(6, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(7, $headRow, '配送员')->getStyleByColumnAndRow(7, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(8, $headRow, '仓库签字')->getStyleByColumnAndRow(8, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(9, $headRow, '配送员签字')->getStyleByColumnAndRow(9, $headRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(10, $headRow, '客户签字')->getStyleByColumnAndRow(10, $headRow)->applyFromArray($styleArray);
            $i = $headRow;
            foreach ($list as $vo) {

                foreach ($vo['goods_list'] as $k => $goods) {
                    $i++;
                    if(!$k) {
                        $worksheet->setCellValueByColumnAndRow(1, $i, $vo['receive_name'] . ' ' . $vo['receive_tel'] . "\n" . $vo['receive_region'] . ' ' . $vo['receive_street'] . ' ' . $vo['receive_address'])->getStyleByColumnAndRow(1, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                        $worksheet->setCellValueByColumnAndRow(7, $i, $vo['marki_name'] . "\n" . $vo['receive_tel'])->getStyleByColumnAndRow(7, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                    }else {
                        $worksheet->setCellValueByColumnAndRow(1, $i, '')->getStyleByColumnAndRow(1, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                        $worksheet->setCellValueByColumnAndRow(7, $i, '')->getStyleByColumnAndRow(7, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                    }

                    $options = '';
                    $goods['goods_options'] = unserialize($goods['goods_options']);
                    if ($goods['goods_options']) {
                        foreach ($goods['goods_options'] as $v) {
                            $options .= $v['value'] . ' ';
                        }
                    } else {
                        $options = '无';
                    }
                    $worksheet->setCellValueByColumnAndRow(2, $i, $goods['goods_name'])->getStyleByColumnAndRow(2, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
                    $worksheet->setCellValueByColumnAndRow(3, $i, $goods['goods_unit'])->getStyleByColumnAndRow(3, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(4, $i, $options)->getStyleByColumnAndRow(4, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(5, $i, $goods['goods_qty'])->getStyleByColumnAndRow(5, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(6, $i, $goods['price_total'])->getStyleByColumnAndRow(6, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(8, $i, '')->getStyleByColumnAndRow(8, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(9, $i, '')->getStyleByColumnAndRow(9, $i)->applyFromArray($styleArray);
                    $worksheet->setCellValueByColumnAndRow(10, $i, '')->getStyleByColumnAndRow(10, $i)->applyFromArray($styleArray);

                }
            }
            $footRow = $i + 1;

            $worksheet->setCellValueByColumnAndRow(1, $footRow, '合计')->getStyleByColumnAndRow(1, $footRow)->applyFromArray($styleArray)->getFont()->setBold(true);
            $worksheet->setCellValueByColumnAndRow(2, $footRow, '')->getStyleByColumnAndRow(2, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(3, $footRow, '')->getStyleByColumnAndRow(3, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(4, $footRow, '')->getStyleByColumnAndRow(4, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(5, $footRow, '=SUM(E4:E' . $i . ')')->getStyleByColumnAndRow(5, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(6, $footRow, '=SUM(F4:F' . $i . ')')->getStyleByColumnAndRow(6, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(7, $footRow, '')->getStyleByColumnAndRow(7, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(8, $footRow, '')->getStyleByColumnAndRow(8, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(9, $footRow, '')->getStyleByColumnAndRow(9, $footRow)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(10, $footRow, '')->getStyleByColumnAndRow(10, $footRow)->applyFromArray($styleArray);

            $sheet++;
            
            unset($worksheet);

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="商品配送单-' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

}