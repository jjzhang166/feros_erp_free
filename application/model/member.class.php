<?php

/**
 * 菜单管理
 */

namespace model;

class member extends common {

    public $primary_key = 'id';

    /**
     * 增加积分
     */
    public function increase($id, $points, $title, $m_id = NULL) {
        $data['points[+]'] = $points;
        $this->limit(1)->update($data, $id) ? $this->m_member_points->add($id, 1, $title, $points, $m_id) : false;
    }

    /**
     * 减少积分
     */
    public function reduce($id, $points, $title, $m_id = NULL) {
        $data['points[-]'] = $points;
        $this->limit(1)->update($data, $id) ? $this->m_member_points->add($id, 0, $title, $points, $m_id) : false;
    }

    /**
     * 检查是否可以销售产器
     */
    public function check_reduce($id, $number) {
        $list = $this->find($id);
        if (!empty($list) && $list['points'] >= $number) {
            return TRUE;
        }
        return FALSE;
    }

    public function json() {
        if ($this->t_input->request('query')) {
            $where['%p.py%'] = $this->t_input->request('query');
            $where['%card%'] = $this->t_input->request('query');
            $where['%realname%'] = $this->t_input->request('query');
            $this->where_or($where);
        }
        $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.realname USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        $array = $this->select('a.*,c.name,c.discounts')->from('member a')->left_join('member_group as c', 'a.g_id=c.id')->limit(10)->finds();
        $json['query'] = $this->t_input->request('query');
        foreach ($array as $value) {
            $json['suggestions'][] = array('value' => $value['realname'], 'id' => $value['id'], 'discounts' => $value['discounts'], 'name' => $value['name']);
        }
        return json_encode($json);
    }

    public function edit_submit() {
        $this->failure("免费版不提供该功能");
    }

    public function add_submit() {

        if (!$this->t_input->post('realname'))
            $this->failure('请输入会员姓名');

        $data['uid'] = $this->get_uid();
        $data['g_id'] = $this->t_input->post('g_id');
        $data['card'] = $this->t_input->post('card');
        $data['realname'] = $this->t_input->post('realname');
        $data['sex'] = $this->t_input->post('sex');
        $data['tel'] = $this->t_input->post('tel');
        $data['qq'] = $this->t_input->post('qq');
        $data['email'] = $this->t_input->post('email');
        $data['address'] = $this->t_input->post('address');
        $data['id_card'] = $this->t_input->post('id_card');

        $data['remark'] = $this->t_input->post('remark');
        $data['time'] = time();
        $data['update_time'] = time();
        $data['update'] = $this->get_uid();
        if (!empty($data['card']) && $this->where(array('card' => $data['card']))->find())
            $this->failure('会员卡号已存在');

        if ($this->t_input->post('year') && $this->t_input->post('month') && $this->t_input->post('day'))
            $data['birthday'] = $this->t_input->post('year') . '-' . $this->t_input->post('month') . '-' . $this->t_input->post('day');


        if ($this->insert($data)) {
            $this->m_operate->success('新增会员');
            $this->success('新增成功', $this->url->site('member/add'));
        } else {
            $this->m_operate->failure('新增会员');
            $this->failure('新增失败');
        }
    }

    public function member_where($py = NULL) {
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.realname USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }
        if ($this->t_input->request('staff_uid')) {
            $where = array('a.uid' => $this->t_input->request('staff_uid'));
            $this->where($where);
        }
        if ($this->t_input->request('g_id')) {
            $where = array('a.g_id' => $this->t_input->request('g_id'));
            $this->where($where);
        }
        if ($this->t_input->request('birthday')) {
            $where = array('a.birthday' => $this->t_input->request('birthday'));
            $this->where($where);
        }
       
        
        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));
        
        

        if ($this->t_input->request('realname')) {
            $where = array('%a.realname%' => $this->t_input->request('realname'));
            $this->where($where);
        }
        if ($this->t_input->request('card')) {
            $where = array('%a.card%' => $this->t_input->request('card'));
            $this->where($where);
        }
        if ($this->t_input->request('tel')) {
            $where = array('%a.tel%' => $this->t_input->request('tel'));
            $this->where($where);
        }
        if ($this->t_input->request('qq')) {
            $where = array('%a.qq%' => $this->t_input->request('qq'));
            $this->where($where);
        }

        if ($this->t_input->request('id_card')) {
            $where = array('%a.id_card%' => $this->t_input->request('id_card'));
            $this->where($where);
        }
        if ($this->t_input->request('email')) {
            $where = array('%a.email%' => $this->t_input->request('email'));
            $this->where($where);
        }
        if ($this->t_input->request('address')) {
            $where = array('%a.address%' => $this->t_input->request('address'));
            $this->where($where);
        }
        if ($this->t_input->request('remark')) {
            $where = array('%a.remark%' => $this->t_input->request('remark'));
            $this->where($where);
        }






        $this->order('a.id desc');

        $this->left_join('member_group as c', 'a.g_id=c.id');



        $this->left_join('staff as s', 'a.uid=s.uid');

        $this->left_join('staff as s2', 'a.update=s2.uid');

        $this->from('member a')->select('a.*,s.realname as s_realname,s2.realname as u_realname,c.name as category,c.discounts');
        return $this;
    }

    public function delete_submit($id) {
        if ($this->limit(1)->delete($id)) {
            $this->m_operate->success('删除会员信息');
            $this->success('删除成功');
        } else {
            $this->m_operate->failure('删除会员信息');
            $this->failure('删除失败');
        }
    }

}
