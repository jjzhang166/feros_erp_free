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
 * controller
 * @author sanliang
 */
class controller extends common {

    public function __set($var, $value) {
        $this->assign($var, $value);
    }

    /**
     * 注入变量
     * @access public
     * @param string|NULL $var 变量名称
     * @param * $value 值
     */
    public function assign($var, $value = NULL) {
        $this->view->assign($var, $value);
    }

    /**
     * 模板显示 调用内置的模板引擎显示方法，
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @param int|boog $cachetime 缓存|缓存时间
     * @return void
     */
    public function display($template = NULL, $cacheid = NULL, $cachetime = NULL) {
        $this->view->display($template, $cacheid, $cachetime);
    }

    /**
     *  获取输出页面内容
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param int|bool $cachetime 缓存|缓存时间
     * @return string
     */
    public function fetch($template = NULL, $cacheid = NULL, $cachetime = NULL) {
        return $this->view->fetch($template, $cacheid, $cachetime);
    }

    /**
     * 检测缓存是否存在
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return boolean
     */
    public function is_cached($template, $cacheid = NULL) {
        return $this->view->is_cached($template, $cacheid);
    }

}
