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
 * 处理类
 * @author sanliang
 */
class handler {

    public static function error($errno, $errstr, $file, $line) {
        //debug, info, notice, warn, error, crit
        $error = "[{$errno}] {$errstr} " . $file . " 第 {$line} 行.";
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                loader::init('log', 'error', $error);
                break;
            default:
                loader::init('log', 'notic', $error);
                break;
        }
    }

    public static function exception($e) {
        $message = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $file = $trace[0]['file'];
            $line = $trace[0]['line'];
        } else {
            $file = $e->getFile();
            $line = $e->getLine();
        }
        loader::init('log', 'error', "{$message} {$file} 第 {$line} 行.");
        loader::init('view')->assign('message', \feros::$bug ? $message : '服务器错误，请查看错误日志');
        loader::init('view')->set_template_path(rtrim(FEROS_PATH, '\\/') . DS . 'view')->display('500');
    }

    public static function shutdown() {
        if (feros::$config) {
            loader::init('t_file')->delete(loader::init('config')->run_file());
        }
        if (feros::$runtime) {
            loader::init('t_file')->delete(APP_RUNFILE);
        }
        if (loader::init('config')->log->write) {
            log::save();
        }
    }

}
