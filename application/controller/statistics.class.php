<?php

namespace controller;

class statistics extends common {
public function __construct() {
        parent::__construct();
        $this->m_authority->check();
    }
    /**
     * 账务统计
     */
    public function financeAction() {
        $chart = $this->t_input->request('chart');

        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $_REQUEST['status'] = !isset($_REQUEST['status']) ? 1 : $_REQUEST['status'];

        if (empty($chart)) {
            $this->assign('list', $this->m_finance_accounts->accounts_where()->group('a.id')->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        }
        $this->assign('expenditure', $this->m_finance_accounts->accounts_where()->where(array('a.type' => 0))->sum('a.money'));
        $this->assign('revenue', $this->m_finance_accounts->accounts_where()->where(array('a.type' => 1))->sum('a.money'));

        $this->assign('chart', $chart);
        $this->assign('staffs', $this->m_staff->finds());

        $this->display();
    }

    /**
     * 工资统计
     */
    public function wageAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        if (!isset($_REQUEST['staff_uid']))
            $_REQUEST['staff_uid'] = $this->uid;

        $chart = $this->t_input->request('chart');

        if (empty($chart)) {
            $this->assign('list', $this->m_staff_wage->wage_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            $this->assign('list', $this->m_staff_wage->wage_where()->finds());
        }

        $this->assign('staffs', $this->m_staff->get_info_key($_REQUEST['staff_uid']));

        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 入库统计
     */
    public function purchaseAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $chart = $this->t_input->request('chart');

        if (empty($chart)) {
            $this->assign('list', $this->m_product->pr_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            if ($chart === '1') {
                if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                    $this->assign('list', $this->m_product->pr_where()->group('w.id')->lists($this->t_input->post('count')));
                } else {
                    $this->assign('count', $this->m_product->pr_where()->count('DISTINCT w.id'));
                }
            } elseif ($chart === '2') {
                if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                    $this->assign('list', $this->m_product_w_order->pr_where()->group('a.id')->lists($this->t_input->post('count')));
                } else {
                    $this->assign('count', $this->m_product_w_order->pr_where()->count('DISTINCT a.id'));
                }
            }
            $this->assign('sum', $this->m_product->pr_where()->sum('number'));
        }
        if (!$this->t_verify->is_ajax()) {
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('supplier', $this->m_supplier->sup_where()->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 销售统计
     */
    public function salesAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $chart = $this->t_input->request('chart');
        if (!$this->t_input->request('mode'))
            $where['a.points'] = 0;
        else
            $where['a.amount'] = 0;

        if (empty($chart)) {
            $this->assign('list', $this->m_orders->orders_where()->where($where)->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            if ($chart === '1') {
                if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
                    $this->assign('list', $this->m_orders->orders_where()->where($where)->group('a.id')->lists($this->t_input->post('count')));
                } else {
                    $this->assign('count', $this->m_orders->orders_where()->where($where)->count('DISTINCT a.id'));
                    $this->assign('list', $this->m_orders->orders_where()->where($where)->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
                }
            } elseif ($chart === '2') {
                if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
                    $this->assign('list', $this->m_orders_data->orders_where($py)->group('d.id')->lists($this->t_input->post('count')));
                } else {
                    $this->assign('count', $this->m_orders_data->orders_where($py)->count('DISTINCT d.id'));
                    $this->assign('list', $this->m_orders->orders_where()->where($where)->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
                }
            }
        }


        if (!$this->t_verify->is_ajax()) {
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 公司退货
     */
    public function company_returnsAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $chart = $this->t_input->request('chart');
        if (empty($chart)) {
            $this->assign('list', $this->m_product_return_supplier->sup_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                $this->assign('list', $this->m_product_return_supplier->sup_where()->group('a.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_product_return_supplier->sup_where()->count('DISTINCT a.id'));
                $this->assign('list', $this->m_product_return_supplier->sup_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
            }
        }
        if (!$this->t_verify->is_ajax()) {
            $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
            $this->assign('supplier', $this->m_supplier->sup_where()->finds());
            $this->assign('category', $this->m_product_category->get_formats());
        }
        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 销售退货
     */
    public function sales_returnsAction() {
        $chart = $this->t_input->request('chart');
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        if (empty($chart)) {
            $this->assign('list', $this->m_product_return->pr_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            if ($this->t_verify->is_ajax() && $this->t_verify->is_post()) {
                $this->assign('list', $this->m_product_return->pr_where()->group('a.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_product_return->pr_where()->count('DISTINCT a.id'));
                $this->assign('list', $this->m_product_return->pr_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
            }
        }
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('category', $this->m_product_category->get_formats());
        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 报废统计
     */
    public function scrappedAction() {
        $chart = $this->t_input->request('chart');
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        if (empty($chart)) {
            $this->assign('list', $this->m_product_obsolescence->ors_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        } else {
            if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
                $this->assign('list', $this->m_product_obsolescence->ors_where()->group('a.id')->lists($this->t_input->post('count')));
            } else {
                $this->assign('count', $this->m_product_obsolescence->ors_where()->count('DISTINCT a.id'));
                $this->assign('sum', $this->m_product_obsolescence->ors_where()->sum('a.quantity'));
            }
        }

        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        $this->assign('category', $this->m_product_category->get_formats());
        $this->assign('chart', $chart);
        $this->display();
    }

    /**
     * 仓库统计
     */
    public function warehouseAction() {
        $chart = $this->t_input->request('chart');
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        //进货
        $this->assign('purchase', $this->m_product->pr_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        //销售
        $where['a.points'] = 0;
        $this->assign('sales', $this->m_orders->orders_where()->where($where)->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        //报废
        $this->assign('scrapped', $this->m_product_obsolescence->ors_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        //公司退货
        $this->assign('company_returns', $this->m_product_return_supplier->sup_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        //销售退货
        $this->assign('sales_returns', $this->m_product_return->pr_where()->chart(($this->t_date->date_diff('d', $_REQUEST['timea'], $_REQUEST['timeb']) + 1)));
        
        $this->assign('warehouse', $this->m_product_warehouse->warehouse_where()->where(array('b.uid' => $this->uid))->finds());
        
        if ($this->t_input->request('warehouse'))
            $this->db->where(array('a.w_id' => $this->t_input->request('warehouse')));
       $this->assign('quantity',$this->db->from('product_warehouse as a')->where(array('b.uid' => $this->uid))->left_join('product_warehouse_staff as b', 'a.w_id = b.w_id')->left_join('product_inventory as w', 'w.warehouse = a.w_id')->select('SUM(DISTINCT w.quantity)')->findcolumn()?:0);
        
       
        
        $this->assign('chart', $chart);
        $this->display();
    }

}
