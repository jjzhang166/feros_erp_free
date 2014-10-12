<?php

/**
 * 产品单位
 */

namespace model;

class product_unit extends common {

    public $primary_key = 'u_id';

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
            $this->m_operate->success('修改账务分类排序');
            $this->success('排序成功');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('u_id'))
            $this->failure('参数出错');
        $data['u_name'] = $this->t_input->post('u_name');
        if (empty($data['u_name']))
            $this->failure('请输入单位');
        if ($this->limit(1)->update($data, $this->t_input->post('u_id'))) {
            $this->m_operate->success('修改产品单位');
            $this->success('修改成功', $this->url->site('system/unit'));
        } else {
            $this->m_operate->failure('修改产品单位');
            $this->failure('没有任何修改');
        }
    }

    public function add_submit() {
        $u_name = $this->t_input->post('u_name');
        if (empty($u_name))
            $this->failure('请输入单位');
        foreach (explode("\n", $u_name) as $value) {
            $value = trim($value);
            if (!empty($value)) {
                $data[]['u_name'] = $value;
            }
            //   $sql[] = 
        }
        if (!empty($data)) {
            $this->insert_batch($data);
        }
        $this->success('新增成功', $this->url->site('system/unit'));
    }

    public function delete_submit($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除产品单位');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除产品单位');
            $this->failure('删除失败');
        }
    }

}
