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
 * cookie操作类
 * $this->cookie->set('name', 'test');
 */
class cookie {

    /**
     * 设置cookie
     * @param string $name cookie名称
     * @param string $value cookie值,默认为null
     * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
     * @param string|int $expires 过期时间,默认为null即会话cookie,随着会话结束将会销毁
     * @param string $path cookie保存的路径,默认为null即采用默认
     * @param string $domain cookie所属域,默认为null即不设置
     * @param boolean $secure 是否安全连接,默认为false即不采用安全链接
     * @param boolean $httponly 是否可通过客户端脚本访问,默认为false即客户端脚本可以访问cookie
     * @return boolean 设置成功返回true,失败返回false
     */
    public function set($name, $value = null, $encode = false, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false) {
        if (empty($name))
            return false;
        $encode && $value && $value = base64_encode(serialize($value));
        $path = $path ? $path : '/';
        setcookie($name, $value,(is_numeric($expires)?time()+$expires:NULL), $path, $domain, $secure, $httponly);
        return true;
    }

    /**
     * 根据cookie的名字删除cookie
     * @param string $name cookie名称
     * @return boolean 删除成功返回true
     */
    public function delete($name) {
        if (self::exist($name)) {
            self::set($name, '');
            unset($_COOKIE[$name]);
        }
        return true;
    }

    /**
     * 取得指定名称的cookie值
     * @param string $name cookie名称
     * @param boolean $dencode 是否对cookie值进行过解码,默认为false即不用解码
     * @return mixed 获取成功将返回保存的cookie值,获取失败将返回false
     */
    public function get($name, $dencode = false) {
        if (self::exist($name)) {
            $value = $_COOKIE[$name];
            $value && $dencode && $value = unserialize(base64_decode($value));
            return $value ? $value : $value;
        }
        return false;
    }

    /**
     * 判断cookie是否存在
     * @param string $name cookie名称
     * @return boolean 如果不存在则返回false,否则返回true
     */
    public function exist($name) {
        return isset($_COOKIE[$name]);
    }

}
