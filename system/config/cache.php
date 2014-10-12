<?php

//缓存驱动自动加载顺序：'xcache,apc,eaccelerator,wincache,zend,file'
return array(
    /**
     * 文件缓存目录
     * @param string 
     */
    'temp_path' => rtrim(APP_RUNTIME, '\\/') . DS . 'Temp',
    /**
     * 自动创建子目录
     * @param bool
     */
    'use_sub_dirs' => true,
    /**
     * 是否开启压缩
     * @param bool
     */
    'gzcompress' => true,
    /**
     * 默认过期
     * @param int
     */
    'expire' => 3600,
);
