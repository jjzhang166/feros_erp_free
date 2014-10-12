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
 * 配制类
 * @author sanliang
 */
class config extends common {

    static public $config = array();

    public function __construct() {
        if (empty(self::$config) && is_file($this->run_file())) {
            self::$config = include $this->run_file();
        }
    }

    public function set($name, $value) {
        self::$config[$name] = (object) $value;
    }

    public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __get($name) {
        return self::get($name);
    }

    public function __call($name, $value) {
        if (!empty($value[1]))
            self::$config[$name]->$value[0] = $value[1];
    }

    public static function get($name) {
        if (!isset(self::$config[$name])) {
            $array = array();
            foreach (self::get_dirs($name) as $value) {
                if (is_file($value))
                    $array = array_merge($array, (include $value));
            }
            self::$config[$name] = (object) $array;
        }
        if (!is_object(self::$config[$name]))
            self::$config[$name] = (object) self::$config[$name];
        if (isset(self::$config[$name]))
            return self::$config[$name];
        throw new \Exception($this->language->config_not_exist);
    }

    private static function get_dirs($name) {
        return array(rtrim(FEROS_PATH, '\\/') . DS . 'config' . DS . $name . '.php', rtrim(APP_PATH, '\\/') . DS . 'config' . DS . $name . '.php');
    }

    public function __destruct() {
        
    }

    public function run_file() {
        return rtrim(APP_RUNTIME, '\\/') . DS . 'config.php';
    }

}
