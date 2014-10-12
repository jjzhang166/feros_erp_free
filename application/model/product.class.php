<?php

/**
 * 菜单管理
 */

namespace model;

class product extends common {

    public $primary_key = 'id';

    public function storage_add_submit() {
        $product_quantity = $this->t_input->post('product_quantity');
        $product_id = $this->t_input->post('product_id');
        $warehouse = $this->t_input->post('warehouse');
        $quantity = 0;
        foreach ($product_id as $key => $value) {
            if (!empty($value) && is_numeric($value) && ($list = $this->m_product->find($value))) {
                $quantity+=$list['product_quantity'] = empty($product_quantity[$key]) || !is_numeric($product_quantity[$key]) ? 1 : $product_quantity[$key];
                if (empty($warehouse[$key]))
                    $this->failure('请选择仓库');
                $list['warehouse'] = $warehouse[$key];
                $product[] = $list;
            }
        }
        if (empty($product))
            $this->failure('请选择入库产品');


        $data['number'] = date('YmdtHis') . rand(100, 999) . $this->get_uid();
        $data['uid'] = $this->get_uid();
        $data['time'] = time();
        $data['supplier'] = $this->t_input->post('supplier');
        $data['quantity'] = $quantity;
        //$data['amount'] = $amount;
        $data['remark'] = $this->t_input->post('remark');

        if ($insert_id = $this->m_product_w_order->insert($data)) {
            foreach ($product as $k => $value) {
                $data2[$k]['o_id'] = $insert_id;
                $data2[$k]['w_id'] = $value['warehouse'];
                $data2[$k]['p_id'] = $value['id'];
                $data2[$k]['s_id'] = $this->t_input->post('supplier');
                $data2[$k]['number'] = $value['product_quantity'];
                $data2[$k]['uid'] = $this->get_uid();
                $data2[$k]['time'] = time();
                $data2[$k]['remark'] = $this->t_input->post('remark');
                $this->m_product_inventory->increase((int) $value['id'], (int) $value['warehouse'], (int) $value['product_quantity']);
            }
            $this->m_product_w->insert_batch($data2);
            $this->m_operate->success('增加产品库存');
            $this->success('新增成功', $this->url->site('inventory/warehousing'));
        } else {
            $this->m_operate->failure('增加产品库存');
            $this->failure('新增失败');
        }





        $this->failure('新增失败');
        exit;
        $product_id = $this->t_input->post('id');
        if (empty($product_id) || !$this->find($product_id))
            $this->failure('产品不存在或请新建');
        $w_id = $this->t_input->post('warehouse');
        $number = $this->t_input->post('number');
        $supplier = $this->t_input->post('supplier');
        if (empty($w_id))
            $this->failure('请提供产品仓库');
        if (empty($number) || !is_numeric($number))
            $this->failure('请提供库存数量');
        if ($this->m_product_w->add((int) $w_id, (int) $product_id, (int) $number, (int) $supplier, $this->t_input->post('remark'))) {
            $this->m_product_inventory->increase((int) $product_id, (int) $w_id, (int) $number);
            $this->m_operate->success('增加产品库存');
            $this->success('新增成功', $this->url->site('inventory/warehousing'));
        } else {
            $this->m_operate->failure('增加产品库存');
            $this->failure('新增失败');
        }
    }

    public function edit_submit() {
        $this->failure("免费版不提供该功能");
    }

