<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace driver\session;

use base\common;

/*
  CREATE TABLE `fer_session` (
  `session_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `session_expires` int(10) unsigned NOT NULL DEFAULT '0',
  `session_data` text,
  PRIMARY KEY (`session_id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='session';
 */

/**
 * DB驱动类
 * @author sanliang
 */
class db extends common {

    const db_table = "session";

    function open($save_path, $sess_name) {
        return true;
    }

    function close() {
        return true;
    }

    function read($id) {
        $where['session_id'] = md5($id);
        $where['session_expires[>]'] = time();
        $list = $this->model->from(self::db_table)->where($where)->find();
        return isset($list['session_data']) ? $list['session_data'] : null;
    }

    function write($id, $sess_data) {
        $data['session_id'] = md5($id);
        $data['session_expires'] = time() + $this->config->session->lifetime;
        $data['session_data'] = $sess_data;
        return $this->model->from(self::db_table)->insert($data, true);
    }

    function destroy($id) {
        $where['session_id'] = md5($id);
        $this->model->from(self::db_table)->where($where)->delete();
        return true;
    }

    function gc($titme) {
        $where['session_expires[<]'] = time();
        $this->model->from(self::db_table)->where($where)->delete();
        return true;
    }

}
