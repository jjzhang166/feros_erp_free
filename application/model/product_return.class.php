<?php

/**
 * 产品退货
 */

namespace model;

class product_return extends common {

    public $primary_key = 'id';

    public function add($orders_data, $data, $is_points = 0) {
        $data['uid'] = $this->get_uid();
        $data['orders_data'] = $orders_data;
        $data['time'] = time();
        $data['quantity'] = $data['quantity'];
        $data['warehouse'] = $data['warehouse'];
        $data['is_points'] = $is_points;
        $data['remark'] = $this->t_input->post('remark');
        $this->insert($data);
    }

    public function pr_where($py = NUll) {
        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea'). ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb'). ' 23:59:59')));

        
        $this->where_or(array('ws.uid' => $this->get_uid(),'ws2.uid' => $this->get_uid()));
        
        
        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));


        if ($this->t_input->request('keyword')) {
            unset($where);
            $where['OR']['%p.code%'] = $this->t_input->request('keyword');
            $where['OR']['%p.name%'] = $this->t_input->request('keyword');
            $this->where($where);
            unset($where);
        }
        if ($this->t_input->request('number'))
            $this->where(array('%c.number%' => $this->t_input->request('number')));

        if ($this->t_input->request('realname')) {
            $where = array('%m.realname%' => $this->t_input->request('realname'));
            $this->where($where);
        }
        if ($this->t_input->request('card')) {
            $where = array('%m.card%' => $this->t_input->request('card'));
            $this->where($where);
        }
        if ($this->t_input->request('id_card')) {
            $where = array('%m.id_card%' => $this->t_input->request('id_card'));
            $this->where($where);
        }
        if ($this->t_input->request('tel')) {
            $where = array('%m.tel%' => $this->t_input->request('tel'));
            $this->where($where);
        }
        if ($this->t_input->request('qq')) {
            $where = array('%m.qq%' => $this->t_input->request('qq'));
            $this->where($where);
        }

        if ($this->t_input->request('c_id'))
            $this->where(array('p.c_id' => $this->t_input->request('c_id')));
        if ($this->t_input->request('type'))
            $this->where(array('p.type' => $this->t_input->request('type')));


        if ($this->t_input->request('warehouse'))
            $this->where(array('a.warehouse' => $this->t_input->request('warehouse')));
        if ($this->t_input->request('warehouse2'))
            $this->where(array('b.warehouse' => $this->t_input->request('warehouse2')));


        $this->left_join('orders_data as b', 'a.orders_data=b.id');

        $this->left_join('orders as c', 'b.o_id=c.id');

        $this->left_join('member as m', 'm.id=c.member');
        if (!empty($py)) {
            $this->where(array('py.py' => $py));
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(m.realname USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }


        $this->left_join('product_warehouse_staff as ws', 'b.warehouse=ws.w_id');
        $this->left_join('product_warehouse_staff as ws2', 'a.warehouse=ws2.w_id');

        $this->left_join('product_warehouse as pw', 'pw.w_id=a.warehouse');

        $this->left_join('product_warehouse as pw2', 'pw2.w_id=b.warehouse');

        $this->left_join('product as p', 'b.product=p.id');


        $this->left_join('staff as s', 'a.uid=s.uid');
        $this->select('a.*,c.id as ordersid,pw.w_name,pw2.w_name as w_name2,s.realname,m.realname as memberrealname,b.product_data');
        $this->order('a.id desc');

        $this->from('product_return as a');

        return $this;
    }
    
    public function chart($day) {

        $list = $this->group('a.id')->finds();
        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['return'][$index + 1] = $this->get_charttime($list, $datea, $dateb);
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
