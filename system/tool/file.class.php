<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace tool;

use base\common;

/**
 * 文件类
 * @author sanliang
 */
class file extends common {

    /**
     * 返回文件 MIME type类型
     * @param string $file
     * @return string|NULL
     */
    public function get_mime_type($file) {
        return $this->t_mime->get($this->get_extension($file));
    }

    /**
     * 返回文件扩展名
     * 如路径 "path/to/something.php" 返回 "php"
     * @param string $path 文件
     * @return string
     */
    public function get_extension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * 写入文件
     * @param string $filename 文件
     * @param string $data 写入数据
     * @return string
     */
    public function file_put_contents($filename, $data) {
        $this->t_dir->path($filename);
        $this->mkdir(dirname($filename));
        return file_put_contents($filename, $data, \LOCK_EX);
    }

    /**
     * 写入数据
     * @param string $filename 文件
     * @param string $data 写入数据
     * @return string
     */
    public function file_put_data($filename, $data) {
        $this->t_dir->path($filename);
        $this->mkdir(dirname($filename));
        $data = "<?php\r\nreturn " . var_export($data, true) . ";";
        return file_put_contents($filename, $data, \LOCK_EX);
    }

    /**
     * 读取文件
     * @param string $filename 文件
     * @return string
     */
    public function file_get_contents($filename) {
        $this->t_dir->path($filename);
        return is_readable($filename) ? file_get_contents($filename) : NULL;
    }

    /**
     * 删除文件
     * @param	string	$filename 文件
     * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
     */
    public function delete($filename) {
        $this->t_dir->path($filename);
        return is_readable($filename) ? @unlink($filename) : FALSE;
    }

    /**
     * 递归的创建目录
     * @param string $path 目录路径
     * @param int $permissions 权限
     * @return boolean
     */
    public function mkdir($path, $permissions = 0777) {
        return $this->t_dir->create($path, $permissions);
    }

    /**
     * 写入安全文件
     */
    public function deny($dir) {
        is_file(($filename = rtrim($dir, '\\//') . DS . '.htaccess'))? : $this->file_put_contents($filename, 'Deny from all');
        is_file(($filename = rtrim($dir, '\\//') . DS . 'index.html'))? : $this->file_put_contents($filename, '');
        return $this;
    }

}
