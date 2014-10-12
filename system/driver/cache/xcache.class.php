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
 * xcache类型缓存类
 */
class xcache extends common {

    public function init() {
        if (function_exists('xcache_isset'))
            return $this;
        return false;
    }

    public function get($key) {
        return xcache_isset($this->get_key($key)) ? xcache_get($this->get_key($key)) : false;
    }

    public function set($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return xcache_set($this->get_key($key), $value, $expire);
    }

    public function add($key, $value, $expire = NULL) {
        return $this->set($key, $value, $expire);
    }

    public function del($key) {
        return xcache_unset($this->get_key($key));
    }

    public function flush() {
        for ($i = 0, $max = xcache_count(XC_TYPE_VAR); $i < $max; $i++) {
            if (xcache_clear_cache(XC_TYPE_VAR, $i) === false)
                return false;
        }
        return true;
    }
    private function get_key($key) {
        return md5($key);
    }
}
