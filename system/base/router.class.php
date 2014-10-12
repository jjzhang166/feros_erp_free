<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace base;

/**
 * 路由器
 * @author sanliang
 */
class router extends common {

    private $controller, $method, $request_url = array();

    public function init() {
        $url = trim($this->get_path_info(), '\\/');
        //考虑浏览器加载favicon.ico
        if ($url === 'favicon.ico') {
            header('Content-Type:' . $this->t_mime->ico);
            exit(base64_decode($this->t_favicon->ico()));
        }
        if (!is_file($this->config->run_file()))
            $this->runtime->config();

       

        if ($this->config->route->url_suffix)
            $url = preg_replace('/\.(' . trim($this->config->route->url_suffix, '.') . ')$/i', '', $url);

        $url = explode('/', $url);

        array_filter($url);
        $this->set_controller(array_shift($url));
        $this->set_method(array_shift($url));
        $this->set_request($url);
        $this->normalize();
        if ($this->config->session->start)
            $this->session->init();


        $this->exec();
    }

    private function exec() {
        loader::init(('c_' . $this->fetch_controller()), ($this->fetch_method() . 'Action'), ((array) $this->fetch_request())) !== false? : $this->view->set_template_path(rtrim(FEROS_PATH, '\\/') . DS . 'view')->display('404');
    }

    /**
     *  设置 class
     * @access	public
     * @param	string
     * @return	void
     */
    public function set_request($request) {
        $this->request_url = $request;
    }

    public function fetch_request() {
        return $this->request_url;
    }

    /**
     *  设置 class
     * @access	public
     * @param	string
     * @return	void
     */
    public function set_controller($controller = NULL) {
        $this->controller = preg_match('/^[A-Za-z](\w)*$/', $controller) ? $controller : $this->config->route->controller;
    }

    /**
     * 获取class
     * @access	public
     * @return	string
     */
    public function fetch_controller() {
        return $this->controller;
    }

    /**
     * 设置 method
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function set_method($method = NULL) {
        $this->method = preg_match('/^[A-Za-z](\w)*$/', $method) ? $method : $this->config->route->method;
    }

    /**
     * 获取 method
     * @access	public
     * @return	string
     */
    public function fetch_method() {
        return $this->method;
    }

    public function normalize() {
        if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
            if (isset($_GET))
                $_GET = $this->stripslashes($_GET);
            if (isset($_POST))
                $_POST = $this->stripslashes($_POST);
            if (isset($_REQUEST))
                $_REQUEST = $this->stripslashes($_REQUEST);
            if (isset($_COOKIE))
                $_COOKIE = $this->stripslashes($_COOKIE);
        }
    }

    /**
     * 对变量进行反转义到原始数据
     * @param string|array $param 需要反转义的原始数据
     * @return string|array
     */
    private function stripslashes(&$data) {
        return is_array($data) ? array_map(array($this, 'stripslashes'), $data) : stripslashes($data);
    }

    public function get_script_path() {
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $base_script = $_SERVER['SCRIPT_NAME'];
        } elseif (basename($_SERVER['PHP_SELF']) === $filename) {
            $base_script = $_SERVER['PHP_SELF'];
        } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $base_script = $_SERVER['ORIG_SCRIPT_NAME'];
        } else {
            $path = $_SERVER['PHP_SELF'];
            $segs = explode('/', trim($_SERVER['SCRIPT_FILENAME'], '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $base_script = '';
            do {
                $seg = $segs[$index];
                $base_script = '/' . $seg . $base_script;
                ++$index;
            } while (($last > $index) && (false !== ($pos = strpos($path, $base_script))) && (0 != $pos));
        }
        return $base_script;
    }

    public function get_path_info() {
        static $path_info = null;
        if (empty($path_info)) {
            if (empty($_SERVER['PATH_INFO'])) {
                $strlen = strlen($this->get_script_path());
                $totallen = strlen($_SERVER['PHP_SELF']);
                $path_info = substr($_SERVER['PHP_SELF'], $strlen, $totallen);
            } else {
                $path_info = $_SERVER['PATH_INFO'];
            }
        }
        return $path_info;
    }

}
