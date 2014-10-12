<?php

/**
 * 权限管理
 */

namespace model;

class authority extends common {

    static $staff_menu;

    public function menu() {
        return self::$staff_menu? : self::$staff_menu =$this->from('staff_menu as a')->left_join('menu as m', 'm.id=a.m_id')->where(array('a.uid' => $this->get_uid(), 'm.status' => 1))->order('m.sort desc')->select('m.*')->finds();
    }

    public function check() {
        if($this->get_uid()==='1')
            return true;
        $controller = $this->router->fetch_controller();
        $action = $this->router->fetch_method();
        foreach ($this->menu() as $value) {
            if ($controller === $value['controller'] && $action === $value['action'])
                return true;
        }
        $this->failure('权限不足');
    }

}
