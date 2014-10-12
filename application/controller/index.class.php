<?php

namespace controller;

class index extends common {

    public function __construct() {
        parent::__construct();
        $this->config->view->trace = false;
    }

    public function indexAction() {
        $menu=$this->m_menu->get_bui();
        $this->assign('menu', $this->m_menu->where(array('id'=>$menu[0]))->where(array('status' => 1))->order('sort desc')->finds());
        $this->assign('bui',json_encode($menu[1]));
        $this->display();
    }

    public function quitAction($lock = NULL) {
        $this->m_staff->quit($lock);
    }

    public function remindAction() {
        $this->m_remind->get_remind();
    }

}
