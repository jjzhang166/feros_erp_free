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
 * 缓存类
 * @author sanliang
 */
class cache {

    static $driverinit, $auto_driver;

    public function __get($name) {
        return $this->driver($name);
    }

    public function driver($driver = NULL) {
        if (empty($driver)) {
            if (empty(self::$auto_driver)) {
                $auto = rtrim(APP_RUNTIME, '\\/') . DS . 'cache_driver.php';
                if (!is_file($auto)) {
                    foreach (explode(',', 'xcache,apc,eaccelerator,wincache,zend,file') as $value) {
                        $value = strtolower($value);
                        self::$driverinit[$value] = loader::init('d_cache\\' . $value)->init();
                        if (is_object(self::$driverinit[$value])) {
                            file_put_contents($auto, $value);
                            break;
                        }
                    }
                }
            }
            $driver = self::$auto_driver? : file_get_contents($auto);
        }
        $driver = strtolower($driver);
        return !empty(self::$driverinit[$driver]) ? self::$driverinit[$driver] : self::$driverinit[$driver] = loader::init('d_cache\\' . $driver);
        if (!is_object($class))
            throw new \Exception($this->language->driver_cache_not_exist);
        return $class;
    }

    public function __call($name, $arguments) {
        return call_user_func_array(array($this->driver(), $name), (array) $arguments);
    }

}
