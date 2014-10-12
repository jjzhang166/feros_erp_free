<?php

/**
 * 菜单管理
 */

namespace model;

class orders_data extends common {

    public $primary_key = 'id';

    public function get_data($id) {
        return $this->from('orders_data as a')->where(array('o_id' => $id))->group('a.id')->left_join('product_warehouse as pw', 'pw.w_id=a.warehouse')->finds();
    }

    public function get_status($x = NULL) {
        static $status = array(
            1 => '<span class="label label-success">已完成</span>',
            -1 => '<span class="label label-warning">有退货</span>',
            -2 => '<span class="label label-important">已退货</span>'
        );
        return isset($status[$x]) ? $status[$x] : $$status;
    }

    public function return_request($id) {
        $data['warehouse'] = $this->t_input->post('warehouse');
        $data['quantity'] = $this->t_input->post('quantity');
        $points = $this->t_input->post('points');

        if (empty($data['warehouse']))
            $this->failure('请选择仓库');
        if (empty($data['quantity']))
            $this->failure('请确定退货数量');

        $list = $this->get_info($id);
        $quantity = $list['quantity'] - $list['return'];

        if ($list['return'] >= $list['quantity'])
            $this->failure('该产品已经完全退货');

        if (empty($list['quantity']) || $quantity < $data['quantity'])
            $this->failure('退货数量不能大与' . $quantity);

        if ($this->m_product_inventory->increase((int) $list['product_data']['id'], (int) $data['warehouse'], (int) $data['quantity'])) {
            $this->limit(1)->update(array('return[+]' => $data['quantity']), $id);
            if (!empty($list['member'])) {
                if ($list['amount'] <= 0 && $this->t_input->post('points'))
                    $this->m_member->increase($list['member'], floor(($list['integral'] / $list['quantity']) * $data['quantity']), '退货产品=>' . $list['product_data']['name'], $list['o_id']);
                elseif ($this->t_input->post('points'))
                    $this->m_member->reduce($list['member'], floor(($list['integral'] / $list['quantity']) * $data['quantity']), '退货产品=>' . $list['product_data']['name'], $list['o_id']);
            }
            $this->m_product_return->add($id, $data, ($this->t_input->post('points') ? 1 : 0));

            $this->m_orders->return_orders($list['o_id']);
            $this->limit(1)->update(array('status' => -1), $id);



            $list = $this->get_info($id);


            if ($list['return'] >= $list['quantity'])
                $this->limit(1)->update(array('status' => -2), $id);

            if (!$this->where(array('o_id' => $list['o_id'], 'status[>=]' => -1))->find()) {
                $this->m_orders->return_orders($list['o_id'], -2);
            }


            $this->m_operate->success('退货产品');
            $this->success('操作成功', $this->t_input->post('url'));
        } else {
            $this->m_operate->failure('退货产品');
            $this->failure('操作失败');
        }
    }

    public function get_info($id) {
        $list = $this->from('orders_data as a')->select('a.*,b.member')->left_join('orders as b', 'a.o_id=b.id')->where(array('a.id' => $id))->find();
        if (empty($list['product_data']))
            return FALSE;
        $list['product_data'] = unserialize($list['product_data']);
        return $list;
    }

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




        $this->left_join('orders  as a', 'd.o_id=a.id');


        $this->left_join('member as m', 'm.id=a.member');
        if (!empty($py)) {
            $this->where(array('py.py' => $py));
            $this->left_join('pinyin as py', 'CONV(HEX(LEFT(CONVERT(m.realname USING GBK),1)),16,10) BETWEEN py.begin AND py.end');
        }


        $this->left_join('staff as s', 'a.uid=s.uid');

        $this->left_join('product as p', 'd.product=p.id');

        $this->left_join('product_warehouse_staff as ws', 'd.warehouse=ws.w_id');

        $this->left_join('product_warehouse as pw', 'pw.w_id=d.warehouse');


        $this->order('a.id desc');

        $this->from('orders_data as d')->select('d.*,a.time,a.ship,pw.w_name,m.realname,s.realname as staff_realname');
        return $this;
    }

}
