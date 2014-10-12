<?php

/**
 * 账务管理
 */

namespace model;

class finance_accounts extends common {

    public $primary_key = 'id', $table_name = 'finance_accounts';

    public function query_delete($id) {
        $this->failure("免费版不提供该功能");
    }

    public function add_submit() {
        $data['uid'] = $this->get_uid();
        $data['type'] = (int) $this->t_input->post('type');
        $data['c_id'] = $this->t_input->post('c_id');
        $data['b_id'] = $this->t_input->post('b_id');
        $data['money'] = abs($this->t_input->post('money'));
        $data['datetime'] = $this->t_input->post('datetime') ? strtotime($this->t_input->post('datetime')) : time();
        $data['attn'] = $this->t_input->post('attn')? : $this->get_uid();
        $data['remark'] = $this->t_input->post('remark');
        $data['create'] = time();
        if (empty($data['c_id']))
            $this->failure('请提供账务分类');
        if (empty($data['b_id']))
            $this->failure('请提供账务银行');
        if (empty($data['money']) || !is_numeric($data['money']))
            $this->failure('请提供金额');
        if (empty($data['type'])) {
            $var = $this->m_finance_bank->find($data['b_id']);
            if (!empty($var['id']) && $var['money'] < $data['money'])
                $this->failure('当前银行金额不足:' . $data['money'] . ' 无法支出');
        }
        if ($this->insert($data)) {
            empty($data['type']) ? $this->m_finance_bank->expenditure($data['money'], $data['b_id']) : $this->m_finance_bank->income($data['money'], $data['b_id']);
            $this->m_operate->success('新增账务');
            $this->success('新增成功', $this->url->site('finance/add'));
        } else {
            $this->m_operate->failure('新增账务');
            $this->failure('新增失败');
        }
    }

    public function accounts_where($py = NULL) {

        if (is_numeric($this->t_input->request('status')))
            $this->where(array('a.status' => $this->t_input->request('status') ? 1 : 0));

        if (is_numeric($this->t_input->request('uid')))
            $this->where(array('a.uid' => $this->t_input->request('uid')));


        if (is_numeric($this->t_input->request('attn')))
            $this->where(array('a.attn' => $this->t_input->request('attn')));


        if ($this->t_input->request('b_id') && is_numeric($this->t_input->request('b_id')))
            $this->where(array('a.b_id' => $this->t_input->request('b_id')));
        if (is_numeric($this->t_input->request('type')))
            $this->where(array('a.type' => $this->t_input->request('type') ? 1 : 0));
        if ($this->t_input->request('c_id') && is_numeric($this->t_input->request('c_id')))
            $this->where(array('a.c_id' => $this->t_input->request('c_id')));

        if ($this->t_input->request('timea'))
            $this->where(array('a.create[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));
        if ($this->t_input->request('timeb'))
            $this->where(array('a.create[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));
        if ($this->t_input->request('datetimea'))
            $this->where(array('a.datetime[>=]' => strtotime($this->t_input->request('datetimea') . ' 00:00:00')));
        if ($this->t_input->request('datetimeb'))
            $this->where(array('a.datetime[<=]' => strtotime($this->t_input->request('datetimeb') . ' 23:59:59')));

        if ($this->t_input->request('moneya'))
            $this->where(array('a.money[>=]' => $this->t_input->request('moneya')));
        if ($this->t_input->request('moneyb'))
            $this->where(array('a.money[<=]' => $this->t_input->request('moneyb')));





//        if ($this->t_input->request('keyword')) {
//            unset($where);
//            $where['or']['%m.realname%'] = $this->t_input->request('keyword');
//            $where['or']['%t.realname%'] = $this->t_input->request('keyword');
//            $where['or']['%p.py%'] = $this->t_input->request('keyword');
//            $where['or']['%py.py%'] = $this->t_input->request('keyword');
//            $this->where($where);
//        }
        if ($this->t_input->request('remark')) {
            unset($where);
            $where['or']['%a.remark%'] = $this->t_input->request('remark');
            $this->where($where);
        }



        $this->order('a.id desc');

        $this->left_join('staff as m', 'a.uid=m.uid');
        $this->left_join('staff as t', 'a.attn=t.uid');

        if (!empty($py)) {
            $where['or']['p.py'] = $py;
            $where['or']['py.py'] = $py;
            $this->where($where);
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(m.realname USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(t.realname USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }

        $this->left_join('finance_bank as b', 'a.b_id=b.id');
        $this->left_join('finance_category as c', 'a.c_id=c.id');





        $this->from('finance_accounts as a')->select('a.*,b.bank,c.name as c_name,m.realname,t.realname as realname_attn');
        return $this;
    }

    public function chart($day) {

        $list = $this->finds();
        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['expenditure'][$index + 1] = $this->get_charttime($list, 0, $datea, $dateb);
            $d['revenue'][$index + 1] = $this->get_charttime($list, 1, $datea, $dateb);
            $d['date'][$index + 1] = "'{$date}'";
        }

        return $d;
    }

    private function get_charttime($list, $type, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['type'] == $type && $var['create'] >= $datea && $var['create'] <= $dateb)
                $vars+= $var['money'];
        }
        return $vars;
    }

}
