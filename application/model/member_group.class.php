<?php

namespace model;

/**
 * 会员分类
 * @author feisanliang
 */
class member_group extends common {

    public $primary_key = 'id';
    private $formats, $category;

    public function add_submit() {
        $data['name'] = $this->t_input->post('name');
        $data['discounts'] = $this->t_input->post('discounts');
        $data['classid'] = (int) $this->t_input->post('classid');
        if (empty($data['name']))
            $this->failure('请输入分组名称');
        if (!empty($data['discounts']) && $data['discounts'] > 10)
            $this->failure('折扣参数不正确');
        if ($this->insert($data)) {
            $this->m_operate->success('新增会员分组');
            $this->success('新增成功', $this->url->site('member/group'));
        } else {
            $this->m_operate->failure('新增会员分组');
            $this->failure('新增失败');
        }
    }

    public function delete_submit($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除会员分组');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除会员分组');
            $this->failure('删除失败');
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

            $this->m_operate->success('修改会员分组排序');
            $this->success('排序成功');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['name'] = $this->t_input->post('name');
        $data['classid'] = (int) $this->t_input->post('classid');
        $data['discounts'] = $this->t_input->post('discounts');
        if (empty($data['name']))
            $this->failure('请输入分组名称');
        if (!empty($data['discounts']) && $data['discounts'] > 10)
            $this->failure('折扣参数不正确');
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改会员分组');
            $this->success('修改成功', $this->url->site('member/group'));
        } else {
            $this->m_operate->failure('修改会员分组');
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
        $this->order('a.sort desc');

        $this->group('a.id');

        //$this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');

        $this->left_join('member as m', 'a.id = m.g_id');

        $this->from('member_group as a')->select('a.*,COUNT(DISTINCT m.id) as member');
        return $this;
    }

}
