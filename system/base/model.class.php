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
 * 模型类
 * @author sanliang
 */
class model extends db {

    public function init($class) {
        return $this->from(ltrim(substr($class, strrpos($class, '\\')), '\\/'));
    }
}
