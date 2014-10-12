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
 * 文件夹类
 * @author sanliang
 */
class dir {

    /**
     * 转化 \ 为 /
     * 
     * @param	string	$path	路径
     * @return	string	路径
     */
    public function path(&$path) {
        $path = str_replace('\\', '/', $path);
        return $path;
    }

    /**
     * 创建目录
     * 
     * @param	string	$path	路径
     * @param	string	$mode	属性
     * @return	string	如果已经存在则返回true，否则为flase
     */
    public function create($path, $mode = 0777) {
        if (is_dir($path))
            return true;
        $_path = dirname($path);
        if ($_path !== $path)
            $this->create($_path, $mode);
        return @mkdir($path, $mode);
    }

    /**
     * 拷贝目录及下面所有文件
     * 
     * @param	string	$fromdir	原路径
     * @param	string	$todir		目标路径
     * @return	string	如果目标路径不存在则返回false，否则为true
     */
    public function copy($fromdir, $todir) {
        $fromdir = rtrim($this->path($fromdir), '\\/') . DS;
        $todir = rtrim($this->path($todir), '\\/') . DS;
        if (!is_dir($fromdir))
            return FALSE;
        if (!is_dir($todir))
            $this->create($todir);
        $list = glob($fromdir . '*');
        if (!empty($list)) {
            foreach ($list as $v) {
                $path = $todir . basename($v);
                if (is_dir($v)) {
                    $this->copy($v, $path);
                } else {
                    copy($v, $path);
                    @chmod($path, 0777);
                }
            }
        }
        return TRUE;
    }

    /**
     * 转换目录下面的所有文件编码格式
     * @param	string	$in_charset		原字符集
     * @param	string	$out_charset	目标字符集
     * @param	string	$dir			目录地址
     * @param	string	$fileexts		转换的文件格式
     * @return	string	如果原字符集和目标字符集相同则返回false，否则为true
     */
    public function iconv($in_charset, $out_charset, $dir, $fileexts = 'php|html|htm|shtml|shtm|js|txt|xml') {
        if ($in_charset == $out_charset)
            return false;
        $list = $this->lists($dir);
        foreach ($list as $v) {
            if (pathinfo($v, PATHINFO_EXTENSION) == $fileexts && is_file($v)) {
                $this->t_file->file_put_contents($v, $this->string->charset($in_charset, $out_charset, $this->t_file->file_get_contents($v)));
            }
        }
        return true;
    }

    /**
     * 列出目录下所有文件
     * @param	string	$path		路径
     * @param	string	$exts		扩展名
     * @param	array	$list		增加的文件列表
     * @return	array	所有满足条件的文件
     */
    public function lists($path, $exts = '', $list = array()) {
        $path = rtrim($this->path($path), '\\/') . DS;
        $files = glob($path . '*');
        foreach ($files as $v) {
            if (!$exts || pathinfo($v, PATHINFO_EXTENSION) == $exts) {
                $list[] = $v;
                if (is_dir($v)) {
                    $list = $this->lists($v, $exts, $list);
                }
            }
        }
        return $list;
    }

    /**
     * 设置目录下面的所有文件的访问和修改时间
     * @param	string	$path		路径
     * @param	int		$mtime		修改时间
     * @param	int		$atime		访问时间
     * @return	array	不是目录时返回false，否则返回 true
     */
    public function touch($path, $mtime = null, $atime = null) {
        if (!is_dir($path))
            return false;
        $path = rtrim($this->path($path), '\\/') . DS;
        if (!is_dir($path))
            touch($path, $mtime, $atime);
        $files = glob($path . '*');
        foreach ($files as $v) {
            is_dir($v) ? $this->touch($v, $mtime, $atime) : touch($v, $mtime, $atime);
        }
        return true;
    }

    /**
     * 目录列表
     * 
     * @param	string	$dir		路径
     * @param	int		$parentid	父id
     * @param	array	$dirs		传入的目录
     * @return	array	返回目录列表
     */
    public function tree($dir, $parentid = 0, $dirs = array()) {

        $id = 0;
        $list = glob($dir . '*');
        foreach ($list as $v) {
            if (is_dir($v)) {
                $id++;
                $dirs[$id] = array('id' => $id, 'parentid' => $parentid, 'name' => basename($v), 'dir' => $v . '/');
                $dirs = $this->tree($v . '/', $id, $dirs);
            }
        }
        return $dirs;
    }

    /**
     * 删除目录及目录下面的所有文件
     * 
     * @param	string	$dir		路径
     * @return	bool	如果成功则返回 TRUE，失败则返回 FALSE
     */
    public function delete($dir) {
        $dir = rtrim($this->path($dir), '\\/') . DS;
        if (!is_dir($dir))
            return FALSE;
        $list = glob($dir . '*');
        foreach ($list as $v) {
            is_dir($v) ? $this->delete($v) : $this->t_file->delete($v);
        }
        return @rmdir($dir);
    }

}
