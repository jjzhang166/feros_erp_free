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

/**
 * file驱动类
 * @author sanliang
 */
class file extends common {

    private $save_path;

    function open($save_path, $sess_name) {
        $this->save_path = $save_path;
        return true;
    }

    function close() {
        return true;
    }

    function read($id) {
        return $this->t_file->file_get_contents($this->filename($id));
    }

    function write($id, $sess_data) {
        return $this->t_file->file_put_contents($this->filename($id), $sess_data);
    }

    function destroy($id) {
        $this->t_file->delete($this->filename($id));
        return true;
    }

    function gc($titme) {
        return true;
    }

    private function filename($id) {
        $name = md5($id);
        $dir = '';
        for ($i = 0; $i < 6; $i++)
            $dir .= ($name{$i}) . ($name{ ++$i}) . DS;
        $name = $dir . $id;
        $filename = $name . '.sess';
        return $this->get_save_path() . $filename;
    }

    public function get_save_path() {
        return rtrim(($this->config->session->save_path? : $this->save_path), '\\/') . DS;
    }

}
