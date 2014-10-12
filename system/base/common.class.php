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
 * 公共类
 * @author sanliang
 */
class common {

    public function __get($name) {
        return loader::init($name);
    }

    

    public function __isset($name) {
        
    }

    public function __unset($name) {
        
    }

}
