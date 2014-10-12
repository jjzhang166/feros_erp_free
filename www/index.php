<?php

if (!ini_get('display_errors')) {
    ini_set('display_errors', 'On');
}
error_reporting(E_ALL);

if (!empty($_REQUEST['session_id']))
    session_id($_REQUEST['session_id']);
define('client_app_dir', __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'application');
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'system/feros.php';
//print_r($_COOKIE);
//feros::rep_config(TRUE);
//feros::rep_runtime(TRUE);

feros::bug(TRUE);

feros::run();


