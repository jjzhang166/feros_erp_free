<?php

/**
 * 调拨
 */

namespace model;

class product_warehouse_allocate extends common {

    public $primary_key = 'id';

    public function add($jin_id, $out_id, $product, $number) {
        $data['uid'] = $this->get_uid();
        $data['jin_id'] = (int) $jin_id;
        $data['out_id'] = (int) $out_id;
        $data['number'] = (int) $number;
        $data['product'] = (int) $product;
        $data['time'] = time();
        $data['remark'] = $this->t_input->post('remark');
        $this->m_operate->success('库存调拨');
        return $this->insert($data);
    }

    public function pr_where($py) {
        if ($this->t_input->request('timea'))
            $this->where(array('a.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

        if ($this->t_input->request('timeb'))
            $this->where(array('a.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));


        $where['OR']['a.uid'] = $this->get_uid();
        $where['OR']['ws1.uid'] = $this->get_uid();
        $where['OR']['ws2.uid'] = $this->get_uid();
        $this->where($where);

        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));

        if ($this->t_input->request('jin_id'))
            $this->where(array('a.jin_id' => $this->t_input->request('jin_id')));
        if ($this->t_input->request('out_id'))
            $this->where(array('a.out_id' => $this->t_input->request('out_id')));

        if ($this->t_input->request('c_id'))
            $this->where(array('ca.id' => $this->t_input->request('c_id')));
        if ($this->t_input->request('type'))
            $this->where(array('pr.type' => $this->t_input->request('type')));

        if ($this->t_input->request('lowesta'))
            $this->where(array('a.number[>=]' => $this->t_input->request('lowesta')));
        if ($this->t_input->request('lowestb'))
            $this->where(array('a.number[<=]' => $this->t_input->request('lowestb')));




        if ($this->t_input->request('keyword')) {
            unset($where);
            if (is_numeric($this->t_input->request('keyword')))
                $where['%a.id%'] = abs($this->t_input->request('keyword'));
            $where['%pr.code%'] = $this->t_input->request('keyword');
            $where['%pr.name%'] = $this->t_input->request('keyword');
            //$where['p.py'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }

        $this->order('a.id desc');

        $this->left_join('product_warehouse_staff as ws1', 'a.jin_id=ws1.w_id');
        $this->left_join('product_warehouse_staff as ws2', 'a.out_id=ws2.w_id');

        $this->left_join('product_warehouse as w', 'w.w_id=a.jin_id');
        $this->left_join('product_warehouse as w2', 'w2.w_id=a.out_id');

        $this->left_join('product as pr', 'a.product=pr.id');
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(pr.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }

        $this->left_join('product_category as ca', 'pr.c_id=ca.id');






        $this->left_join('staff as c', 'a.uid = c.uid');

//,pr.name
        $this->from('product_warehouse_allocate as a')->select('a.*,pr.code,pr.name,pr.format,pr.type,ca.name as category,c.realname,w.w_name as jin_w_name,w2.w_name as out_w_name');

        return $this;
    }

}
