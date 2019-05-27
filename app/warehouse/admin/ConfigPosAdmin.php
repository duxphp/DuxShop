<?php

/**
 * 物流接口
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\warehouse\admin;


class ConfigPosAdmin extends \app\system\admin\SystemAdmin {

    protected $_model = 'WarehouseConfigPos';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return array(
            'info' => array(
                'name' => '打印机设置',
                'description' => '设置小票打印机接口信息',
            ),
        );
    }

    public function index() {
        $this->assign('list', target($this->_model)->typeList());
        $this->systemDisplay();
    }

    public function setting() {
        if (!isPost()) {
            $type = request('get', 'type');
            if (empty($type)) {
                $this->error('参数不能为空！');
            }
            $typeList = target($this->_model)->typeList();
            $typeInfo = $typeList[$type];
            $where = array();
            $where['type'] = $type;
            $info = target($this->_model)->getWhereInfo($where);
            $this->assign('info', $info);
            $this->assign('settingInfo', json_decode($info['setting'], true));
            $this->assign('typeInfo', $typeInfo);
            $this->assign('ruleList', $typeInfo['configRule']);
            $this->assign('type', $type);
            $this->systemDisplay();
        } else {
            $post = request('post');
            $data = array();
            $data['status'] = $post['status'];
            $data['type'] = $post['type'];
            $data['setting'] = $post;
            if ($post['pos_id']) {
                $data['pos_id'] = $post['pos_id'];
                $type = 'edit';
            } else {
                $type = 'add';
            }
            if (target($this->_model)->saveData($type, $data)) {
                //编辑后处理
                $this->success('保存成功！', url('index'));
            } else {
                $this->error(target($this->_model)->getError());
            }
        }
    }

}