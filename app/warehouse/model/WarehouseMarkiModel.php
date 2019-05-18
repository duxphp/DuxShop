<?php

/**
 * 配送员
 */
namespace app\warehouse\model;

use app\system\model\SystemModel;

class WarehouseMarkiModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'marki_id',
    ];

    protected function base($where) {
        return $this->table('warehouse_marki(A)')
            ->join('member_user(B)', ['B.user_id', 'A.user_id'], '>')
            ->field(['A.*',
                'B.nickname(user_nickname)', 'B.tel(user_tel)', 'B.email(user_email)', 'B.avatar(user_avatar)',
            ])
            ->where((array)$where);
    }

    public function loadList($where = [], $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['user_avatar'] = target('member/MemberUser')->getAvatar($vo['user_avatar']);
        }
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['user_avatar'] = target('member/MemberUser')->getAvatar($info['user_avatar']);
        }
        return $info;
    }

    public function getInfo($id) {
        return $this->getWhereInfo([
            'A.marki_id' => $id
        ]);
    }

}