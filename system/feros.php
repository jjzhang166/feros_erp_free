<?php

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('require PHP > 5.3.0 !');


define('FEROS_BEGIN_TIME', microtime(TRUE));
define('FEROS_BEGIN_MEMORY', function_exists('memory_get_usage') ? memory_get_usage() : NULL);
define('FEROS_VERSION', '2.0.1');
define('FEROS_NAME', 'FEROS PHP Framework');
define('FEROS_ENCODING', 'utf-8');
define('FEROS_PATH', __DIR__);
define('CLASS_SUFFIX', '.class.php');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('RUN_FILE') or define('RUN_FILE', basename($_SERVER['SCRIPT_FILENAME']));
defined('RUN_PATH') or define('RUN_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
defined('APP_PATH') or define('APP_PATH', dirname(__DIR__) . DS . 'application');
defined('APP_RUNTIME') or define('APP_RUNTIME', rtrim(APP_PATH, '\\/') . DS . 'runtime');
defined('APP_RUNFILE') or define('APP_RUNFILE', rtrim(APP_RUNTIME, '\\/') . DS . 'runtime.php');
if (!is_file(APP_RUNFILE)) {
    require_once (rtrim(FEROS_PATH, '\\/') . DS . 'base' . DS . 'runtime' . CLASS_SUFFIX);
    $runtime = new \base\runtime;
    $runtime->runtime(APP_RUNFILE);
    $runtime->mkdir();
}
require_once APP_RUNFILE;

class feros extends \base\feros {
    
}
