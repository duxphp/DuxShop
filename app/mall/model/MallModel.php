<?php

/**
 * 商品管理
 */

namespace app\mall\model;

use app\system\model\SystemModel;

class MallModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'mall_id',
        'validate' => [
            'class_id' => [
                'empty' => ['', '请选择分类!', 'must', 'all'],
            ],
        ],
        'format' => [
            'content' => [
                'function' => ['html_in', 'all', 0],
            ],
            'create_time' => [
                'function' => ['time', 'add'],
            ],
        ],
    ];

    protected function base($where) {
        return $this->table('mall(A)')
            ->join('mall_class(B)', ['B.class_id', 'A.class_id'])
            ->field(['A.*', 'B.name(class_name)'])
            ->where((array) $where);
    }

    public function loadList($where = [], $limit = 0, $order = 'A.sort desc, A.mall_id desc') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        if (empty($list)) {
            return [];
        }
        return $list;
    }

    public function countList($where = []) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where, $lock = false) {
        $info = $this->base($where)->lock($lock)->find();
        if ($info) {
            $info['params'] = unserialize($info['params']);
            $info['images'] = unserialize($info['images']);
            $info['spec_data'] = unserialize($info['spec_data']);
        }
        return $info;
    }

    public function getInfo($id, $lock = false) {
        $where = [];
        $where['A.mall_id'] = $id;
        return $this->getWhereInfo($where, $lock);
    }

    public function saveData($type = 'add', $data = []) {
        $this->beginTransaction();
        $data = empty($data) ? $_POST : $data;
        if ($data['content'] && empty($data['description'])) {
            $data['description'] = \dux\lib\Str::strMake($data['content'], 240);
        }
        if (empty($data['images'])) {
            $this->rollBack();
            $this->error = '请至少上传一张图片!';
            return false;
        }

        $data['type'] = $data['type'] ? $data['type'] : 'mall';

        $data['keyword'] = trim($data['keyword']);
        $data['keyword'] = \dux\lib\Str::htmlClear($data['keyword']);
        $data['keyword'] = preg_replace("/\s(?=\s)/", ',', $data['keyword']);
        $data['keyword'] = str_replace('，', ',', $data['keyword']);
        $keyword = explode(',', $data['keyword']);
        $tagsId = [];
        if (!empty($keyword)) {
            foreach ($keyword as $vo) {
                $vo = trim($vo);
                if (empty($vo)) {
                    continue;
                }
                $tagInfo = target('site/SiteTags')->getWhereInfo(['name' => $vo, 'app' => $data['app']]);
                if ($tagInfo) {
                    if (!target('site/SiteTags')->where(['tag_id' => $tagInfo['tag_id']])->setInc('quote', 1)) {
                        return false;
                    }
                    $tagId = $tagInfo['tag_id'];
                } else {
                    $tagId = target('site/SiteTags')->add(['name' => $vo, 'app' => $data['app']]);
                    if (!$tagId) {
                        return false;
                    }
                }
                $tagsId[] = $tagId;
            }
        }
        $data['tags_id'] = implode(',', $tagsId);

        if (empty($data['content'])) {
            $this->rollBack();
            $this->error = '请填写商品详情!';
            return false;
        }
        $images = [];
        foreach ($data['images']['url'] as $key => $vo) {
            $images[] = [
                'url' => $vo,
                'title' => $data['images']['title'][$key],
            ];
        }
        $data['images'] = serialize($images);
        //处理缩略图
        $data['image'] = target('site/Tools', 'service')->coverImage($images[0]['url']);

        //处理规格数据
        $specData = '';
        if (isset($data['data']['spec'])) {
            $goods_spec_array = [];
            foreach ($data['data']['spec'] as $key => $val) {
                foreach ($val as $v) {
                    $tempSpec = json_decode($v, true);
                    if (!isset($goods_spec_array[$tempSpec['id']])) {
                        $goods_spec_array[$tempSpec['id']] = ['id' => $tempSpec['id'], 'name' => $tempSpec['name'], 'value' => []];
                    }
                    $goods_spec_array[$tempSpec['id']]['value'][] = $tempSpec['value'];
                }
            }
            foreach ($goods_spec_array as $key => $val) {
                $val['value'] = array_unique($val['value']);
                $goods_spec_array[$key]['value'] = join(',', $val['value']);
            }
            $specData = serialize($goods_spec_array);
        }
        $data['spec_data'] = $specData;

        //处理自定义属性
        $params = [];
        if (!empty($data['params']['key'])) {
            foreach ($data['params']['key'] as $key => $val) {
                $params[] = [
                    'key' => $val,
                    'value' => $data['params']['value'][$key],
                ];
            }
        }

        $data['goods_no'] = $data['data']['goods_no'][0];
        $data['barcode'] = $data['data']['barcode'][0];
        $data['sell_price'] = $data['data']['sell_price'][0];
        $data['market_price'] = $data['data']['market_price'][0];
        $data['cost_price'] = $data['data']['cost_price'][0];
        $data['store'] = 0;
        $data['weight'] = $data['data']['weight'][0];
        $data['give_point'] = $data['data']['give_point'][0];
        $data['update_time'] = time();
        $data['params'] = serialize($params);
        $data['brand_id'] = intval($data['brand_id']);
        $data['attr_rec'] = intval($data['attr_rec']);
        $data['attr_new'] = intval($data['attr_new']);
        $data['attr_hot'] = intval($data['attr_hot']);
        $data['attr_free'] = intval($data['attr_free']);
        $data['gift_status'] = intval($data['gift_status']);
        $data['service_status'] = intval($data['service_status']);
        $data['invoice_status'] = intval($data['invoice_status']);
        $data['discount_status'] = intval($data['discount_status']);

        $specData = $data['data'];
        $proData = [];
        $store = 0;
        foreach ($specData['goods_no'] as $key => $vo) {
            $proData[$key] = [
                'products_id' => $specData['id'][$key],
                'products_no' => $vo,
                'barcode' => $specData['barcode'][$key],
                'sell_price' => $specData['sell_price'][$key],
                'market_price' => $specData['market_price'][$key],
                'cost_price' => $specData['cost_price'][$key],
                'min_num' => $specData['min_num'][$key],
                'store' => $specData['store'][$key],
                'weight' => $specData['weight'][$key],
                'give_point' => $specData['give_point'][$key],
                'spec_data' => $this->mergerSpec($specData['spec'][$key]),
            ];
            if (empty($vo)) {
                $this->rollBack();
                $this->error = '商品货号未填写!';
                return false;
            }
            $store += intval($specData['store'][$key]);
        }
        $data['store'] = $store;
        $mallId = $data['mall_id'];

        if ($type == 'add') {
            $mallId = parent::saveData('add', $data);
            if (!$mallId) {
                $this->rollBack();
                $this->error = $this->getError();

                return false;
            }
        }
        if ($type == 'edit') {
            $status = parent::saveData('edit', $data);
            if (!$status) {
                $this->rollBack();
                $this->error = $this->getError();
                return false;
            }
        }
        //处理货品
        $proIds = [];
        foreach ($proData as $vo) {
            $vo['mall_id'] = $mallId;
            if ($vo['products_id']) {
                $status = target('mall/MallProducts')->edit($vo);
                $proIds[] = $vo['products_id'];
            } else {
                $status = target('mall/MallProducts')->add($vo);
                $proIds[] = $status;
            }
            if (!$status) {
                $this->error = target('mall/MallProducts')->getError();
                return false;
            }
        }
        $status = target('mall/MallProducts')->where([
            'products_id[!]' => $proIds,
            'mall_id' => $mallId,
        ])->delete();

        if (!$status) {
            $this->error = target('mall/MallProducts')->getError();
            return false;
        }

        $this->commit();
        return $mallId;
    }

    protected function mergerSpec($data) {
        if ($data) {
            $data = str_replace("'", '"', $data);
            return serialize(json_decode('[' . implode(',', $data) . ']', true));
        } else {
            return '';
        }
    }

    public function delData($id) {
        $this->beginTransaction();
        $where = [];
        $where['mall_id'] = $id;
        if (!parent::delData($id)) {
            return false;
        }
        if (!target('mall/MallProducts')->where(['mall_id' => $id])->delete()) {
            return false;
        }
        $this->commit();
        return true;
    }


    public function hasCoupon($coupon, $order) {
        if ($coupon['type'] <> 'mall') {
            return false;
        }
        if (!$coupon['has_id']) {
            return false;
        }
        $hasIds = explode(',', $coupon['has_id']);
        $ids = [];
        foreach ($order['items'] as $v) {
            if (!in_array($v['app_id'], $hasIds)) {
                continue;
            }
            if (bccomp($coupon['meet_money'], $v['total'], 2) !== 1) {
                $ids[] = $v['id'];
                break;
            }
        }
        return $ids ? $ids : false;
    }


}