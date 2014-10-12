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
 * URL类
 * @author sanliang
 */
class url extends common {

    public function site($url = '') {
        if($this->t_verify->is_url($url))
            return $url;
        $url=trim($url, '\\/');
        return $this->domain() . ($this->config->route->basename ? trim($this->config->route->basename, '\\/') . '/' : '') . ($url ? $url . $this->config->route->url_suffix : '');
    }

    public function base($url = '') {
        if($this->t_verify->is_url($url))
            return $url;
        return $this->domain().ltrim($url, '\\/');
    }

    public function domain() {
        return rtrim((($this->t_verify->is_ssl() ? 'https://' : 'http://') . trim($_SERVER['HTTP_HOST'], '\\/') . dirname($this->router->get_script_path())), '\\/') . '/' ;
    }

}
