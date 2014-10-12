<?php

/**
 * 菜单管理
 */

namespace model;

class product_return_supplier extends common {

    public $primary_key = 'id';

    public function add_submit($id) {
        $data['uid'] = $this->get_uid();
        $data['w_id'] = $id;
        $data['quantity'] = $this->t_input->post('quantity');
        $data['remark'] = $this->t_input->post('remark');
        $data['time'] = time();
        if (!is_numeric($data['quantity']))
            $this->failure('参数不正确');
        $list = $this->m_product->pr_where()->group('w.id')->where(array('w.id' => $id))->find();
        $inventory = $this->m_product_inventory->where(array('product' => $list['id'], 'warehouse' => $list['wid']))->find();
        if (empty($list) || empty($inventory))
            $this->failure('参数不正确');
        $return = $list['number'] - $list['return'];
        if ($return > 0) {
            if ($inventory['quantity'] > $return) {
                $return = $inventory['quantity'] - $return;
            } elseif ($inventory['quantity'] <= $return) {
                $return = $inventory['quantity'];
            }
        } else {
            $return = 0;
        }


        if (empty($return) || $return < $data['quantity'])
            $this->failure('仓库库存不足 无法退回');


        if ($this->insert($data)) {
            $this->m_product_w->limit(1)->update(array('return[+]' => (int) $data['quantity']), $id);
            $this->m_product_inventory->reduce($list['id'], $list['wid'], (int) $data['quantity']);

            $this->m_operate->success('退回产品');
            $this->success('退回成功', $this->t_input->post('url'));
        } else {
            $this->m_operate->failure('退回产品');
            $this->failure('退回失败');
        }



        //$this->m_product->pr_where()->group('w.id')->where(array('w.id' => $id))->find();

        $this->failure($id);
    }

    public function sup_where($py = NULL) {
        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));


        $this->where(array('ws.uid' => $this->get_uid()));

        if ($this->t_input->request('type'))
            $this->where(array('p.type' => $this->t_input->request('type')));
        if ($this->t_input->request('c_id'))
            $this->where(array('p.c_id' => $this->t_input->request('c_id')));
        if ($this->t_input->request('supplier'))
            $this->where(array('w.s_id' => $this->t_input->request('supplier')));
        if ($this->t_input->request('warehouse'))
            $this->where(array('w.w_id' => $this->t_input->request('warehouse')));
        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));
        if ($this->t_input->request('keyword')) {
            unset($where);
            $where['%p.code%'] = $this->t_input->request('keyword');
            $where['%p.name%'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }

        $this->left_join('staff as s', 'a.uid=s.uid');
        $this->left_join('product_w as w', 'a.w_id=w.id');
        $this->left_join('product_warehouse as pw', 'pw.w_id=w.w_id');

        $this->left_join('product as p', 'w.p_id=p.id');

        if (!empty($py)) {
            $this->where(array('py.py' => $py));
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(p.name USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }

        $this->left_join('product_category as pc', 'p.c_id=pc.id');
        $this->left_join('supplier as su', 'w.s_id=su.id');

        $this->left_join('product_warehouse_staff as ws', 'w.w_id=ws.w_id');

        $this->select('a.*,pw.w_name,s.realname,p.code,p.name,p.type,pc.name as category,su.company');

        $this->order('a.id desc');

        $this->from('product_return_supplier as a');
        return $this;
    }

    public function chart($day) {

        $list = $this->finds();
        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['quantity'][$index + 1] = $this->get_charttime($list, $datea, $dateb);
            $d['date'][$index + 1] = "'{$date}'";
        }
       
        return $d;
    }

    private function get_charttime($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+= $var['quantity'];
        }
        return $vars;
    }

}
