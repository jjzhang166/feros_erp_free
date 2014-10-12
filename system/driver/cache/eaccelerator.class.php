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
 * eaccelerator类型缓存类
 */
class eaccelerator extends common {

    public function init() {
        if (function_exists('eaccelerator_get'))
            return $this;
        return false;
    }

    public function get($key) {
        $result = eaccelerator_get($this->get_key($key));
        return $result !== NULL ? $result : false;
    }

    public function set($key, $value, $expire = NULL) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        return eaccelerator_put($this->get_key($key), $value, $expire);
    }

    public function add($key, $value, $expire = NULL) {
        return $this->set($key, $value, $expire);
    }

    public function del($key) {
        return eaccelerator_rm($this->get_key($key));
    }

    public function flush() {
        eaccelerator_gc();
        $keys = eaccelerator_list_keys();
        foreach ($keys as $key)
            $this->del(substr($key['name'], 1));
        return true;
    }

    private function get_key($key) {
        return md5($key);
    }

}
