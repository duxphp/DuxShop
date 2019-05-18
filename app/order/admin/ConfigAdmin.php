<?php

/**
 * 订单设置
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;


class ConfigAdmin extends \app\system\admin\SystemAdmin {

    /**
     * 模块信息
     */
    protected function _infoModule(){
        return array(
            'info' => array(
                'name' => '订单设置',
                'description' => '配置订单基本功能',
            ),
        );
    }

    public function menu() {
        $menu = [
            [
                'name' => '订单设置',
                'url' => url('index'),
                'cur' => ACTION_NAME == 'index' ? 1 : 0,
            ],
            [
                'name' => '售后信息',
                'url' => url('info'),
                'cur' => ACTION_NAME == 'info' ? 1 : 0,
            ],
        ];
        return $menu;
    }

    /**
     * 订单设置
     */
    public function index() {
        if(!isPost()) {
            $info = target('OrderConfig')->getConfig();
            $waybillList = target('order/OrderConfigWaybill')->typeList();
            $info['pay_type'] =  array_filter(explode(',', $info['pay_type']), function($var) {
                if(!strlen($var)) {
                    return false;
                }
                return true;
            });
            $this->assign('info', $info);
            $this->assign('waybillList', $waybillList);
            $this->assign('hookMenu', $this->menu());
            $this->systemDisplay();
        }else{
            
            $_POST['pay_type'] = $_POST['pay_type'] ? implode(',', $_POST['pay_type']) : '';
            if(target('OrderConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }

    /**
     * 售后信息
     */
    public function info() {
        if(!isPost()) {
            $info = target('OrderConfig')->getConfig();
            $this->assign('info', $info);
            $this->assign('hookMenu', $this->menu());
            $this->systemDisplay();
        }else{
            if(target('OrderConfig')->saveInfo()){
                $this->success('功能配置成功！');
            }else{
                $this->error('功能配置失败');
            }
        }
    }
}