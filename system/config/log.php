<?php

return array(
     /**
     * 是否记录日志
     * @param boot 
     */
    'write'=>FALSE,
    /**
     * 记录日志类型
     * @param boot 
     */
    'tyles' =>'sql,error,notic,info',
    /**
     * 日志驱动
     * @param string 
     */
    'driver' => 'file',
    /**
     * 日志日间格式
     * @param string 
     */
    'time_format' => 'Y-m-d H:i:s',
    /**
     * 日志文件大小限制
     * @param int 
     */
    'file_size' => 2097152,
    /**
     * 日志保存目录
     * @param string
     */
    'destination' => rtrim(APP_RUNTIME, '\\/') . DS . 'Log',
);
