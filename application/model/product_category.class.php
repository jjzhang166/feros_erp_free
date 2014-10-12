<?php

/**
 * 产品分类
 */

namespace model;

class product_category extends common {

    public $primary_key = 'id';
    private $formats, $category;

    public function add_submit() {
        
        $u_name = $this->t_input->post('name');
        $classid=(int) $this->t_input->post('classid');
        if (empty($u_name))
            $this->failure('请输入单位');
        foreach (explode("\n", $u_name) as $value) {
            $value = trim($value);
            if (!empty($value)) {
                $data[]=array('name'=> $value,'classid'=>$classid);
            }
            //   $sql[] = 
        }
        if (!empty($data)) {
            $this->insert_batch($data);
            $this->m_operate->success('新增产品分类');
        }
        $this->success('新增成功', $this->url->site('system/product_categories'));
        
        
        $data['name'] = $this->t_input->post('name');
        $data['classid'] = (int) $this->t_input->post('classid');
        if (empty($data['name']))
            $this->failure('请输入产品名称');
        if ($this->insert($data)) {
            $this->m_operate->success('新增产品分类');
            $this->success('新增成功', $this->url->site('system/product_categories'));
        } else {
            $this->m_operate->failure('新增产品分类');
            $this->failure('新增失败');
        }
    }

    public function delete_submit($id) {
        if ($this->m_product->where(array('c_id' => $id))->find()) {
            $this->m_operate->failure('删除产品分类');
            $this->failure('请先转移分类下的产品再删除');
        } elseif ($this->where(array('classid' => $id))->find()) {
            $this->m_operate->failure('删除产品分类');
            $this->failure('请先删除子分类');
        } elseif ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除产品分类');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除产品分类');
            $this->failure('删除失败');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['name'] = $this->t_input->post('name');
        $data['classid'] = (int) $this->t_input->post('classid');
        if (empty($data['name']))
            $this->failure('请输入产品名称');
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改产品分类');
            $this->success('修改成功', $this->url->site('system/product_categories'));
        } else {
            $this->m_operate->failure('修改产品分类');
            $this->failure('没有任何修改');
        }
    }

    public function get_formats($m = 0, $id = 0) {
        $this->category = $this->category_where()->finds();
        $this->formats($m, $id);
        $category = $this->formats;
        $this->formats = array();
        return $category;
    }

    function formats($m, $id) {
        $n = str_pad('', $m, '-', STR_PAD_RIGHT);
        $n = str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $n);
        foreach ($this->category as $var) {
            if ($var['classid'] == $id) {
                $var['name'] = $n . "|--" . $var['name'];
                $this->formats[] = $var;
                $this->formats($m + 1, $var['id']);
            }
        }
    }

    public function category_where() {
        $this->order('sort desc');

        return $this;
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

}
