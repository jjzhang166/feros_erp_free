<?php

namespace controller;

class prints extends common {

    /**
     * 订单
     */
    public function ordersAction($id) {
        $this->assign('var', $var = $this->m_orders->orders_where()->where(array('a.id' => $id))->group('a.id')->find());
        if (!empty($var)) {
            $this->m_orders->limit(1)->update(array('print' => 1), $id);
            $this->m_operate->success('打印出货单=>' . $var['number']);
        }
        $this->assign('orders', $this->m_orders_data->get_data($id));
        $this->display();
    }

    /**
     * 订单
     */
    public function orders_listAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $this->assign('list', $this->m_orders_data->orders_where()->group('d.id')->finds());

        $this->m_operate->success('打印出货单');

        $this->display();
    }

    /**
     * 退货单
     */
    public function sales_returnsAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product_return->pr_where()->group('a.id')->finds());
        $this->m_operate->success('打印退货单');
        $this->display();
    }

    /**
     * 报废单
     */
    public function scrapped_inquiryAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product_obsolescence->ors_where()->group('a.id')->finds());
        $this->m_operate->success('打印报废单');
        $this->display();
    }

    /**
     * 库存查询
     */
    public function stockAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product_inventory->pr_where()->group('w.id')->finds());
        $this->m_operate->success('打印库存查询');
        $this->display();
    }

    /**
     * 调拨查询
     */
    public function allocation_inquiryAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        $this->assign('list', $this->m_product_warehouse_allocate->pr_where()->group('a.id')->finds());
        $this->m_operate->success('打印调拨查询');
        $this->display();
    }

    /**
     * 库存报警查询
     */
    public function stock_alarmAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product_inventory->pr_where()->where('a.lowest>=w.quantity')->finds());
        $this->m_operate->success('打印库存报警');
        $this->display();
    }

    /**
     * 退回查询
     */
    public function returns_queryAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product_return_supplier->sup_where()->group('a.id')->finds());
        $this->m_operate->success('打印退回单');
        $this->display();
    }

    /**
     * 入库查询
     */
    public function warehousing_queriesAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_product->pr_where($py)->group('w.id')->finds());
        $this->m_operate->success('打印入库单');
        $this->display();
    }

    /**
     * 入库查询
     */
    public function warehousing_queries_sAction($id) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('var', $this->m_product_w_order->pr_where()->group('a.id')->where(array('a.id' => $id))->find());
        $this->m_operate->success('打印入库单');
        $this->display();
    }

    /**
     * 打印账务
     */
    public function financeAction() {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');
        $this->assign('list', $this->m_finance_accounts->accounts_where()->group('a.id')->finds());
        $this->m_operate->success('打印账务单');
        $this->display();
    }

}
