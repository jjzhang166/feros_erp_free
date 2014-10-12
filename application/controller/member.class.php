<?php

/**
 * 员工管理
 */

namespace controller;

class member extends common {
public function __construct() {
        parent::__construct();
        $this->m_authority->check();
    }
    /**
     * 会员生日
     */
    public function birthdayAction($py = NULL) {
        //if (empty($_REQUEST['birthday']))
        $where['a.birthday[>=]'] = date('Y-m-d', (time() - (86400 * $this->base['birthday'])));
        $where['a.birthday[<=]'] = date('Y-m-d');
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_member->member_where($py)->where($where)->lists($this->t_input->post('count')));
        } else {
            $this->assign('group', $this->m_member_group->get_formats());
            $this->assign('count', $this->m_member->member_where($py)->where($where)->count());
        }
        $this->assign('py', $py);
        $this->display();
    }

    /**
     * 会员管理
     */
    public function indexAction($py = NULL) {

        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_member->member_where($py)->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_member->member_where($py)->count('DISTINCT a.id'));
            $this->assign('group', $this->m_member_group->get_formats());
            $this->assign('py', $py);
        }



        $this->display();
    }

    /**
     * 删除管理
     */
    public function index_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_member->delete_submit($id);
    }

    /**
     * 查看会员
     */
    public function index_lookAction($id) {
        $type = $this->t_input->request('type');
        if (is_numeric($type))
            $where['type'] = (int) $type;
        $where['member'] = $id;
        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_member_points->from('member_points as a')->order('a.id desc')->select('a.*,s.realname')->left_join('staff as s', 'a.uid=s.uid')->where($where)->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_member_points->where($where)->count('DISTINCT id'));
            $this->assign('look', $this->m_member->member_where()->where(array('a.id' => $id))->find());
        }
        $this->display();
    }

    /**
     * 修改会员
     */
    public function index_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_member->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_member->find($id));
        }
        $this->assign('group', $this->m_member_group->get_formats());
        $this->display();
    }

    /**
     * 新增会员
     */
    public function addAction() {
        if ($this->t_verify->is_post())
            return $this->m_member->add_submit();
        $this->assign('group', $this->m_member_group->get_formats());
        $this->display();
    }

    /**
     * 会员分组
     */
    public function groupAction() {
        $this->assign('list', $this->m_member_group->get_formats());
        $this->display();
    }

    /**
     * 新增会员分组
     */
    public function group_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_member_group->add_submit();
        $this->assign('list', $this->m_member_group->get_formats());
        $this->display();
    }

    /**
     * 修改会员分组
     */
    public function group_editAction($id) {
        if ($this->t_verify->is_post())
            return $this->m_member_group->edit_submit();
        if (!empty($id) && is_numeric($id)) {
            $this->assign('edit', $this->m_member_group->find($id));
        }
        $this->assign('list', $this->m_member_group->get_formats());
        $this->display();
    }

    /**
     * 删除会员分组
     */
    public function group_deleteAction($id) {
        if (!empty($id) && is_numeric($id))
            return $this->m_member_group->delete_submit($id);
    }

    /**
     * 会员分组排序
     */
    public function group_sortAction() {
        return $this->m_member_group->sort_submit();
    }

}
