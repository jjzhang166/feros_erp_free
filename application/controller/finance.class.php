<?php

/**
 * 财务管理
 */

namespace controller;

class finance extends common {

    public function __construct() {
        parent::__construct();
        $this->m_authority->check();
    }

    /**
     * 银行管理
     */
    public function bankAction($py = NULL) {
        $this->assign('py', $py);
        $this->assign('list', $this->m_finance_bank->bandk_where($py)->finds());
        $this->display();
    }

    /**
     * 新增银行
     */
    public function bank_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_finance_bank->bank_add_submit();
        $this->display();
    }

    /**
     * 删除银行
     */
    public function bank_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_finance_bank->bank_delete($id);
    }

    /**
     * 修改银行
     */
    public function bank_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_finance_bank->bank_edit_submit();
        if (!empty($id) && is_numeric($id))
            $this->assign('var', $this->m_finance_bank->find($id));
        $this->display();
    }

    /**
     * 银行排序
     */
    public function bank_sortAction() {
        if ($this->t_verify->is_post())
            return $this->m_finance_bank->bank_sort_submit();
    }

    /**
     * 财务分类
     */
    public function categoryAction() {
        $this->assign('list', $this->m_finance_category->get_formats());
        $this->display();
    }

    /**
     * 新增财务分类
     */
    public function category_addAction($type = 0) {
        if ($this->t_verify->is_post())
            return $this->m_finance_category->category_add_submit();
        $this->assign('list', $this->m_finance_category->where(array('type' => $type))->get_formats());
        $this->assign('type', $type);
        $this->display();
    }

    /**
     * 银行分类排序
     */
    public function category_sortAction() {
        if ($this->t_verify->is_post())
            return $this->m_finance_category->category_sort_submit();
    }

    /**
     * 删除银行分类
     */
    public function category_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_finance_category->category_delete($id);
    }

    /**
     * 修改银行分类
     */
    public function category_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_finance_category->category_edit_submit();
        if (!empty($id) && is_numeric($id))
            $this->assign('var', $var = $this->m_finance_category->find($id));
        $this->assign('list', $this->m_finance_category->where(array('type' => $var['type'], 'id[!]' => $id))->get_formats());
        $this->display();
    }

    /**
     * 新增财务
     */
    public function addAction() {
        if ($this->t_verify->is_post())
            return $this->m_finance_accounts->add_submit();
        $this->assign('bank', $this->m_finance_bank->bandk_where()->finds());
        $this->assign('staff', $this->m_staff->staff_where()->where(array('status[>]' => 0))->finds());
        $this->display();
    }

    /**
     * 账务查询
     */
    public function queryAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - (86400 * $this->t_date->get_monthinday(date('m'), date('Y'))));
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_finance_accounts->accounts_where($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_finance_accounts->accounts_where($py)->count('DISTINCT a.id'));
            $this->assign('revenue', $this->m_finance_accounts->accounts_where($py)->where(array('a.type'=>1))->sum('a.money'));
            $this->assign('expenditure', $this->m_finance_accounts->accounts_where($py)->where(array('a.type'=>0))->sum('a.money'));
            $this->assign('py', $py);

            $this->assign('staffs', $this->m_staff->finds());
        }

        $this->display();
    }

    /**
     * 撤销账单
     */
    public function query_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_finance_accounts->query_delete($id);
    }

}
