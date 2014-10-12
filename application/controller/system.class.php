<?php

namespace controller;

class system extends common {

    public function __construct() {
        parent::__construct();
        $this->m_authority->check();
    }

    /**
     * 数据管理
     */
    public function databaseAction() {
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $tables = array();
            foreach ($this->db->query('SHOW TABLES')->fetchAll(\PDO::FETCH_ASSOC) as $value) {
                $tables[] = $value['Tables_in_erp'];
            }
            $tables = implode(',', $tables);
            if ($this->t_input->post('type')) {
                $this->db->query("OPTIMIZE TABLE $tables");
                $this->m_common->success('优化成功', $this->url->site('system/database'));
            } else {
                $this->db->query("REPAIR TABLE $tables");
                $this->m_common->success('修复成功', $this->url->site('system/database'));
            }
        }

        $this->display();
    }

    /**
     * 部门管理
     */
    public function database_cacheAction() {
        $this->display();
    }

    /**
     * 部门管理
     */
    public function departmentAction() {
        $this->assign('list', $this->m_staff_category->get_formats(true));
        $this->display();
    }

    /**
     * 修改部门管理
     */
    public function department_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_staff_category->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_staff_category->find($id));
        }
        $this->assign('list', $this->m_staff_category->get_formats());
        $this->display();
    }

    /**
     * 删除部门
     */
    public function department_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_staff_category->delete_submit($id);
    }

    /**
     * 部门排序
     */
    public function department_sortAction() {
        return $this->m_staff_category->sort_submit();
    }

    /**
     * 新增部门
     */
    public function department_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_staff_category->add_submit();
        $this->assign('list', $this->m_staff_category->get_formats());
        $this->display();
    }

    /**
     * 员工管理
     */
    public function staffAction($py = null) {
        if (!isset($_REQUEST['status']))
            $_REQUEST['status'] = 1;
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_staff->staff_where($py)->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_staff->staff_where($py)->count('DISTINCT a.uid'));
            $this->assign('category', $this->m_staff_category->get_formats());
            $this->assign('py', $py);
        }



        $this->display();
    }

    /**
     * 新增员工
     */
    public function staff_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_staff->add_submit();
        $this->assign('menu', $this->m_menu->get_find_tree());
        $this->assign('list', $this->m_staff_category->get_formats());
        $this->display();
    }

    /**
     * 修改员工
     */
    public function staff_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_staff->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_staff->find($id));
        }
        $staff_menu = $this->m_staff_menu->where(array('uid' =>$id))->finds();
        foreach ($staff_menu as $value) {
            $menu[] = $value['m_id'];
        }

        $this->assign('staff_menu', $menu);
        $this->assign('menu', $this->m_menu->get_find_tree());
        $this->assign('list', $this->m_staff_category->get_formats());
        $this->display();
    }

    /**
     * 查看员工
     */
    public function staff_lookAction($id) {
        if (!empty($id) && is_numeric($id))
            $this->assign('look', $this->m_staff->staff_where()->find($id));

        $staff_menu = $this->m_staff_menu->where(array('uid' => $id))->finds();
        foreach ($staff_menu as $value) {
            $menu[] = $value['m_id'];
        }

        $this->assign('staff_menu', $menu);
        $this->assign('menu', $this->m_menu->get_find_tree());
        $this->display();
    }

    /**
     * 删除员工
     */
    public function staff_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_staff->delete_submit($id);
    }

    /**
     * 操作日志
     */
    public function log_operateAction($py = null) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_operate->logwhere($py)->group('a.id')->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_operate->logwhere($py)->count('DISTINCT a.id'));
            $this->assign('py', $py);
        }

        $this->display();
    }

    /**
     * 基础设置
     */
    public function baseAction() {
        if ($this->t_verify->is_post()) {
            file_put_contents($this->m_common->get_base_file(), "<?php\r\nreturn " . var_export($_POST, true) . ";");
            $this->m_operate->success('基础设置');
            return $this->m_common->success('保存成功', $this->url->site('system/base'));
        }
        $this->assign('var', $this->base);
        $this->display();
    }

    /**
     * 菜单列表
     */
    public function meunAction() {
        $this->assign('list', $this->m_menu->get_formats());
        $this->display();
    }

    /**
     * 新增菜单
     */
    public function meun_addAction($m_id = NULL, $parentid = NULL) {
        if ($this->t_verify->is_post())
            return $this->m_menu->add_submit();

        $this->assign('m_id', $m_id);
        $this->assign('parentid', $parentid);
        $this->display();
    }

    /**
     * 删除菜单
     */
    public function menu_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_menu->menu_delete($id);
    }

    /**
     * 修改菜单
     */
    public function menu_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_menu->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('var', $var = $this->m_menu->find($id));
        }

        $this->display();
    }

    /**
     * 菜单排序
     */
    public function menu_sortAction() {
        if ($this->t_verify->is_post())
            return $this->m_menu->menu_submit();
    }

    /**
     * 菜单排序
     */
    public function ferosAction() {
        $this->t_header->redirect(base64_decode('aHR0cDovL2Zlcm9zLmNvbS5jbi8='));
    }

    /**
     * 单位管理
     */
    public function unitAction() {
        $this->assign('list', $this->m_product_unit->order('sort desc')->finds());
        $this->display();
    }

    /**
     * 单位管理排序
     */
    public function unit_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_product_unit->add_submit();
        $this->display();
    }

    /**
     * 单位管理排序
     */
    public function unit_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_product_unit->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $var = $this->m_product_unit->find($id));
        }

        $this->display();
    }

    /**
     * 删除单位
     */
    public function unit_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_product_unit->delete_submit($id);
    }

    /**
     * 单位管理排序
     */
    public function unit_sortAction() {
        return $this->m_product_unit->sort_submit();
    }

    /**
     * 产品分类
     */
    public function product_categoriesAction() {
        $this->assign('list', $this->m_product_category->get_formats());
        $this->display();
    }

    /**
     * 产品排序
     */
    public function product_categories_sortAction() {
        return $this->m_product_category->sort_submit();
    }

    /**
     * 新增产品分类
     */
    public function product_categories_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_product_category->add_submit();
        $this->assign('list', $this->m_product_category->get_formats());
        $this->display();
    }

    /**
     * 删除产品分类
     */
    public function product_categories_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_product_category->delete_submit($id);
    }

    /**
     * 产品分类修改
     */
    public function product_categories_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_product_category->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_product_category->find($id));
        }
        $this->assign('list', $this->m_product_category->get_formats());
        $this->display();
    }

    /**
     * 仓库管理
     */
    public function warehouseAction() {
        $this->assign('list', $this->m_product_warehouse->warehouse_where(true)->finds());
        $this->display();
    }

    /**
     * 仓库排序
     */
    public function warehouse_sortAction() {
        return $this->m_product_warehouse->sort_submit();
    }

    /**
     * 新增仓库
     */
    public function warehouse_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_product_warehouse->add_submit();
        $this->display();
    }

    /**
     * 仓库修改
     */
    public function warehouse_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_product_warehouse->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_product_warehouse->warehouse_where(true)->where(array('a.w_id' => $id))->find());
        }

        $this->display();
    }

    /**
     * 删除仓库
     */
    public function warehouse_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_product_warehouse->delete_submit($id);
    }

}