    public function add_submit() {
        $data['uid'] = $this->get_uid();
        $data['c_id'] = $this->t_input->post('c_id');
        $data['code'] = $this->t_input->post('code');
        $data['name'] = $this->t_input->post('name');
        $data['sales'] = $this->t_input->post('sales');
        $data['purchase'] = $this->t_input->post('purchase');
        $data['points'] = $this->t_input->post('points');
        $data['format'] = $this->t_input->post('format');
        $data['lowest'] = $this->t_input->post('lowest');

        $data['type'] = $this->t_input->post('type');
        $data['deduct_type'] = $this->t_input->post('deduct_type');
        $data['deduct'] = $this->t_input->post('deduct');

        $data['unit'] = $this->t_input->post('u_name')? : $this->t_input->post('unit');
        $data['create'] = time();
        $data['update'] = time();
        $data['update_uid'] = $this->get_uid();

        $data['remark'] = $this->t_input->post('remark');

        if (empty($data['c_id']))
            $this->failure('请选择产品分类');
        if (empty($data['code']))
            $this->failure('请提供产品货号');
        if ($this->where(array('code' => $data['code']))->find())
            $this->failure('产品编号已存在');
        if (empty($data['name']))
            $this->failure('请提供产品名称');
        if (empty($data['sales']))
            $this->failure('请提供产品销售价');
        if (empty($data['purchase']))
            $this->failure('请提供产品进货价');

        if (($p_id = $this->insert($data))) {
            $this->m_operate->success('新增产品');
            $this->success('新增成功', $this->url->site('inventory/warehousing'));
        } else {
            $this->m_operate->failure('新增产品');
            $this->failure('新增失败');
        }
    }

    public function json() {
        if ($this->t_input->request('query')) {
            $where['%p.py%'] = $this->t_input->request('query');
            $where['%code%'] = $this->t_input->request('query');
            $where['%name%'] = $this->t_input->request('query');
            $this->where_or($where);
        }

        $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        $array = $this->limit(10)->finds();
        $json['query'] = $this->t_input->request('query');
        foreach ($array as $value) {
            $json['suggestions'][] = array('value' => $value['name'], 'code' => $value['code'], 'id' => $value['id']);
        }
        return json_encode($json);
    }

    public function pr_where($py = NULL, $where = true) {
        if ($where) {
            if ($this->t_input->request('timea'))
                $this->where(array('w.time[>=]' => strtotime($this->t_input->request('timea') . ' 00:00:00')));

            if ($this->t_input->request('timeb'))
                $this->where(array('w.time[<=]' => strtotime($this->t_input->request('timeb') . ' 23:59:59')));


            $this->where(array('ws.uid' => $this->get_uid()));




            if ($this->t_input->request('type'))
                $this->where(array('a.type' => $this->t_input->request('type')));

            if ($this->t_input->request('warehouse'))
                $this->where(array('w.w_id' => $this->t_input->request('warehouse')));


            if ($this->t_input->request('lowesta'))
                $this->where(array('w.number[>=]' => $this->t_input->request('lowesta')));

            if ($this->t_input->request('lowestb'))
                $this->where(array('w.number[<=]' => $this->t_input->request('lowestb')));


            if ($this->t_input->request('supplier'))
                $this->where(array('w.s_id' => $this->t_input->request('supplier')));
            if ($this->t_input->request('c_id'))
                $this->where(array('a.c_id' => $this->t_input->request('c_id')));
            if ($this->t_input->request('keyword')) {
                unset($where);
                if (is_numeric($this->t_input->request('keyword')))
                $where['%w.id%'] = abs($this->t_input->request('keyword'));
                $where['%a.code%'] = $this->t_input->request('keyword');
                $where['%a.name%'] = $this->t_input->request('keyword');
                $this->where_or($where);
            }

            if ($this->t_input->request('staff_uid'))
                $this->where(array('w.uid' => $this->t_input->request('staff_uid')));
        }

        $this->left_join('product as a', 'w.p_id=a.id');
        if (!empty($py) && $where) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }
        $this->left_join('product_warehouse as pw', 'pw.w_id=w.w_id');
        $this->left_join('product_warehouse_staff as ws', 'w.w_id=ws.w_id');

        $this->left_join('supplier as s', 'w.s_id=s.id');


        $this->left_join('product_category as pc', 'a.c_id=pc.id');


        $this->left_join('product_inventory as i', 'w.p_id=i.product and w.w_id=i.warehouse');


