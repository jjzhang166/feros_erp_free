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
 * ZendDataCache类型缓存类
 */
class zend extends common {

    public function init() {
        if (function_exists('zend_shm_cache_store'))
            return $this;
        return false;
    }

    public function get($key) {
        return zend_shm_cache_fetch($this->get_key($key))? : false;
    }

    public function set($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return zend_shm_cache_store($this->get_key($key), $value, $expire);
    }

    public function add($key, $value, $expire = NULL) {
        return $this->set($key, $value, $expire);
    }

    public function delete($key) {
        return zend_shm_cache_delete($this->get_key($key));
    }

    public function flush() {
        return zend_shm_cache_clear();
    }
    private function get_key($key){
        return md5($key);
    }

}
