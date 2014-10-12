<?php

/**
 * 账务分类
 * @author feisanliang
 */

namespace model;

class finance_category extends common {

    public $primary_key = 'id';
    private $formats, $category;

    public function json() {
        $json = array();
        $array = array(array('v' => '0', 'n' => '支出'), array('v' => '1', 'n' => '收入'));
        foreach ($array as $value) {
            foreach ($this->get_formats() as $var) {
                if ($var['type'] == $value['v'])
                    $value['s'][] = array('v' => $var['id'], 'n' => $var['name']);
            }
            $json[] = $value;
        }
        return json_encode($json);
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
        $n = str_replace("-", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $n);
        foreach ($this->category as $var) {
            if ($var['classid'] == $id) {
                $var['name'] = $n . "|--" . $var['name'];
                $this->formats[] = $var;
                $this->formats($m + 1, $var['id']);
            }
        }
    }

    public function category_where() {
        if (is_numeric($this->t_input->request('type')))
            $this->where(array('a.type' => $this->t_input->request('type')));

//        if ($this->t_input->request('keyword')) {
//            $where['%a.name%'] = $this->t_input->request('keyword');
//            $where['p.py'] = $this->t_input->request('keyword');
//            $this->where_or($where);
//        }

        $this->order('sort desc');
        //$this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        $this->select('a.*');
        $this->from('finance_category as a');
        return $this;
    }

    /**
     * 新增
     */
    public function category_add_submit() {
        $data['classid'] = $this->t_input->post('classid')? : 0;
        $data['type'] = $this->t_input->post('type') ? 1 : 0;
        $data['name'] = $this->t_input->post('name');
        if (empty($data['name']))
            $this->failure('请输入银行分类名称');
        if ($this->insert($data)) {
            $this->m_operate->success('新增银行分类');
            $this->success('新增成功', $this->url->site('finance/category'));
        } else {
            $this->m_operate->failure('新增银行分类');
            $this->failure('新增失败');
        }
    }

    /**
     * 修改
     */
    public function category_edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['classid'] = $this->t_input->post('classid')? : 0;
        $data['type'] = $this->t_input->post('type') ? 1 : 0;
        $data['name'] = $this->t_input->post('name');
        if (empty($data['name']))
            $this->failure('请输入银行分类名称');
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改银行分类');
            $this->success('修改成功', $this->url->site('finance/category'));
        } else {
            $this->m_operate->failure('修改银行分类');
            $this->failure('没有任何修改');
        }
    }

    /**
     * 排序
     */
    public function category_sort_submit() {
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
            $this->success('排序成功' . $sql);
        }
    }

    /**
     * 删除
     */
    public function category_delete($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除账务分类');
            $this->success('删除成功', $this->url->site('finance/category'));
        } else {
            $this->m_operate->failure('删除账务分类');
            $this->failure('删除失败', $this->url->site('finance/category'));
        }
    }

}
