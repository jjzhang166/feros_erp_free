<?php

/**
 * 订单
 */

namespace model;

class product_w_order extends common {

    public $primary_key = 'id';

    public function pr_where($py = NULL) {

        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));

        $this->where(array('ws.uid' => $this->get_uid()));

        if ($this->t_input->request('supplier'))
            $this->where(array('a.supplier' => $this->t_input->request('supplier')));


        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));



        if ($this->t_input->request('type'))
            $this->where(array('pr.type' => $this->t_input->request('type')));

        if ($this->t_input->request('warehouse'))
            $this->where(array('w.w_id' => $this->t_input->request('warehouse')));


        if ($this->t_input->request('lowesta'))
            $this->where(array('w.number[>=]' => $this->t_input->request('lowesta')));

        if ($this->t_input->request('lowestb'))
            $this->where(array('w.number[<=]' => $this->t_input->request('lowestb')));


        if ($this->t_input->request('c_id'))
            $this->where(array('pr.c_id' => $this->t_input->request('c_id')));
        if ($this->t_input->request('keyword')) {
            unset($where);
            if (is_numeric($this->t_input->request('keyword')))
                $where['%w.id%'] = abs($this->t_input->request('keyword'));
            $where['%a.number%'] = $this->t_input->request('keyword');
            $where['%pr.code%'] = $this->t_input->request('keyword');
            $where['%pr.name%'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }



        $this->left_join('staff as s', 'a.uid=s.uid');
        $this->left_join('supplier as su', 'a.supplier=su.id');

        $this->left_join('product_w as w', 'w.o_id=a.id');
        $this->left_join('product_warehouse_staff as ws', 'w.w_id=ws.w_id');
        $this->left_join('product as pr', 'w.p_id=pr.id');


        $this->select('a.*,s.realname,su.company');
        $this->order('a.id desc');
        $this->from('product_w_order as a');
        return $this;
    }

}
