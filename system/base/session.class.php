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
 * session类
 * @author sanliang
 */
class session extends common {

    public function init() {
        if ($this->config->session->driver) {
            $session = loader::init('\\driver\\session\\' . $this->config->session->driver);
            if (!is_object($session))
                throw new \Exception($this->language->driver_session_not_exist . '[' . $this->config->session->driver . ']');
            session_module_name('user');
            session_set_save_handler(array(&$session, "open"), array(&$session, "close"), array(&$session, "read"), array(&$session, "write"), array(&$session, "destroy"), array(&$session, "gc"));
        }
        if (!defined('SESSION_START')) {
            session_start();
            define('SESSION_START', TRUE);
        }
    }

}
