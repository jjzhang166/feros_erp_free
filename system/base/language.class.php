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
 * 语言类
 * @author sanliang
 */
class language {

    static $language = array();

    public function __get($name) {
        return self::get($name);
    }

    public static function get($key) {
        $name = config::get('global')->language? : 'zh-cn';
        if (!isset(self::$language[$name])) {
            $filename = array(
                rtrim(FEROS_PATH, '\\/') . DS . 'lang' . DS . $name . DS . 'global.php',
                rtrim(FEROS_PATH, '\\/') . DS . 'lang' . DS . $name . DS . loader::init('router')->fetch_controller() . '.php',
                rtrim(APP_PATH, '\\/') . DS . 'lang' . DS . $name . DS . 'global.php',
                rtrim(APP_PATH, '\\/') . DS . 'lang' . DS . $name . DS . loader::init('router')->fetch_controller() . '.php'
            );
            $array = array();
            foreach ($filename as $value) {
                if (is_file($value))
                    $array = array_merge($array, (include $value));
            }
            self::$language[$name] = $array;
        }
        if (isset(self::$language[$name][$key]))
            return self::$language[$name][$key];
        throw new \Exception($this->language->language_not_exist);
    }

}
