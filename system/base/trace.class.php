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
class trace extends common {

    static $colors = array(
                'chars' => 'grey',
                'keywords' => 'blue',
                'joins' => 'gray',
                'functions' => 'violet',
                'constants' => 'red'),
            $words = array(
                'keywords' => array(
                    'SELECT',
                    'UPDATE',
                    'INSERT',
                    'DELETE',
                    'REPLACE',
                    'INTO',
                    'CREATE',
                    'ALTER',
                    'TABLE',
                    'DROP',
                    'TRUNCATE',
                    'FROM',
                    'ADD',
                    'CHANGE',
                    'COLUMN',
                    'KEY',
                    'WHERE',
                    'ON',
                    'CASE',
                    'WHEN',
                    'THEN',
                    'END',
                    'ELSE',
                    'AS',
                    'USING',
                    'USE',
                    'INDEX',
                    'CONSTRAINT',
                    'REFERENCES',
                    'DUPLICATE',
                    'LIMIT',
                    'OFFSET',
                    'SET',
                    'SHOW',
                    'STATUS',
                    'BETWEEN',
                    'AND',
                    'IS',
                    'NOT',
                    'OR',
                    'XOR',
                    'INTERVAL',
                    'TOP',
                    'GROUP BY',
                    'ORDER BY',
                    'DESC',
                    'ASC',
                    'COLLATE',
                    'NAMES',
                    'UTF8',
                    'DISTINCT',
                    'DATABASE',
                    'CALC_FOUND_ROWS',
                    'SQL_NO_CACHE',
                    'MATCH',
                    'AGAINST',
                    'LIKE',
                    'REGEXP',
                    'RLIKE',
                    'PRIMARY',
                    'AUTO_INCREMENT',
                    'DEFAULT',
                    'IDENTITY',
                    'VALUES',
                    'PROCEDURE',
                    'FUNCTION',
                    'TRAN',
                    'TRANSACTION',
                    'COMMIT',
                    'ROLLBACK',
                    'SAVEPOINT',
                    'TRIGGER',
                    'CASCADE',
                    'DECLARE',
                    'CURSOR',
                    'FOR',
                    'DEALLOCATE'),
                'joins' => array(
                    'JOIN',
                    'INNER',
                    'OUTER',
                    'FULL',
                    'NATURAL',
                    'LEFT',
                    'RIGHT'),
                'chars' => '/([\\.,\\(\\)<>:=`]+)/i',
                'functions' => array(
                    'MIN',
                    'MAX',
                    'SUM',
                    'COUNT',
                    'AVG',
                    'CAST',
                    'COALESCE',
                    'CHAR_LENGTH',
                    'LENGTH',
                    'SUBSTRING',
                    'DAY',
                    'MONTH',
                    'YEAR',
                    'DATE_FORMAT',
                    'CRC32',
                    'CURDATE',
                    'SYSDATE',
                    'NOW',
                    'GETDATE',
                    'FROM_UNIXTIME',
                    'FROM_DAYS',
                    'TO_DAYS',
                    'HOUR',
                    'IFNULL',
                    'ISNULL',
                    'NVL',
                    'NVL2',
                    'INET_ATON',
                    'INET_NTOA',
                    'INSTR',
                    'FOUND_ROWS',
                    'LAST_INSERT_ID',
                    'LCASE',
                    'LOWER',
                    'UCASE',
                    'UPPER',
                    'LPAD',
                    'RPAD',
                    'RTRIM',
                    'LTRIM',
                    'MD5',
                    'MINUTE',
                    'ROUND',
                    'SECOND',
                    'SHA1',
                    'STDDEV',
                    'STR_TO_DATE',
                    'WEEK'),
                'constants' => '/(\'[^\']*\'|[0-9]+)/i');

    public function init($files) {

        $var['runtime'] = \feros::run_time();

        $var['trace'] = array('BASE' => '基本', 'FILE' => '文件', 'INFO' => '消息', 'ERR' => '错误', 'SQL' => 'SQL', 'DEBUG' => '调试');

        foreach ($files as $key => $file) {
            if (is_file($file))
                $var['info']['FILE'][] = str_replace(RUN_PATH, 'RUN_PATH ', str_replace(APP_PATH, 'APP_PATH ', str_replace(FEROS_PATH, 'FEROS_PATH ', $file))) . ' ( ' . $this->t_format->size(filesize($file)) . ')';
        }
        foreach (\base\log::$log as $key => $value) {
            if ($key == 'sql') {
                foreach ($value as $sql) {
                    $var['info']['SQL'][] = $this->high_sql($sql);
                }
            } elseif ($key == 'info') {
                $var['info']['INFO'] = $value;
            } else {
                $var['info']['ERR'] = $value;
            }
        }
        $var['info']['BASE'] = array(
            '框架信息' => FEROS_NAME . ' ' . FEROS_VERSION . ' ' . FEROS_ENCODING . ' ' . (\feros::$bug ? '调试' : '生产'),
            'PHP 版本' => PHP_VERSION,
            'PHPINFO' => $this->router->get_path_info(),
            '请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $this->router->get_script_path(),
            '运行时间' => $var['runtime'] . 's',
            '吞 吐 率' => $this->t_format->number(1 / $var['runtime'], 2) . 'req/s',
            '内存开销' => \feros::run_memory()? : '不支持',
            '支持缓存' => $this->check_cache('file,xcache,apc,eaccelerator,wincache,zend'),
            '文件加载' => count($files),
            '会话信息' => session_id() ? 'SESSION_ID=' . session_id() : '无',
        );
        $var['logo'] = $this->t_favicon->png();
        $this->view->assign($var);
        $this->view->display(rtrim(FEROS_PATH, '\\/') . DS . 'view' . DS . 'trace.html');
    }

    public function check_cache($driver) {
        $var = array();
        foreach (explode(',', $driver) as $value) {
            if (loader::init('d_cache\\' . $value, 'init'))
                $var[] = $value;
        }
        return implode(',', $var);
    }

    public function high_sql($sql) {
        $sql = str_replace('\\\'', '\\&#039;', $sql);
        foreach (self::$colors as $key => $color) {
            if (in_array($key, array('constants', 'chars'))) {
                $regexp = self::$words[$key];
            } else {
                $regexp = '/\\b(' . join("|", self::$words[$key]) . ')\\b/i';
            }
            $sql = preg_replace($regexp, '<span style="color:' . $color . "\">$1</span>", $sql);
        }
        return $sql;
    }

}
