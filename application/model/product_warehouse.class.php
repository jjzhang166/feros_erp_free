<?php

/**
 * 仓库
 */

namespace model;

class product_warehouse extends common {

    public $primary_key = 'w_id';

    public function sort_submit() {
        if ($this->t_input->post('sort')) {
            $array = explode('|', $this->t_input->post('sort'));
            $count = count($array);
            foreach ($array as $id) {
                $sql[] = $this->limit(1)->update(array('sort' => $count--), $id, true);
            }
            if (!empty($sql)) {
                $this->exec(implode(";", $sql));
            }
            $this->m_operate->success('修改仓库排序');
            $this->success('排序成功');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('w_id'))
            $this->failure('参数出错');
        $id = $this->t_input->post('w_id');
        $data['w_name'] = $this->t_input->post('w_name');
        $data['w_address'] = $this->t_input->post('w_address');
        $data['w_remark'] = $this->t_input->post('w_remark');
        $data['w_default'] = $this->t_input->post('w_default') ? 1 : 0;
        if (empty($data['w_name']))
            $this->failure('请输入仓库名称');
//        if (!empty($data['w_default']))
//            $this->update(array('w_default' => 0));

        $this->m_product_warehouse_staff->where(array('w_id' => $this->t_input->post('w_id')))->delete();

        foreach ($this->t_input->post('staff') as $value) {
            if (is_numeric($value))
                $da[] = array('uid' => (int) $value, 'w_id' => $id);
        }

        if (!empty($da)) {
            $update2 = $this->m_product_warehouse_staff->insert_batch($da);
        }
        $update = $this->limit(1)->update($data, $id);
        if ($update || !empty($update2)) {
            $this->m_operate->success('修改仓库');
            $this->success('修改成功', $this->url->site('system/warehouse'));
        } else {
            $this->m_operate->failure('修改仓库');
            $this->failure('没有任何修改');
        }
    }

    public function add_submit() {
        $data['w_name'] = $this->t_input->post('w_name');
        $data['w_address'] = $this->t_input->post('w_address');
        $data['w_remark'] = $this->t_input->post('w_remark');
        $data['w_default'] = $this->t_input->post('w_default') ? 1 : 0;
        $staff = $this->t_input->post('staff');
        if (empty($data['w_name']))
            $this->failure('请输入仓库名称');
//        if (!empty($data['w_default']))
//            $this->update(array('w_default' => 0));
        if (($insert_id = $this->insert($data))) {
            foreach ((array) $staff as $value)
                $da[] = array('uid' => (int) $value, 'w_id' => $insert_id);
            if (!empty($da)) {
                $this->m_product_warehouse_staff->insert_batch($da);
            }
            $this->m_operate->success('新增仓库');
            $this->success('新增成功', $this->url->site('system/warehouse'));
        } else {
            $this->m_operate->failure('新增仓库');
            $this->failure('新增失败');
        }
    }

    public function delete_submit($id) {
        $list = $this->where(array('a.w_id' => $id))->warehouse_where()->find();
        if (!empty($list['number']))
            $this->failure('当前仓库有库存,无法删除');
        if ($this->from('product_warehouse')->limit(1)->where(array('w_id' => $id))->delete()) {
            $this->m_product_warehouse_staff->where(array('w_id' => $id))->delete();
            $this->m_operate->success('删除仓库');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除仓库');
            $this->failure('删除失败');
        }
    }

    public function warehouse_where($count = false) {
        $this->order('a.sort desc');

        //$this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.w_name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');

        $this->left_join('product_warehouse_staff as b', 'a.w_id = b.w_id');
        $this->left_join('staff as c', 'b.uid = c.uid');

        $this->left_join('product_inventory as w', 'w.warehouse = a.w_id');

        $this->from('product_warehouse as a')->select('a.*' . ($count ? ',group_concat(DISTINCT c.realname) as realname,group_concat(DISTINCT c.uid) as uid,SUM(DISTINCT w.quantity) as number' : ''));
        $this->group('a.w_id');
        return $this;
    }

}
