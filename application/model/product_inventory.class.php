<?php

namespace model;

/**
 * 库存管理
 * @author feisanliang
 */
class product_inventory extends common {

    public $table_name = 'product_inventory';

    /**
     * 增加库存
     * @param int $product 产品
     * @param int $warehouse 仓库
     * @param int $quantity 数量
     */
    public function increase($product, $warehouse, $quantity) {
        $where['product'] = $product;
        $where['warehouse'] = $warehouse;
        if ($this->where($where)->find()) {
            $data['quantity[+]'] = $quantity;
            return $this->limit(1)->where($where)->update($data);
        } else {
            return $this->add($product, $warehouse, $quantity);
        }
    }

    /**
     * 减少库存
     * @param int $product 产品
     * @param int $warehouse 仓库
     * @param int $quantity 数量
     */
    public function reduce($product, $warehouse, $quantity) {
        $where['product'] = $product;
        $where['warehouse'] = $warehouse;
        $data['quantity[-]'] = $quantity;
        return $this->limit(1)->where($where)->update($data);
    }

    /**
     * 新增库存
     * @param int $product 产品
     * @param int $warehouse 仓库
     * @param int $quantity 数量
     */
    public function add($product, $warehouse, $quantity) {
        $data['product'] = $product;
        $data['warehouse'] = $warehouse;
        $data['quantity'] = $quantity;
        return $this->insert($data);
    }

    public function pr_where($py = NULL) {
        $this->where(array('ws.uid' => $this->get_uid()));

        if ($this->t_input->request('type'))
            $this->where(array('a.type' => $this->t_input->request('type')));

        if ($this->t_input->request('warehouse'))
            $this->where(array('w.warehouse' => $this->t_input->request('warehouse')));


        if ($this->t_input->request('lowesta'))
            $this->where(array('w.quantity[>=]' => $this->t_input->request('lowesta')));

        if ($this->t_input->request('lowestb'))
            $this->where(array('w.quantity[<=]' => $this->t_input->request('lowestb')));


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

        $this->left_join('product as a', 'w.product=a.id');
        if (!empty($py)) {
            $this->where(array('p.py' => $py));
            $this->left_join('pinyin as p', 'CONV(HEX(LEFT(CONVERT(a.name USING GBK),1)),16,10) BETWEEN p.begin AND p.end');
        }
        $this->left_join('product_warehouse as pw', 'pw.w_id=w.warehouse');

        $this->left_join('product_warehouse_staff as ws', 'w.warehouse=ws.w_id');

        $this->left_join('product_category as pc', 'a.c_id=pc.id');

        $this->left_join('staff as b', 'a.uid=b.uid');
        $this->left_join('staff as c', 'a.update_uid=c.uid');
        $this->order('w.id desc');
        $this->from('product_inventory as w')->select('a.*,pc.name as category,pw.w_id,pw.w_name,pw.w_default,w.id as inventory_id,w.product,w.warehouse,w.quantity,b.realname,c.realname as replace_name');
        return $this;
    }

    /**
     * 检查是否可以销售产器
     */
    public function check_product_sales($product, $warehouse, $number) {
        $where['product'] = $product;
        $where['warehouse'] = $warehouse;
        $list = $this->where($where)->find();
        if (!empty($list) && $list['quantity'] >= $number) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * 库存调拨
     * @param int $id
     */
    public function stock_transfer($id) {
        $warehouse = $this->t_input->post('warehouse');
        $number = $this->t_input->post('number');
        if (empty($warehouse) || !is_numeric($warehouse))
            $this->failure('请选择拨出仓库');
        if (empty($number) || !is_numeric($number))
            $this->failure('请输入拨出数目');
        $list = $this->where(array('id' => $id))->find();

        if (empty($list) || $list['quantity'] < $number) {
            $this->failure('无法调拨当前仓库');
        }
        if ($this->m_product_warehouse_allocate->add($warehouse, $list['warehouse'], $list['product'], $number)) {
            $this->increase($list['product'], $warehouse, $number);
            $this->reduce($list['product'], $list['warehouse'], $number);
            $this->success('调拨成功', $this->t_input->post('url'));
        } else {
            $this->failure('调拨失败');
        }

//        $list['inventory_id'];
//        $list['product'];
//        $list['warehouse'];
    }

}