        $this->left_join('staff as b', 'a.uid=b.uid');
        $this->left_join('staff as c', 'a.update_uid=c.uid');
        $this->left_join('staff as e', 'w.uid=e.uid');
        $this->order('w.id desc');
        $this->from('product_w as w')->select('a.*,i.quantity,pc.name as category,pw.w_name,w.id as w_id,w.w_id as wid,w.number,w.return,w.library,w.time,s.id as com_id,s.company,b.realname,c.realname as replace_name,e.realname as w_members');
        return $this;
    }

    public function pro_where($py = NULL) {
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }
        if ($this->t_input->request('c_id'))
            $this->where(array('a.c_id' => $this->t_input->request('c_id')));
        if ($this->t_input->request('type'))
            $this->where(array('a.type' => $this->t_input->request('type')));

        if ($this->t_input->request('staff_uid'))
            $this->where(array('a.uid' => $this->t_input->request('staff_uid')));

        if ($this->t_input->request('keyword')) {
            unset($where);
            $where['%a.code%'] = $this->t_input->request('keyword');
            $where['%a.name%'] = $this->t_input->request('keyword');
            $this->where_or($where);
        }

        $this->left_join('product_inventory as i', 'a.id=i.product');

        $this->left_join('product_category as pc', 'a.c_id=pc.id');

        $this->left_join('staff as b', 'a.uid=b.uid');
        $this->left_join('staff as c', 'a.update_uid=c.uid');




        $this->select('a.*,pc.name as category,SUM(i.quantity) as quantity,COUNT(i.warehouse) as warehouse,b.realname,c.realname as replace_name');
        $this->order('a.id desc');
        $this->from('product as a');
        return $this;
    }

    public function sales_submit() {
        if (!$this->t_input->post('product_id') || !is_array($this->t_input->post('product_id')))
            $this->failure('至少提供一个产品');
        if ($this->t_input->post('product_id')) {
            foreach ($this->t_input->post('product_id') as $value) {
                if (is_numeric($value)) {
                    if (($var = $this->m_product->find($value))) {
                        $var['warehouse'] = $this->m_product_inventory->pr_where()->where(array('a.id' => $value, 'quantity[>]' => 0))->finds();
                        $product[] = $var;
                        unset($var);
                    }
                }
            }
        }
        if (empty($product))
            $this->failure('至少提供一个产品');
        $product_quantity = $this->t_input->post('product_quantity');
        $product_discount = $this->t_input->post('product_discount');
        $product_warehouse = $this->t_input->post('product_warehouse');
        $points = $this->t_input->post('points');
        $po = 0;
        $amount = 0;
        $cost = 0;
        $vars = array();
        $member = $this->t_input->post('member');
        foreach ($product as $key => $var) {
            if (empty($product_quantity[$key]) || !is_numeric($product_quantity[$key]))
                $this->failure('数量参数有误');
            if (!empty($product_discount[$key]) && !is_numeric($product_quantity[$key]))
                $this->failure('折扣参数有误');
            $quantity+= $var['product_quantity'] = $product_quantity[$key];
            $var['product_discount'] = $product_discount[$key];
            $var['product_warehouse'] = $product_warehouse[$key];
            if (!$this->m_product_inventory->check_product_sales($var['id'], $var['product_warehouse'], $var['product_quantity']))
                $this->failure('产品:' . $var['name'] . ' 当前库存不足,不能出库，请更换仓库');
            if (!empty($points[$key]) && $member) {
                $po += $var['product_quantity'] * $var['points'];
                $var['product_points'] = 1;
            }
            if (empty($var['product_points'])) {
                if (!empty($var['product_discount']) && $var['product_discount'] > 0 && $var['product_discount'] < 10) {
                    $amount+= sprintf("%.2f", ($var['sales'] * $var['product_quantity']) * ($var['product_discount'] / 10));
                } else {
                    $amount+= sprintf("%.2f", ($var['sales'] * $var['product_quantity']));
                    
                }
                $cost+= sprintf("%.2f", ($var['purchase'] * $var['product_quantity']));
            }
            //points
            $vars[] = $var;
        }
        if (!empty($po) && !$this->m_member->check_reduce($member, ceil($po)))
            $this->failure('积分不足无法购买');
        $orders['uid'] = $this->get_uid();
        $orders['member'] = $member;
        $orders['time'] = time();
        $orders['remark'] = $this->t_input->post('remark');
        $orders['ship'] = $this->t_input->post('ship') ? strtotime($this->t_input->request('ship')) : NULL;
        $orders['amount'] = $amount;
        $orders['cost'] = $cost;
        $orders['points'] = $po;
        $orders['number'] = date('YmdtHis') . rand(100, 999) . $this->get_uid();

        if (($insert_id = $this->m_orders->insert($orders))) {
            foreach ($vars as $key => $value) {
                $products = $this->pr_where()->where(array('a.id' => $value['id']))->find();

                $orders_data[$key]['o_id'] = $insert_id;
                $orders_data[$key]['discounts'] = $value['product_discount'];
                $orders_data[$key]['quantity'] = $value['product_quantity'];
                if (empty($value['product_points'])) {
                    if (!empty($value['product_discount']) && $value['product_discount'] > 0 && $value['product_discount'] < 10) {
                        $orders_data[$key]['amount'] = sprintf("%.2f", ($value['sales'] * $value['product_quantity']) * ($value['product_discount'] / 10));
                    } else {
                        $orders_data[$key]['amount'] = sprintf("%.2f", ($value['sales'] * $value['product_quantity']));
                    }
                    $orders_data[$key]['cost'] = sprintf("%.2f", ($value['purchase'] * $value['product_quantity']));

                    $this->m_staff_wage->add($insert_id, $products, $orders_data[$key]['amount']);


                    $orders_data[$key]['integral'] = floor($orders_data[$key]['amount'] * $this->get_base('points'));
                    if ($member && !empty($orders_data[$key]['integral']) && $this->get_base('points') && is_numeric($this->get_base('points')))
                        $this->m_member->increase($member, $orders_data[$key]['integral'], '购买产品=>' . $products['name'], $insert_id);
                }else {
                    $orders_data[$key]['integral'] = $orders_data[$key]['points'] = ceil($value['points'] * $value['product_quantity']);
                    $this->m_member->reduce($member, $orders_data[$key]['points'], '购买产品=>' . $products['name'], $insert_id);
                }
                $orders_data[$key]['product'] = $value['id'];
                $orders_data[$key]['warehouse'] = $value['product_warehouse'];
                $orders_data[$key]['product_data'] = serialize($products);
                $this->m_product_inventory->reduce($value['id'], $value['product_warehouse'], $value['product_quantity']);
            }

            $this->m_orders_data->insert_batch($orders_data);
            $this->m_operate->success('销售产品');
            $this->success('销售成功', $this->url->site('inventory/product_sales'));
        } else {
            $this->m_operate->failure('销售产品');
            $this->failure('销售失败');
        }
    }

    public function get_sales() {
        if ($this->t_input->post('code')) {
            if (($var = $this->m_product->where(array('code' => $this->t_input->post('code')))->find())) {
                $var['warehouse'] = $this->m_product_inventory->pr_where()->where(array('a.code' => $this->t_input->post('code'), 'quantity[>]' => 0))->finds();
                $product[] = $var;
                unset($var);
            }
        }
        if ($this->t_input->post('product_id')) {
            foreach ($this->t_input->post('product_id') as $value) {
                if (is_numeric($value)) {
                    if (($var = $this->m_product->find($value))) {
                        $var['warehouse'] = $this->m_product_inventory->pr_where()->where(array('a.id' => $value, 'quantity[>]' => 0))->finds();
                        $product[] = $var;
                        unset($var);
                    }
                }
            }
        }
        return $product;
    }

    public function chart($day) {
        $list = $this->finds();
        if (empty($list))
            return;

        $vars = array();

        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $datea = strtotime($date . ' 00:00:00');
            $dateb = strtotime($date . ' 23:59:59');
            $vars['number'][$index + 1] = $this->get_charttime($list, $datea, $dateb);
        }



        for ($index = 0; $index < $day; $index++) {
            $date = date('Y-m-d', strtotime($_REQUEST['timea']) + ($index * 86400));
            $vars['date'][] = "'{$date}'";
        }
        return $vars;
    }

    private function get_charttime($list, $datea, $dateb) {

        $vars = 0;
        foreach ($list as $var) {
            if ($var['time'] >= $datea && $var['time'] <= $dateb)
                $vars+= $var['number'];
        }
        return $vars;
    }

}
