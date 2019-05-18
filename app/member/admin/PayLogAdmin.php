<?php

/**
 * 支付记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'statis/StatisFinancialLog';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '交易记录',
                'description' => '资金收入支出交易记录',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'name' => 'name',
            'type' => 'A.type',
            'species' => 'species',
            'log_no' => 'A.log_no',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time',
            'user_id' => 'B.user_id',
        ];
    }

    public function _indexOrder() {
        return 'log_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['name']) {
            $whereMaps['OR'] = [
                'B.user_id[~]' => $whereMaps['name'],
                'B.nickname[~]' => $whereMaps['name'],
                'B.tel[~]' => $whereMaps['name'],
                'B.email[~]' => $whereMaps['name'],
            ];
        }
        unset($whereMaps['name']);

        if($whereMaps['A.type'] > 1) {
            unset($whereMaps['A.type']);
        }
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'A.time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.time <= ' . $stopTime;
        }
        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);

        $typeList = target('statis/StatisFinancial')->typeList('member');
        $species = [];
        foreach ($typeList as $key => $value) {
            $species[] = $value['key'];
        }
        $species = array_unique($species);
        $whereMaps['A.has_species'] = $species;

        $species = explode('|', $whereMaps['species']);
        if ($species[0]) {
            $whereMaps['has_species'] = $species[0];
        }

        if ($species[1]) {
            $whereMaps['sub_species'] = $species[1];
        }

        unset($whereMaps['species']);
        return $whereMaps;
    }

    public function _indexAssign() {
        $typeList = target('statis/StatisFinancial')->typeList('member');
        return [
            'typeList' => $typeList,
        ];
    }

    public function info() {
        $id = request('', 'id');
        if(empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target($this->_model)->getInfo($id);
        if(empty($info)) {
            $this->error('暂无该记录!');
        }
        $html = \dux\Dux::view()->fetch('app/member/view/admin/paylog/info', [
            'info' => $info
        ]);
        $this->success($html);
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

        $data = target($this->_model)->loadList($whereMaps, 0, 'A.log_id desc');


        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $worksheet = $spreadsheet->getSheet(0);
        //标题
        $worksheet->setCellValue('A1', '用户流水记录')->mergeCells('A1:I1');
        $styleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $worksheet->getStyle('A1')->getFont()->setSize(20);
        $worksheet->getStyle('A1')->applyFromArray($styleCenter);
        $worksheet->getColumnDimension('A')->setWidth(19);
        $worksheet->getColumnDimension('B')->setWidth(10);
        $worksheet->getColumnDimension('C')->setWidth(10);
        $worksheet->getColumnDimension('D')->setWidth(10);
        $worksheet->getColumnDimension('E')->setWidth(19);
        $worksheet->getColumnDimension('F')->setWidth(25);
        $worksheet->getColumnDimension('G')->setWidth(20);

        //时间
        if ($pageMaps['start_time'] && $pageMaps['stop_time']) {
            $worksheet->setCellValue('A2', '时间：' . $pageMaps['start_time'] . ' 至 ' . $pageMaps['stop_time'])->mergeCells('A2:I2')->getStyle('A2')->getFont()->setSize(14);
        } else {
            $worksheet->setCellValue('A2', '时间：全部时间')->mergeCells('A2:I2')->getStyle('A2')->getFont()->setSize(14);
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
        $worksheet->setCellValueByColumnAndRow(1, $headRow, '流水号')->getStyleByColumnAndRow(1, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(2, $headRow, '入账')->getStyleByColumnAndRow(2, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(3, $headRow, '出账')->getStyleByColumnAndRow(3, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(4, $headRow, '支付方式')->getStyleByColumnAndRow(4, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(5, $headRow, '支付号')->getStyleByColumnAndRow(5, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(6, $headRow, '交易信息')->getStyleByColumnAndRow(6, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(7, $headRow, '交易时间')->getStyleByColumnAndRow(7, $headRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(8, $headRow, '用户')->getStyleByColumnAndRow(8, $headRow)->applyFromArray($styleArray);

        $i = $headRow;
        foreach ($data as $vo) {
            $i++;
            $worksheet->setCellValueByColumnAndRow(1, $i, ' ' . $vo['log_no'])->getStyleByColumnAndRow(1, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(2, $i, $vo['type'] ? $vo['money'] : 0)->getStyleByColumnAndRow(2, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(3, $i, $vo['type'] ? 0 : $vo['money'])->getStyleByColumnAndRow(3, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(4, $i, $vo['pay_name'])->getStyleByColumnAndRow(4, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(5, $i, ' ' . $vo['pay_no'])->getStyleByColumnAndRow(5, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(6, $i, $vo['title'] . "\n" . $vo['remark'])->getStyleByColumnAndRow(6, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);
            $worksheet->setCellValueByColumnAndRow(7, $i, date('Y-m-d H:i:s', $vo['time']))->getStyleByColumnAndRow(7, $i)->applyFromArray($styleArray);
            $worksheet->setCellValueByColumnAndRow(8, $i, $vo['user_nickname'])->getStyleByColumnAndRow(8, $i)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

        }

        $footRow = $i + 1;

        $worksheet->setCellValueByColumnAndRow(1, $footRow, '合计')->getStyleByColumnAndRow(1, $footRow)->applyFromArray($styleArray)->getFont()->setBold(true);
        $worksheet->setCellValueByColumnAndRow(2, $footRow, '=SUM(B4:B' . $i . ')')->getStyleByColumnAndRow(2, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(3, $footRow, '=SUM(C4:C' . $i . ')')->getStyleByColumnAndRow(3, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(4, $footRow, '')->getStyleByColumnAndRow(4, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(5, $footRow, '')->getStyleByColumnAndRow(5, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(6, $footRow, '')->getStyleByColumnAndRow(6, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(7, $footRow, '')->getStyleByColumnAndRow(7, $footRow)->applyFromArray($styleArray);
        $worksheet->setCellValueByColumnAndRow(8, $footRow, '')->getStyleByColumnAndRow(8, $footRow)->applyFromArray($styleArray);


        unset($worksheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="用户流水统计-' . date('YmdHis') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;

    }

}