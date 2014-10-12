<?php

return array(
    /**
     * 是否启动session
     * @param string 
     */
    'start' => true,
    /**
     * session保存路径
     * @param string 
     */
    'save_path' => rtrim(APP_RUNTIME, '\\/') . DS . 'session',
    /**
     * session有效期
     * @param int 
     */
    'lifetime' => 21600,
    /**
     * session驱动
     * @param string 
     */
    'driver' => 'db',
);
