<?php

return array(
    /**
     * PDO DNS
     * @param string 
     */
    'dns' => 'mysql:host=localhost;dbname=erp;charset=utf8',
    /**
     * 表前缀
     * @param string 
     */
    'prefix' => 'fer_',
    /**
     * 数据服务器账号
     * @param string 
     */
    'username' => 'root',
    /**
     * 数据库密码
     * @param string 
     */
    'password' => '123',
    /**
     * 缓存驱动
     * null 时自动选择缓存驱动
     * @param string 
     */
    'cache_driver' => NULL,
    /**
     * 缓存时间
     * @param int 
     */
    'cache_expire' => 1600,
    /**
     * PDO附加参数
     * @param string 
     */
    'option' => array()
);
