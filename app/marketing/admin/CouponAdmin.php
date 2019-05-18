<?php

/**
 * 优惠券管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\marketing\admin;

class CouponAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'MarketingCoupon';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '优惠券管理',
                'description' => '管理订单优惠券',
            ],
            'fun' => [
                'index' => true,
                'add' => true,
                'edit' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'name',
            'status' => 'status',
        ];
    }

    public function _indexWhere($whereMaps) {
        switch ($whereMaps['status']) {
            case 1:
                $whereMaps['status'] = 0;
                break;
            case 2:
                $whereMaps['status'] = 1;
                break;
            default:
                unset($whereMaps['status']);
        }
        return $whereMaps;
    }


    public function _indexAssign($pageMaps) {
        return [
            'typeList' => target('marketing/MarketingCoupon')->typeList(true),
            'currencyList' => target('member/MemberCurrency')->typeList(),
            'roleList' => target('member/MemberRole')->loadList(),
            'gradeList' => target('member/MemberGrade')->loadList(),
        ];
    }

    public function _addAssign() {
        $typeList = target('marketing/MarketingCoupon')->typeList(true);
        $typeCur = current($typeList);

        return [
            'info' => [
                'type' => $typeCur['key']
            ],
            'classList' => target('marketing/MarketingCouponClass')->loadList(),
            'goodsClass' => target('mall/MallClass')->loadTreeList(),
            'mallList' => target('mall/Mall')->loadList(),
            'typeList' => $typeList,
            'currencyList' => target('member/MemberCurrency')->typeList()
        ];
    }

    public function edit() {
        $id = request('', 'id', 0, 'intval');
        if (empty($id)) {
            $this->error('参数不正确!');
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('优惠券不存在！');
        }
        if (!isPost()) {
            $this->assign([
                'info' => $info,
                'classList' => target('marketing/MarketingCouponClass')->loadList(),
                'goodsClass' => target('mall/MallClass')->loadTreeList(),
                'mallList' => target('mall/Mall')->loadList(),
                'typeList' => target('marketing/MarketingCoupon')->typeList(true),
                'currencyList' => target('member/MemberCurrency')->typeList()
            ]);

            $proList = [];
            if ($info['has_id'] && $info['typeInfo']['type'] == 1) {
                $proList = target('mall/Mall')->loadList([
                    'mall_id' => explode(',', $info['has_id'])
                ]);
            }

            $proClass = [];
            if ($info['has_id'] && $info['typeInfo']['type'] == 2) {
                $proClass = target('mall/MallClass')->getInfo($info['has_id']);
            }

            $this->assign('proClass', $proClass);
            $this->assign('proList', $proList);
            $this->systemDisplay();
        } else {
            $data = [];
            $data['coupon_id'] = $id;
            $endTime = request('post', 'end_time', '', 'strtotime');
            $stock = request('post', 'stock', '', 'intval');
            $stockType = request('post', 'stock_type', 0, 'intval');
            $receiveType = request('post', 'receive_type', 0, 'intval');
            if ($info['start_time'] >= $endTime) {
                $this->error('结束时间不能大于发放时间！');
            }
            if (!empty($endTime)) {
                $data['end_time'] = $endTime;
                $data['stock'] = $stock;
            }

            $data['stock_type'] = $stockType;
            $data['receive_type'] = $receiveType;
            $status = target($this->_model)->edit($data);
            if (!$status) {
                $this->error(target($this->_model)->getError());
            }
            $this->success('编辑成功！');
        }
    }

    public function _editTpl() {
        return 'edit';
    }

    public function _editAssign($info) {

    }

    public function _indexMarketing() {
        return 'A.coupon_id desc';
    }

    public function del() {
        $id = request('', 'id', 0, 'intval');
        if (empty($id)) {
            $this->error('参数传递错误！');
        }
        $status = target($this->_model)->edit([
            'coupon_id' => $id,
            'del_status' => 1
        ]);
        if ($status) {
            $this->success('删除成功！');
        } else {

            $this->error('删除失败！');
        }
    }

    public function _addBefore() {
        $_POST['platform'] = 1;
    }

    public function status() {
        $id = request('get', 'id', 0, 'intval');
        $status = request('get', 'status', 0, 'intval');
        if (empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target($this->_model)->getInfo($id);
        $data = [];
        $data['coupon_id'] = $info['coupon_id'];

        if ($status == 1) {
            $data['status'] = 1;
            $msg = '优惠券上架成功！';
        } else {
            $data['status'] = 0;
            $msg = '优惠券下架成功！';
        }
        $status = target($this->_model)->edit($data);
        if (!$status) {
            $this->error(target($this->_model)->getError());
        }
        $this->success($msg);
    }

    public function send() {
        $type = request('', 'type', 0, 'intval');
        $num = request('', 'num', 1, 'intval');
        $couponId = request('', 'coupon_id', 0, 'intval');
        $roleId = $_POST['role_id'];
        $gradeId = $_POST['grade_id'];
        $userIds = $_POST['user_id'];

        if(empty($couponId)) {
            $this->error('请选择优惠券！');
        }

        switch ($type) {
            case 1:
                if (empty($roleId)) {
                    $this->error('请选择角色');
                }
                $roleId = array_filter($roleId);
                $userList = target('member/MemberUser')->where(['role_id' => $roleId])->field(['user_id'])->select();
                $userIds = array_column($userList, 'user_id');
                break;
            case 2:
                if (empty($gradeId)) {
                    $this->error('请选择等级');
                }
                $gradeId = array_filter($gradeId);
                $userList = target('member/MemberUser')->where(['grade_id' => $gradeId])->field(['user_id'])->select();
                $userIds = array_column($userList, 'user_id');
                break;
            case 3:
                if (empty($userIds)) {
                    $this->error('请选择用户');
                }
                $userIds = array_filter($userIds);
                break;
            case 0:
            default:
                $userList = target('member/MemberUser')->field(['user_id'])->select();
                $userIds = array_column($userList, 'user_id');
                break;
        }
        if(empty($userIds)) {
            $this->error('暂无用户可发放！');
        }
        target('marketing/MarketingCoupon')->beginTransaction();
        if(!target('marketing/MarketingCoupon')->giveCoupon($userIds, $couponId, $num)) {
            $this->error(target('marketing/MarketingCoupon')->getError());
        }
        target('marketing/MarketingCoupon')->commit();
        $this->success('优惠券发放成功！');
    }


}