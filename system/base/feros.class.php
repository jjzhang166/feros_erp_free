<?php

namespace base;

class feros {

    static public $bug = FALSE, $config = FALSE, $runtime = FALSE;

    public static function run() {
        self::init();
        \base\loader::init('router')->init();
    }

    /**
     * 返回当前执行时间
     * @return int
     */
    public static function run_time($end = null, $dec = 4) {
        return number_format((($end? : microtime(TRUE)) - FEROS_BEGIN_TIME), $dec);
    }

    /**
     * 返回当前执行内存
     * @return int
     */
    public static function run_memory($end = null) {
        return FEROS_BEGIN_MEMORY ? number_format((($end? : memory_get_usage()) - FEROS_BEGIN_MEMORY)) : NULL;
    }

    private static function init() {
        mb_internal_encoding(FEROS_ENCODING);
        self::$bug ? error_reporting(E_ALL) : error_reporting(0);
        spl_autoload_register('\base\loader::autoload');
        register_shutdown_function('\base\handler::shutdown');
        set_exception_handler('\base\handler::exception');
        set_error_handler('\base\handler::error');
        date_default_timezone_set(\base\loader::init('config')->global->timezone? : 'PRC');
    }

    public static function bug($bool = TRUE) {
        self::$bug = $bool;
    }

    public static function rep_config($bool = TRUE) {
        self::$config = $bool;
    }

    public static function rep_runtime($bool = TRUE) {
        self::$runtime = $bool;
    }

}
