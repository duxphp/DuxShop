<?php

/**
 * 账户提现
 */

namespace app\member\middle;


class FinanceMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'statis/StatisFinancialLog';

    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        if ($type == 1) {
            $where['A.type'] = 1;
        }
        if ($type == 2) {
            $where['A.type'] = 0;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        
        $typeList = target('statis/StatisFinancial')->typeList('member');

        $species = [];
        foreach ($typeList as $key => $value) {
            $species[] = $value['key'];
        }
        $species = array_unique($species);
        $where['A.has_species'] = $species;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'log_id desc');

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function info() {
        $no = intval($this->params['no']);
        $userId = intval($this->params['user_id']);
        $info = target($this->_model)->getWhereInfo([
            'A.user_id' => $userId,
            'A.log_no' => $no,
        ]);
        if (empty($info)) {
            return $this->stop('该记录不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }



}