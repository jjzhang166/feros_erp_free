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
 * 日志类
 * @author sanliang
 */
class log extends common {

    static $log = array();

    public function write($log, $type = NULL) {
            self::$log[$type][] = $log;
    }

    public static function save() {
        if (loader::init('config')->log->write) {
            $tyle = explode(',',trim(str_replace(' ','',loader::init('config')->log->tyles)));
            if (!empty(self::$log)) {
                $driver = loader::init('d_log\\' . strtolower(\base\config::get('log')->driver))->init();
                foreach (self::$log as $key => $value) {
                    if (in_array($key, $tyle)) {
                        foreach ($value as $val) {
                            $driver->write($val, $key);
                        }
                    }
                }
            }
        }
    }

    public function sql($log) {
        $this->write($log, 'sql');
    }

    public function error($log) {
        $this->write($log, 'error');
    }

    public function notic($log) {
        $this->write($log, 'notic');
    }

    public function info($log) {
        $this->write($log, 'info');
    }

}
