<?php

/**
 * FerOS PHP template engine
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace base;

/**
 * 模板引擎
 * @author sanliang
 */
class view extends common {

    const VERSION = '2.0.1';
    const ENCODING = 'UTF-8';
    const NAME = 'FEROS™ PHP template engine';
    const DS = DIRECTORY_SEPARATOR;
    const DIR = __DIR__;
    const CACHE_SUFFIX = '.cache.php';
    const COMPILE_SUFFIX = '.compile.php';

    //模板样式
    public $theme;
    //模板变量
    public $_vars = array();
    public static $cache_file = array(), $runtime, $runtime_start, $runtime_end, $_content;

    public function __set($var, $value) {
        $this->assign($var, $value);
    }

    /**
     * 增加模板目录
     * @param string $dir
     * @return \feros\view
     */
    public function set_template_path($dir) {
        $this->config->view('template_path', array_merge((array) $this->config->view->template_path, (array) $dir));
        return $this;
    }

    /**
     * 返回模板目录
     * @return array
     */
    public function get_template_path() {
        return (array) $this->config->view->template_path;
    }

    /**
     * 返回缓存文件
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return string
     */
    public function get_cache_file($template, $cacheid = NULL) {
        return rtrim($this->config->view->cache_dir, '\\//') . DS . $this->resolve_file($template, $cacheid) . self::CACHE_SUFFIX;
    }

    /**
     * 返回编译文件
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return string
     */
    public function get_compile_file($template, $cacheid = NULL) {
        return rtrim($this->config->view->compile_dir, '\\//') . DS . $this->resolve_file($template, $cacheid) . self::COMPILE_SUFFIX;
    }

    /**
     * 返回执行时间
     * @return int
     */
    public function get_runtime($dec = 4) {
        return self::$runtime = number_format((self::$runtime_end - self::$runtime_start), $dec);
    }

    private function get_microtime() {
        return microtime(TRUE);
    }

    /**
     * 设置当前输出的模板主题
     * @access public
     * @param  mixed $theme 主题名称
     * @return View
     */
    public function theme($theme) {
        $this->theme = $theme . DS;
        return $this;
    }

    /**
     * 注入变量
     * @access public
     * @param string|NULL $var 变量名称
     * @param * $value 值
     */
    public function assign($var, $value = NULL) {
        is_array($var) ? ($this->_vars = array_merge($this->_vars, $var)) : $this->_vars[$var] = $value;
        return $this;
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
        //header('Content-Type:' . ($type? : $this->config->view->template_type) . ';charset=' . ($charset? : $this->config->view->template_charset));

        return self::$_content.=$this->fetch($template, $cacheid, $cachetime);
    }

    /**
     *  获取输出页面内容
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param int|bool $cachetime 缓存|缓存时间
     * @return string
     */
    public function fetch($template = NULL, $cacheid = NULL, $cachetime = NULL) {
        if (empty($template))
            $template = $this->router->fetch_controller() . DS . $this->router->fetch_method();
        self::$runtime_start = self::$runtime_start? : $this->get_microtime();
        if ($cachetime) {
            if ($this->is_cached($template, $cacheid)) {
                self::$runtime_end = $this->get_microtime();
                return $this->t_file->file_get_contents($this->get_cache_file($this->get_template_file($template), $cacheid));
            }
        }

        ob_start();
        ob_implicit_flush(0);
        extract($this->_vars, \EXTR_OVERWRITE);
        $this->get_template_file($template);
        include $this->compile($template, $cacheid);
        self::$runtime_end = $this->get_microtime();
        $content = ob_get_clean();
        if ($cachetime) {
            $cache_file = $this->get_cache_file($template, $cacheid);
            $this->t_dir->create(dirname($cache_file));
            $this->t_file->file_put_contents($cache_file, $content);
            $this->t_file->deny($this->config->view->cache_dir);
            if ((int) $cachetime > 0)
                $cachetime = $cachetime + time();
            touch($cache_file, $cachetime? : time());
        }

        return $content;
    }

    /**
     * Gzip数据压缩传输 如果客户端支持
     * @param string $content
     * @return string
     */
    public function ob_gzip(&$content) {
        if (!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) {
            $content = gzencode($content, 9);
            header('Content-Encoding:gzip');
            header('Vary:Accept-Encoding');
            header('Content-Length:' . strlen($content));
        }
        return $content;
    }

