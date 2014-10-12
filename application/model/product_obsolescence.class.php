<?php

/**
 * 产品报废
 */

namespace model;

class product_obsolescence extends common {

    public $primary_key = 'id';

    public function add_submit($id) {

        $list = $this->m_product_inventory->pr_where()->where(array('w.id' => $id))->find();

        if (empty($list))
            $this->failure('产品不存在');

        if (!$this->t_input->post('quantity') || !is_numeric($this->t_input->post('quantity')) || $list['quantity'] < $this->t_input->post('quantity'))
            $this->failure('库存不足,无法出库');

        $data['uid'] = $this->get_uid();
        $data['remark'] = $this->t_input->post('remark');
        $data['create'] = time();

        $data['product'] = $list['id'];
        $data['warehouse'] = $list['w_id'];
        $data['quantity'] = (int) $this->t_input->post('quantity');
        if ($this->insert($data)) {
            $this->m_product_inventory->reduce($data['product'], $data['warehouse'], $data['quantity']);
            $this->m_operate->success('报废产品');
            $this->success('操作成功', $this->t_input->post('url'));
        } else {
            $this->m_operate->failure('报废产品');
            $this->failure('操作失败');
        }
    }

    public function ors_where($py = NULL) {
        if ($this->t_input->request('timea'))
            $this->where(array('a.create[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.create[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));

        if ($this->t_input->request('type'))
            $this->where(array('p.type' => $this->t_input->request('type')));

        if ($this->t_input->request('warehouse'))
            $this->where(array('a.warehouse' => $this->t_input->request('warehouse')));




        if ($this->t_input->request('c_id'))
            $this->where(array('p.c_id' => $this->t_input->request('c_id')));

        if ($this->t_input->request('keyword')) {
            unset($where);
            if (is_numeric($this->t_input->request('keyword')))
                $where['%a.id%'] = abs($this->t_input->request('keyword'));
            $where['%p.code%'] = $this->t_input->request('keyword');
            $where['%p.name%'] = $this->t_input->request('keyword');

            $this->where_or($where);
        }
        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));


        $this->where(array('ws.uid' => $this->get_uid()));

        $this->left_join('product_warehouse_staff as ws', 'a.warehouse=ws.w_id');

        $this->left_join('product_warehouse as pw', 'pw.w_id=a.warehouse');

        $this->left_join('product as p', 'a.product=p.id');

        $this->left_join('product_category as pc', 'p.c_id=pc.id');

        if (!empty($py)) {
            $this->where(array('py.py' => $py));
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(p.name USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }
        $this->left_join('staff as s', 'a.uid=s.uid');

        $this->select('a.*,s.realname,p.name,p.code,pw.w_name,pc.name as category,p.type,p.id as product_id');
        $this->order('a.id desc');
        $this->from('product_obsolescence as a');
        return $this;
    }

    public function chart($day) {

        $list = $this->finds();
        $d = array();
        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $d['obsolescence'][$index + 1] = $this->get_charttime($list, $datea, $dateb);
            $d['date'][$index + 1] = "'{$date}'";
        }

        return $d;
    }

    private function get_charttime($list, $datea, $dateb) {
        $vars = 0;
        foreach ($list as $var) {
            if ($var['create'] >= $datea && $var['create'] <= $dateb)
                $vars+= $var['quantity'];
        }
        return $vars;
    }

}
