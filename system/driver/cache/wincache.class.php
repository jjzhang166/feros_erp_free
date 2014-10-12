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
 * WinCache类型缓存类
 */
class wincache extends common {

    public function init() {
        if (extension_loaded('wincache') && ini_get('wincache.ucenabled'))
            return $this;
       return false;
    }

    public function get($key) {
        return wincache_ucache_get($this->get_key($key));
    }

    public function set($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return wincache_ucache_set($this->get_key($key), $value, $expire);
    }

    public function add($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return wincache_ucache_add($this->get_key($key), $value, $expire);
    }

    public function del($key) {
        return wincache_ucache_delete($this->get_key($key));
    }

    public function flush() {
        return wincache_ucache_clear();
    }

    private function get_key($key) {
        return md5($key);
    }

}
