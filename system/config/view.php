<?php

/*
 * 模板配制
 */

return array(
    /**
     * 模板解析左分隔符
     * @param string
     */
    'left_delimiter' => '{',
    /**
     * 模板解析右分隔符
     * @param string
     */
    'right_delimiter' => '}',
    /**
     * 是否运行模板内插入PHP代码
     * @param bool
     */
    'php_off' => TRUE,
    /**
     * 自动创建子目录
     * @param bool
     */
    'use_sub_dirs' => TRUE,
    /**
     * 是否压缩模板
     * @param bool
     */
    'strip_space' => FALSE,
    /**
     * Gzip数据压缩传输
     * @param bool
     */
    'header_gzip' => FALSE,
    /**
     * 模板缓存过期时间,为-1，则设置缓存永不过期,0可以让缓存每次都重新生成
     * @param int
     */
    'cache_lifetime' => 3600,
    /**
     * 编译目录
     * @param string
     */
    'compile_dir' => rtrim(APP_RUNTIME, '\\/') . DS . 'Compile',
    /**
     * 缓存目录
     * @param string
     */
    'cache_dir' => rtrim(APP_RUNTIME, '\\/') . DS . 'Cache',
    /**
     * 模板目录 多个目录用数组
     * @param array|string
     */
    'template_path' => rtrim(APP_PATH, '\\/') . DS . 'view',
    /**
     * 模板后缀
     * @param string
     */
    'template_suffix' => '.html',
    /**
     * 模板大小 单位MB
     * @param int
     */
    'template_size' => 1,
    /**
     * 显示调度报告
     * @param bool
     */
    'trace' => FALSE
);
