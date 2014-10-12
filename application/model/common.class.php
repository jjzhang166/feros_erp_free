<?php

namespace model;

class common extends \base\model {

    static $configbase, $base;

    public function success($message, $url = NULL) {
        $this->t_verify->is_ajax() || $this->t_input->request('feros_client') ? $this->t_header->ajax_return($message, 1, $url) : $this->display($message, 1, $url);
    }

    public function failure($message, $url = NULL) {
        $this->t_verify->is_ajax() || $this->t_input->request('feros_client') ? $this->t_header->ajax_return($message, 0, $url) : $this->display($message, 0, $url);
    }

    public function display($message, $status, $url, $time = 3) {
        $var['message'] = $message;
        $var['status'] = $status;
        $var['url'] = $url? : $this->t_client->reffer();
        $var['time'] = $time;
        $this->view->assign($var);
        $this->view->display('public/message');
        exit();
        
    }

    public function get_uid() {
        return $this->m_staff->get_uid();
    }

    public function get_base($key = null) {
        if (empty(self::$base))
            self::$base = is_file($this->get_base_file()) ? include $this->get_base_file() : array();
        return is_null($key) ? self::$base : (isset(self::$base[$key]) ? self::$base[$key] : NULL);
    }

    public function get_base_file() {
        return self::$configbase? : self::$configbase = rtrim(dirname(__DIR__), '\\/') . DS . 'config' . DS . 'base.php';
    }

    public function lists($count) {

        $page = isset($_REQUEST['pagination']) ? $_REQUEST['pagination'] : 1;

        $numPerPage = $this->get_base('queqry');

        $offset = $numPerPage * ((int) $page - 1);




        return $this->limit($numPerPage, $offset)->finds();
    }

    function gen_tree($items, $id = 'id', $pid = 'pid', $son = 's') {
        $tree = array();
        $tmpMap = array();

        foreach ($items as $item) {
            $tmpMap[$item[$id]] = $item;
        }

        foreach ($items as $item) {
            if (isset($tmpMap[$item[$pid]])) {
                $tmpMap[$item[$pid]][$son][] = &$tmpMap[$item[$id]];
            } else {
                $tree[] = &$tmpMap[$item[$id]];
            }
        }
        unset($tmpMap);
        return $tree;
    }

}
