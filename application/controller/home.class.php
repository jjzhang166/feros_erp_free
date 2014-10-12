<?php

namespace controller;

class home extends common {

    public function homeAction() {

        $this->display();
    }

    /**
     * 事件
     */
    public function calendarAction() {
        $this->display();
    }

    /**
     * 查询事件
     */
    public function calendar_jsonAction() {
        echo $this->m_calendar->get_json();
    }

    /**
     * 托运事件
     */
    public function calendar_dragAction() {
        echo $this->m_calendar->drag_submit();
    }

    /**
     * 修改事件
     */
    public function calendar_editAction() {
        if ($this->t_verify->is_post())
            return $this->m_calendar->edit_submit();
        $this->assign('list', $this->m_calendar->where(array('id' => $_REQUEST['id'], 'uid' => $this->uid))->find());
        $this->display();
    }

    /**
     * 新增事件
     */
    public function calendar_addAction() {
        if ($this->t_verify->is_post())
            return $this->m_calendar->add_submit();
        $this->display();
    }

    public function memoAction() {

        $this->display();
    }

    public function logAction($py = NULL) {
        if (empty($_REQUEST['timea']))
            $_REQUEST['timea'] = date('Y-m-d', time() - $this->get_day());
        if (empty($_REQUEST['timeb']))
            $_REQUEST['timeb'] = date('Y-m-d');

        if ($this->t_verify->is_post() && $this->t_verify->is_ajax()) {
            $this->assign('list', $this->m_operate->logwhere($py, $this->uid)->lists($this->t_input->post('count')));
        } else {
            $this->assign('count', $this->m_operate->logwhere($py, $this->uid)->count());
            $this->assign('py', $py);
        }

        $this->display();
    }

    public function passwordAction() {
        if ($this->t_verify->is_post())
            return $this->m_staff->change_password();
        $this->display();
    }

    public function password_gestureAction() {
        if ($this->t_verify->is_post())
            return $this->m_staff->password_gesture();
        $this->display();
    }

}
