<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace tool;

/**
 * 格式化
 * @author sanliang
 */
class format {

    /**
     * 格式化字节大小
     * @param int $size
     * @return string
     */
    public function size($size) {
        if ($size > 0 and is_numeric($size)) {
            $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');
            return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit[$i];
        }
        return 0;
    }

    public function number($number, $decimals = 0) {
        return number_format($number, $decimals);
    }

}
