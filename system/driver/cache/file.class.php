<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace driver\cache;

use base\common;

/**
 * 文件类型缓存类
 */
class file extends common {

    public function init() {
        return $this;
    }

    /**
     * 取得变量的存储文件名
     * @access private
     * @param string $name 缓存变量名
     * @return string
     */
    private function filename($name) {
        $name = md5($name);
        if ($this->config->cache->use_sub_dirs) {
            $dir = '';
            for ($i = 0; $i < 6; $i++)
                $dir .= ($name{$i}) . ($name{ ++$i}) . DS;
            $name = $dir . md5($name);
            $filename = $name . '.php';
        } else {
            $filename = $name . '.php';
        }
        return rtrim($this->config->cache->temp_path, '\\/') . DS . $filename;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name) {
        $filename = $this->filename($name);
        if (!is_file($filename)) {
            return false;
        }

        $content = $this->t_file->file_get_contents($filename);
        if (false !== $content) {
            $expire = (int) substr($content, 8, 12);
            if ($expire != 0 && time() > filemtime($filename) + $expire) {
                $this->t_file->delete($filename);
                return false;
            }
            $check = substr($content, 20, 32);
            $content = substr($content, 52, -3);
            if ($check != md5($content)) {
                return false;
            }
            if ($this->config->cache->gzcompress && function_exists('gzcompress'))
                $content = gzuncompress($content);
            $content = unserialize($content);
            return $content;
        } else {
            return false;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null) {
        if (is_null($expire))
            $expire = $this->config->cache->expire;
        $filename = $this->filename($name);
        $data = serialize($value);
        if ($this->config->cache->gzcompress && function_exists('gzcompress'))
            $data = gzcompress($data, 3);
        $check = md5($data);
        $data = "<?php\n//" . sprintf('%012d', $expire) . $check . $data . "\n?>";
        $result = $this->t_file->file_put_contents($filename, $data);
        if ($result) {
            clearstatcache();
            return true;
        } else {
            return false;
        }
    }

    public function add($name, $value, $expire = null) {
        return $this->set($name, $value, $expire);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function del($name) {
        return $this->t_file->delete($this->filename($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function flush() {
        return $this->t_dir->delete($this->config->cache->temp_path);
    }

}
