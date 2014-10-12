<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace driver\log;

use base\common;

/**
 * 文件类型日志驱动
 */
class file extends common {

    public function init() {
        return $this;
    }

    public function write($log, $type = NULL) {
        if (empty($log) || !is_string($log))
            return;
        $now = date($this->config->log->time_format);
        $destination = rtrim($this->config->log->destination, '\\/') . DS . ($type ? $type . DS : '') . date('y_m_d') . '.log';

        $dir = dirname($destination);
        is_dir($dir)? : $this->t_dir->create($dir);
        is_dir($dir)? : $this->t_file->deny($dir);
        if (is_file($destination) && floor($this->config->log->file_size) <= filesize($destination))
            rename($destination, $dir . DS . time() . '-' . basename($destination));
        error_log("[" . \feros::run_time() . "s] [{$now}] " . $this->t_client->ip() . ' ' . $_SERVER['REQUEST_URI'] . "\n{$log}\n----------------------分隔线----------------------\n", 3, $destination);
    }

}
