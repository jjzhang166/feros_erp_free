<?php

namespace model;

/**
 * 员工部门
 * @author feisanliang
 */
class staff_category extends common {

    public $primary_key = 'id';
    private $formats, $category;

    public function add_submit() {
        $data['name'] = $this->t_input->post('name');
        $data['classid'] = (int) $this->t_input->post('classid');
        if (empty($data['name']))
            $this->failure('请输入部门名称');
        if ($this->insert($data)) {
            $this->m_operate->success('新增员工部门');
            $this->success('新增成功', $this->url->site('system/department'));
        } else {
            $this->m_operate->failure('新增员工部门');
            $this->failure('新增失败');
        }
    }

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

    public function delete_submit($id) {
        if ($this->m_staff->where(array('c_id' => $id))->find()) {
            $this->m_operate->failure('删除员工部门');
            $this->failure('请先转移部门下员工后再删除');
        } elseif ($this->where(array('classid' => $id))->find()) {
            $this->m_operate->failure('删除员工部门');
            $this->failure('请先删除子部门');
        } elseif ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除员工部门');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除员工部门');
            $this->failure('删除失败');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['name'] = $this->t_input->post('name');
        $data['classid'] = (int) $this->t_input->post('classid');
        if (empty($data['name']))
            $this->failure('请输入部门名称');
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改员工部门');
            $this->success('修改成功', $this->url->site('system/department'));
        } else {
            $this->m_operate->failure('修改员工部门');
            $this->failure('没有任何修改');
        }
    }

//    public function get_formats() {
//
//        return $this->gen_tree($this->category_where()->finds(), 'id', 'classid');
//    }

    public function get_formats($count = false, $m = 0, $id = 0) {
        $this->category = $this->category_where($count)->finds();
        $this->formats($m, $id);
        $category = $this->formats;
        $this->formats = array();
        return $category;
    }

    function formats($m, $id) {
        $n = str_pad('', $m, '-', STR_PAD_RIGHT);
        $n = str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $n);
        foreach ($this->category as $var) {
            if ($var['classid'] == $id) {
                $var['name'] = $n . "|--" . $var['name'];
                $this->formats[] = $var;
                $this->formats($m + 1, $var['id']);
            }
        }
    }

    public function category_where($count = false) {
        $this->order('a.sort desc');
        $this->group('a.id');
        if ($count)
            $this->left_join('staff as s', 'a.id = s.c_id');

        $this->from('staff_category as a')->select('a.*' . ($count ? ',COUNT(DISTINCT s.uid) as staff' : ''));
        return $this;
    }

}
