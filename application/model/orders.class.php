<?php

/**
 * 订单管理
 */

namespace model;

class orders extends common {

    public $primary_key = 'id';

    public function orders_where($py = NUll) {
        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));

        $this->where(array('ws.uid' => $this->get_uid()));

        if ($this->t_input->request('status'))
            $this->where(array('d.status' => $this->t_input->request('status')));

        if ($this->t_input->request('amounta'))
            $this->where(array('a.amount[>=]' => $this->t_input->request('amounta')));


        if ($this->t_input->request('amountb'))
            $this->where(array('a.amount[<=]' => $this->t_input->request('amountb')));

        if ($this->t_input->request('pointsa'))
            $this->where(array('a.points[>=]' => $this->t_input->request('pointsa')));

        if ($this->t_input->request('pointsb'))
            $this->where(array('a.points[<=]' => $this->t_input->request('pointsb')));
        if ($this->t_input->request('number'))
            $this->where(array('%a.number%' => $this->t_input->request('number')));

        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));


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
        if ($this->t_input->request('keyword')) {
            unset($where);
            $where['%p.code%'] = $this->t_input->request('keyword');
            $where['%p.name%'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }
        if ($this->t_input->request('type'))
            $this->where(array('p.type' => $this->t_input->request('type')));
        if ($this->t_input->request('warehouse'))
            $this->where(array('d.warehouse' => $this->t_input->request('warehouse')));


        $this->left_join('member as m', 'm.id=a.member');
        if (!empty($py)) {
            $this->where(array('py.py' => $py));
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(m.realname USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }
        $this->left_join('staff as s', 'a.uid=s.uid');
        $this->left_join('orders_data as d', 'd.o_id=a.id');
        $this->left_join('product as p', 'd.product=p.id');

        $this->left_join('product_warehouse_staff as ws', 'd.warehouse=ws.w_id');

        $this->order('a.id desc');

        $this->from('orders as a')->select('a.*,m.realname,s.realname as staff_realname,COUNT(DISTINCT d.id) as count_data');
        return $this;
    }

    public function get_status($x = NULL) {
        static $status = array(
            1 => '<span class="label label-success">已完成</span>',
            -1 => '<span class="label label-warning">有退货</span>',
            -2 => '<span class="label label-important">已退货</span>'
        );
        return isset($status[$x]) ? $status[$x] : $$status;
    }

    public function return_orders($o_id, $status = -1) {
        $this->limit(1)->update(array('status' => $status), $o_id);
    }

    public function chart($day) {

        $list = $this->select('d.*,a.time')->group('d.id')->finds();



//销售 sales 
//实际 actual
//利润 profit

      //  print_r($list);


        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['sales'][$index + 1] = $this->get_sales($list, $datea, $dateb);
            $d['actual'][$index + 1] = $this->get_actual($list, $datea, $dateb);
            $d['profit'][$index + 1] = $this->get_profit($list, $datea, $dateb);
            $d['quantity'][$index + 1] = $this->get_count($list, $datea, $dateb);
            $d['date'][$index + 1] = "'{$date}'";
        }

        return $d;
    }

    private function get_count($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+=$var['quantity'];
        }
        return $vars;
    }

    private function get_sales($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+=$var['amount'];
        }
        return $vars;
    }

    private function get_actual($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['status'] > 0 && $var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+=$var['amount'];
        }
        return $vars;
    }

    private function get_profit($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['status'] > 0 && $var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+=$var['amount'] - $var['cost'];
        }
        return $vars;
    }

}
