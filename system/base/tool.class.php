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
 * 工具类
 * @author sanliang
 */
class tool {

    public function __get($class) {
        return loader::init("t_{$class}");
    }

}
