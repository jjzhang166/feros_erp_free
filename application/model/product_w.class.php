<?php

namespace model;

/**
 * 仓库管理
 * @author feisanliang
 */
class product_w extends common {

    public $primary_key = 'id';

    /**
     * 入库
     * @param int $w_id 仓库
     * @param int $p_id 产品
     * @param int $number 数量
     */
    public function add($w_id, $p_id, $number,$supplier, $remark = NULL) {
        $data['w_id'] = $w_id;
        $data['p_id'] = $p_id;
        $data['s_id'] = $supplier;
        $data['number'] = $number;
        $data['uid'] = $this->get_uid();
        $data['time'] = time();
        $data['remark'] = $remark;
        return $this->insert($data);
    }

    /**
     * 删除入库记录
     * @param int $id
     */
    public function del($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除入库记录');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除入库记录');
            $this->failure('删除失败');
        }
    }

}
