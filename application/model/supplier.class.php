<?php

/**
 * 供应商
 */

namespace model;

class supplier extends common {

    public $primary_key = 'id';

    public function sup_where($py = NULL) {
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.company USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }
        if ($this->t_input->post('keyword')) {
            $where['%a.company%'] = $this->t_input->post('keyword');
            $where['%a.name%'] = $this->t_input->post('keyword');
            $this->where_or($where);
        }
        $this->order('a.id desc');

        $this->left_join('staff as m', 'a.uid=m.uid');
        $this->left_join('staff as m2', 'a.replace_uid=m2.uid');

        $this->from('supplier a')->select('a.*,m.realname,m2.realname as realname_replace');
        return $this;
    }

    public function delete_submit($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除供应商名称');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除供应商名称');
            $this->failure('删除失败');
        }
    }

    public function edit_submit() {
        if (!$this->t_input->post('id'))
            $this->failure('参数出错');
        $data['company'] = $this->t_input->post('company');
        $data['name'] = $this->t_input->post('name');
        $data['tel'] = $this->t_input->post('tel');
        $data['fax'] = $this->t_input->post('fax');
        $data['shouji'] = $this->t_input->post('shouji');
        $data['site'] = $this->t_input->post('site');
        $data['email'] = $this->t_input->post('email');
        $data['pc'] = $this->t_input->post('pc');
        $data['address'] = $this->t_input->post('address');
        $data['beizhu'] = $this->t_input->post('beizhu');

        $data['replace'] = time();
        $data['replace_uid'] = $this->get_uid();
        if (empty($data['company']))
            $this->failure('请输入供应商名称');
        if ($this->limit(1)->update($data, $this->t_input->post('id'))) {
            $this->m_operate->success('修改供应商');
            $this->success('修改成功', $this->t_input->post('url'));
        } else {
            $this->m_operate->failure('修改供应商');
            $this->failure('修改失败');
        }
    }

    public function add_submit() {
        $data['uid'] = $this->get_uid();
        $data['company'] = $this->t_input->post('company');
        $data['name'] = $this->t_input->post('name');
        $data['tel'] = $this->t_input->post('tel');
        $data['fax'] = $this->t_input->post('fax');
        $data['shouji'] = $this->t_input->post('shouji');
        $data['site'] = $this->t_input->post('site');
        $data['email'] = $this->t_input->post('email');
        $data['pc'] = $this->t_input->post('pc');
        $data['address'] = $this->t_input->post('address');
        $data['beizhu'] = $this->t_input->post('beizhu');
        $data['create'] = time();
        $data['replace'] = time();
        $data['replace_uid'] = $this->get_uid();
        if (empty($data['company']))
            $this->failure('请输入供应商名称');
        if ($this->insert($data)) {
            $this->m_operate->success('新增供应商');
            $this->success('新增成功', $this->t_input->post('url'));
        } else {
            $this->m_operate->failure('新增供应商');
            $this->failure('新增失败');
        }
    }

}
