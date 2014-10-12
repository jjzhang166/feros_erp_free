<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace driver\cache;

use base\common;

/**
 * apc类型缓存类
 */
class apc extends common {

    public function init() {
        if (extension_loaded('apc'))
            return $this;
        return false;
    }

    public function get($key) {
        return apc_fetch($this->get_key($key));
    }

    public function set($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return apc_store($this->get_key($key), $value, $expire);
    }

    public function add($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return apc_add($this->get_key($key), $value, $expire);
    }

    public function del($key) {
        return apc_delete($this->get_key($key));
    }

    public function flush() {
        return apc_clear_cache('user');
    }
    private function get_key($key) {
        return md5($key);
    }
}
