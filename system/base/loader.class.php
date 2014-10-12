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
 * 装载机
 * @author sanliang
 */
class loader {

    static $_instance = array();

    /**
     * 类实例
     * @param string $class 对象类名
     * @param string $callback 方法名
     * @param array $param_arr 将参数传递给方法
     * @return object
     */
    public static function init($class, $callback = '', $param_arr = array()) {
        return self::initialize($class, $callback, (array) $param_arr);
    }

    static $classes = array(
        'tool' => '\base\tool',
        'model' => '\base\model',
        'config' => '\base\config',
        'router' => '\base\router',
        'view' => '\base\view',
        'language' => '\base\language',
        'cache' => '\base\cache',
        'runtime' => '\base\runtime',
        'session' => '\base\session',
        'url' => '\base\url',
        'log' => '\base\log',
        'db' => '\base\db',
        'cookie' => '\tool\cookie'
    );

    /**
     * 类实例
     * @param string $class 对象类名
     * @param string $callback 方法名
     * @param array $param_arr 将参数传递给方法
     * @return object
     */
    private static function initialize($class, $callback = '', $param_arr = array()) {
        $substr = substr($class, 0, 2);
        switch (strtolower($substr)) {
            case 'b_':
                return self::initialize('\\base\\' . substr($class, 2), $callback, $param_arr);
            case 'm_':
                return self::initialize('\\model\\' . substr($class, 2), 'init', array(substr($class, 2)))? : self::initialize('\\base\\model')->init(substr($class, 2));
            case 'c_':
                return self::initialize('\\controller\\' . substr($class, 2), $callback, $param_arr);
            case 't_':
                return self::initialize('\\tool\\' . substr($class, 2), $callback, $param_arr);
            case 'd_':
                return self::initialize('\\driver\\' . substr($class, 2), $callback, $param_arr);
            case 'l_':
                return self::initialize('\\libraries\\' . substr($class, 2), $callback, $param_arr);
            case 'p_':
                return self::initialize('\\plugin\\' . substr($class, 2), $callback, $param_arr);
            default:
                $class = isset(self::$classes[$class]) ? self::$classes[$class] : $class;
        }
        if (!isset(self::$_instance[$class])) {
            if (class_exists($class)) {
                $x = new $class();
                if (!empty($callback) && is_callable($class, $callback)) {
                    self::$_instance[$class] = call_user_func_array(array($x, $callback), (array) $param_arr);
                } else {
                    self::$_instance[$class] = $x;
                }
            } else {
                return FALSE;
            }
        }
        return self::$_instance[$class];
    }

    /**
     * 类库自动加载
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
        $name = trim(str_replace('\\', DS, $class), '\\/') . CLASS_SUFFIX;
        $x = substr($name, 0, stripos($name, DS));
        if (in_array($x, array('base', 'tool', 'driver'))) {
            $filename = rtrim(FEROS_PATH, '\\/') . DIRECTORY_SEPARATOR . $name;
            if (is_file($filename)) {
                require_once $filename;
                return true;
            }
        } elseif (in_array($x, array('controller', 'model'))) {
            $filename = rtrim(APP_PATH, '\\/') . DIRECTORY_SEPARATOR . $name;
            if (is_file($filename)) {
                require_once $filename;
                return true;
            }
        } else {
            $filename = rtrim(APP_PATH, '\\/') . DIRECTORY_SEPARATOR . 'libraries' . $name;
            if (is_file($filename)) {
                require_once $filename;
                return true;
            }
        }
    }

}
