<?php

/**
 * 仓库
 */

namespace controller;

class inventory extends common {
    public function __construct() {
        parent::__construct();
        $this->m_authority->check();
    }

    /**
     * 产品添加
     */
    public function product_addAction() {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax())
            return $this->m_product->add_submit();
        $this->assign('category', $this->m_product_category->get_formats());
        $this->assign('unit', $this->m_product_unit->order('sort desc')->finds());
        $this->display();
    }

    /**
     * 产品管理
     */
    public function productAction($py = NULL) {


        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product->pro_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product->pro_where($py)->count('DISTINCT a.id'));
            $this->assign('py', $py);
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->display();
    }

    /**
     * 报废查询
     */
    public function scrapped_inquiryAction($py = NULL) {

        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_obsolescence->ors_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_obsolescence->ors_where($py)->count('DISTINCT a.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->display();
    }

    /**
     * 提交报废
     */
    public function product_obsolescence_submitAction($id) {
        if ($this->t_verify->is_ajax() && $this->t_verify->is_post())
            return $this->m_product_obsolescence->add_submit($id);

        $this->assign('list', $this->m_product_inventory->pr_where()->where(array('w.id' => $id))->find());
        $this->assign('id', $id);
        $this->display();
    }

    /**
     * 产品报废
     */
    public function product_obsolescenceAction($py = NULL) {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_inventory->pr_where($py)->where(array('w.quantity[>]' => 0))->group('w.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_inventory->pr_where($py)->where(array('w.quantity[>]' => 0))->count('DISTINCT w.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->display();
    }

    /**
     * 退货查询
     */
    public function sales_returns_queryAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
            $this->assign('list', $this->m_product_return->pr_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_return->pr_where($py)->count('DISTINCT a.id'));

            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
            $this->assign('py', $py);
        }
        $this->display();
    }

    /**
     * 产品退货
     */
    public function return_requestAction($id) {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax() && !empty($id) && is_numeric($id)) {
            return $this->m_orders_data->return_request($id);
        }

        $this->assign('list', $this->m_orders_data->get_info($id));
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->display();
    }

    /**
     * 销售退货
     */
    public function sales_returnsAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
            $this->assign('list', $this->m_orders->orders_where($py)->where(array('a.status[>=]' => -1))->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_orders->orders_where($py)->where(array('a.status[>=]' => -1))->count('DISTINCT a.id'));
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
            $this->assign('py', $py);
        }
        $this->display();
    }

    /**
     * 产品销售查询
     */
    public function product_sales_lookAction($id) {
        $this->assign('var', $list = $this->m_orders->orders_where()->where(array('a.id' => $id))->group('a.id')->find());
        if (empty($list['id']))
            return $this->m_common->failure('查询到产品销售记录不存在');
        $this->assign('orders', $this->m_orders_data->get_data($id));
        $this->display();
    }

    /**
     * 销售查询
     */
    public function sales_records_checkAction($py = NUll) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $chart = $this->t_input->request('chart');

        if (empty($chart)) {
            if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
                $this->assign('list', $this->m_orders->orders_where($py)->group('a.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_orders->orders_where($py)->count('DISTINCT a.id'));
                $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
                $this->assign('category', $this->m_product_category->get_formats());
                $this->assign('py', $py);
            }
        } else {
            if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
                $this->assign('list', $this->m_orders_data->orders_where($py)->group('d.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_orders_data->orders_where($py)->count('DISTINCT d.id'));
                $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
                $this->assign('category', $this->m_product_category->get_formats());
                $this->assign('py', $py);
            }
        }

        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 产品销售
     */
    public function product_salesAction() {


        if ($this->t_verify->is_ajax() && $this->t_verify->is_post())
            return $this->m_product->sales_submit();
        if ($this->t_verify->is_post()) {
            if ($this->t_input->post('member')) {
                $member = $this->m_member->from('member a')->left_join('member_group as c', 'a.g_id=c.id')->where(array('a.id' => $this->t_input->post('member')))->find();
                $this->assign('discounts', $member['discounts']);
                $this->assign('info', $member);
            }
            $product = $this->m_product->get_sales();
            if (!empty($product))
                $this->assign('product', $product);
        }
        //$this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->display();
    }

    /**
     * 退回公司
     */
    public function returnAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');


        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product->pr_where($py)->where('w.number>w.return')->where('i.quantity>0')->group('w.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product->pr_where($py)->where('w.number>w.return')->where('i.quantity>0')->count('DISTINCT w.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('supplier', $this->m_supplier->sup_where()->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->display();
    }

    public function returns_queryAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_return_supplier->sup_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_return_supplier->sup_where($py)->count('DISTINCT a.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('supplier', $this->m_supplier->sup_where()->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->display();
    }

    public function return_addAction($id) {
        if ($this->t_verify->is_ajax() && $this->t_verify->is_post())
            return $this->m_product_return_supplier->add_submit($id);

        $this->assign('list', $list = $this->m_product->pr_where()->group('w.id')->where(array('w.id' => $id))->find());


        $return = $list['number'] - $list['return'];
        if ($return > 0) {
            if ($list['quantity'] > $return) {
                $return = $list['quantity'] - $return;
            } elseif ($list['quantity'] <= $return) {
                $return = $list['quantity'];
            }
        } else {
            $return = 0;
        }


        $this->assign('return', $return);
        $this->assign('id', $id);
        $this->display();
    }

    /**
     * 查看产品
     */
    public function product_lookAction($id) {
        if ($this->t_input->post('looktype')) {
            if ($this->t_input->post('looktype') === '1')
                $this->assign('inventory', $this->m_product_inventory->pr_where()->where(array('a.id' => $id))->lists($this->t_input->post('count')));
            elseif ($this->t_input->post('looktype') === '2')
                $this->assign('warehouse', $this->m_product->pr_where()->group('w.id')->where(array('a.id' => $id))->lists($this->t_input->post('count')));
            elseif ($this->t_input->post('looktype') === '3')
                $this->assign('warehouse_allocate', $this->m_product_warehouse_allocate->pr_where()->where(array('a.product' => $id))->group('a.id')->lists($this->t_input->post('count')));

            $this->assign('looktype', $this->t_input->post('looktype'));
        } else {
            $this->assign('list', $list = $this->m_product->pro_where()->group('a.id')->where(array('a.id' => $id))->find());
            if (empty($list['id']))
                return $this->m_common->failure('产品不存在');

            $this->assign('count1', $this->m_product_inventory->pr_where()->where(array('a.id' => $id))->count('DISTINCT a.id'));
            $this->assign('count2', $this->m_product->pr_where()->where(array('a.id' => $id))->count('DISTINCT a.id'));
            $this->assign('count3', $this->m_product_warehouse_allocate->pr_where()->where(array('a.product' => $id))->count('DISTINCT a.id'));
        }
        $this->display();
    }

    /**
     * 修改产品
     */
    public function product_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_product->edit_submit();
        $this->assign('list', $this->m_product->where(array('id' => $id))->find());
        $this->assign('category', $this->m_product_category->get_formats());
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('unit', $this->m_product_unit->order('sort desc')->finds());
        $this->assign('supplier', $this->m_supplier->sup_where()->finds());
        $this->display();
    }

    /**
     * 库存报警
     */
    public function stock_alarmAction() {
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('list', $this->m_product_inventory->pr_where()->where('a.lowest>=w.quantity')->finds());

        $this->display();
    }

    /**
     * 库存调拨
     */
    public function stock_transfer_addAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_product_inventory->stock_transfer($id);
        $this->assign('list', $this->m_product_inventory->pr_where()->where(array('w.id' => $id))->find());
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('id', $id);
        $this->display();
    }

    /**
     * 库存调拨
     */
    public function stock_transferAction($py = NULL) {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_inventory->pr_where($py)->where(array('w.quantity[>]' => 0))->group('w.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_inventory->pr_where($py)->where(array('w.quantity[>]' => 0))->count('DISTINCT w.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }

        $this->display();
    }

    /**
     * 调拨查询
     */
    public function allocation_inquiryAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');


        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_warehouse_allocate->pr_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_warehouse_allocate->pr_where($py)->count('DISTINCT a.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }

        $this->display();
    }

    /**
     * 库存查询
     */
    public function stockAction($py = NULL) {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_product_inventory->pr_where($py)->group('w.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_product_inventory->pr_where($py)->count('DISTINCT w.id'));
            $this->assign('py', $py);
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }

        $this->display();
    }

    /**
     * 产品入库
     */
    public function warehousingAction() {

        $this->assign('category', $this->m_product_category->get_formats());
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());

        $this->assign('supplier', $this->m_supplier->sup_where()->finds());

        if ($this->t_input->post('product')) {
            $list = $this->m_product->find($this->t_input->post('product'));
            if (!empty($list))
                $product[] = $list;
        }


        foreach ($this->t_input->post('product_id') as $value) {
            if (!empty($value) && is_numeric($value) && ($list = $this->m_product->find($value)))
                $product[] = $list;
        }

        if (!empty($product))
            $this->assign('product', $product);


        $this->display();
    }

    /**
     * 旧产品入库
     */
    public function storageAction() {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax())
            return $this->m_product->storage_add_submit();
    }

    /**
     * 入库查询
     */
    public function warehousing_queriesAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $chart = $this->t_input->request('chart');
        $this->assign('chart', $chart);
        if (empty($chart)) {
            if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                $this->assign('list', $this->m_product->pr_where($py)->group('w.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_product->pr_where($py)->count('DISTINCT w.id'));
            }
        } else {
            if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                $this->assign('list', $this->m_product_w_order->pr_where()->group('a.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_product_w_order->pr_where()->count('DISTINCT a.id'));
            }
        }
        $this->assign('py', $py);
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('supplier', $this->m_supplier->sup_where()->finds());
        $this->assign('category', $this->m_product_category->get_formats());
        $this->display();
    }

    /**
     * 供应商
     */
    public function supplierAction($py = NULL) {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_supplier->sup_where($py)->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_supplier->sup_where($py)->count());
        }
        $this->assign('py', $py);
        $this->display();
    }

    /**
     * 新增供应商
     */
    public function supplier_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_supplier->add_submit();
        $this->display();
    }

    /**
     * 修改供应商
     */
    public function supplier_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_supplier->edit_submit();
        $this->assign('var', $this->m_supplier->sup_where()->find($id));
        $this->display();
    }

    /**
     * 查看供应商
     */
    public function supplier_lookAction($id) {
        $this->assign('var', $this->m_supplier->sup_where()->find($id));
        $this->display();
    }

    /**
     * 删除供应商
     */
    public function supplier_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_supplier->delete_submit($id);
    }

}