    /**
     * 检测缓存是否存在
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheid 缓存ID
     * @return boolean
     */
    public function is_cached($template, $cacheid = NULL) {
        static $cached = array();
        $temp = md5($template . $cacheid);
        if (isset($cached[$temp]))
            return $cached[$temp];
        $this->get_template_file($template);
        $cache_file = $this->get_cache_file($template, $cacheid);
        if (!file_exists($cache_file)) {
            $cached[$temp] = false;
            return false;
        }if ($this->config->view->cache_lifetime == -1) {
            $cached[$temp] = true;
            return true;
        } elseif (filemtime($cache_file) + $this->config->view->cache_lifetime < time()) {
            $cached[$temp] = false;
            return false;
        } else {
            $savet = filemtime($template);
            $fromt = filemtime($cache_file);
            if ($savet > $fromt) {
                $cached[$temp] = false;
                return false;
            }
            $cached[$temp] = true;
            return true;
        }
    }

    /**
     * 编译
     * @param string $template
     */
    private function compile($template, $cacheid = NULL) {
        $compile = $this->get_compile_file($template, $cacheid);
        if (file_exists($compile)) {
            $savet = filemtime($template);
            $fromt = filemtime($compile);
            if ($savet <= $fromt) {
                return $compile;
            }
        }
        $content = $this->t_file->file_get_contents($template);
        new compile($this, $content, $template);
        $this->t_dir->create(dirname($compile));
        if ($this->config->view->strip_space) {
            $content = preg_replace(array('~>\s+<~', '~>(\s+\n|\r)~'), array('><', '>'), $content);
            $content = str_replace('?><?php', '', $content);
        }
        $this->t_file->file_put_contents($compile, $content);
        $this->t_file->deny($this->config->view->compile_dir);
        return !empty($compile) ? $compile : NULL;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheId 缓存ID
     * @return boolean
     */
    public function delete_cached($template, $cacheId = null) {
        $cache = $this->get_cache_file($template, $cacheId);
        return file_exists($cache) ? unlink($cache) : false;
    }

    /**
     * 删除编译
     * @access public
     * @param string $template 指定要调用的模板文件
     * @param string $cacheId 缓存ID
     * @return boolean
     */
    public function delete_compile($template, $cacheId = null) {
        $compile = $this->get_compile_file($template, $cacheId);
        return file_exists($compile) ? unlink($compile) : false;
    }

    /**
     * 清空缓存
     * @access public
     * @return boolean
     */
    public function flush_cached() {
        return $this->t_dir->delete($this->config->view->cache_dir);
    }

    /**
     * 清空编译
     * @access public
     * @return boolean
     */
    public function flush_compile() {
        return $this->t_dir->delete($this->config->view->compile_dir);
    }

    /**
     * 解释引擎文件
     * @access public
     * @param string $template
     * @param string $cacheid
     * @return string
     */
    public function resolve_file($template, $cacheid = NULL) {
        static $resolve = array();
        $template = md5($template . $cacheid);
        if (isset($resolve[$template]))
            return $resolve[$template];
        if ($this->config->view->use_sub_dirs) {
            $dir = '';
            for ($i = 0; $i < 6; $i++)
                $dir .= ($template{$i}) . ($template{ ++$i}) . DS;
            $template = $dir . md5($template);
        }
        return $resolve[$template] = $template;
    }

    /**
     *  获取模板文件
     * @access public
     * @param string $template  模板
     * @return string
     */
    private function get_template_file(&$template) {
        static $templateFile = array();
        $template = str_replace('\\//', DS, $template);
        if (isset($templateFile[$template])) {
            $template = $templateFile[$template];
            return $template;
        }
        if (file_exists($template)) {
            $t = $template;
        } else {
            $suffix = $this->config->view->template_suffix;
            foreach ($this->get_template_path() as $row) {
                $t = rtrim($row, '\\//') . DS . ($this->theme? : '') . $template . $suffix;
                if (file_exists($t)) {
                    break;
                }
            }
            if (!file_exists($t))
                throw new \Exception(($this->language->template_not_exist . '[' . $template . ']'));
        }
        if (\filesize($t) > ((int) $this->config->view->template_size * 1024 * 1024))
            throw new \Exception($this->language->template_too . '[' . $template . ']');
        return $template = $templateFile[$template] = $t;
    }

    public function __destruct() {
        $this->show();
    }

    private function show() {
        $files = get_included_files();
        if (loader::init('config')->view->trace && !loader::init('t_verify')->is_ajax() && !loader::init('t_verify')->is_flash()) {
            loader::init('b_trace')->init($files);
        }
        if (!empty(self::$_content)) {
            //header('Cache-control:private ');
            header('X-Powered-By:FEROS PHP Framework');
            if ($this->config->view->header_gzip)
                $this->view->ob_gzip(self::$_content);
            echo self::$_content;
        }
    }

}
