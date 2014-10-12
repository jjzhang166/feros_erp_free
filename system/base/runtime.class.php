<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace base;

/**
 * 核心编译类
 * @author sanliang
 */
class runtime {

    static $app_path = array(
        'controller',
        'libraries',
        'config',
        'model',
        'plugin',
        'view'
    );
    private $runtionfile = array(
        'base\loader',
        'base\common',
        'base\feros',
        'base\handler',
        'base\config',
        'base\router',
        'base\log');

    public function mkdir() {
        foreach (self::$app_path as $value)
            $this->create(rtrim(APP_PATH, '\\/') . DS . $value);
        $this->deny(APP_PATH)->deny(FEROS_PATH);
    }

    public function runtime($runtime) {
        $content = '';
        foreach ($this->runtionfile as $value)
            $content.=$this->compile(rtrim(FEROS_PATH, '\\/') . DS . $value . CLASS_SUFFIX);
        $this->create(dirname($runtime));
        file_put_contents($runtime, ($this->whitespace('<?php ' . $content)));
    }

    public function config() {
        foreach (array(rtrim(FEROS_PATH, '\\/') . DS . 'config', rtrim(APP_PATH, '\\/') . DS . 'config') as $key => $value) {
            foreach (loader::init('t_dir')->lists($value) as $value) {
                $key = basename($value, '.php');
                $config[$key] = isset($config[$key]) ? array_merge($config[$key], (include $value)) : include $value;
            }
        }
        $this->create(dirname(loader::init('config')->run_file()));
        file_put_contents(loader::init('config')->run_file(), $this->whitespace("<?php\r\nreturn " . var_export($config, true) . ";"));
    }

    function compile($filename) {
        $content = file_get_contents(str_replace('\\', DS, $filename));
        $content = substr(trim($content), 5);
        if ('?>' == substr($content, -2))
            $content = substr($content, 0, -2);
        return $content;
    }

    /**
     * 写入安全文件
     */
    public function deny($dir) {
        is_file(($filename = rtrim($dir, '\\//') . DS . '.htaccess'))? : file_put_contents($filename, 'Deny from all');
        is_file(($filename = rtrim($dir, '\\//') . DS . 'index.html'))? : file_put_contents($filename, '');
        return $this;
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
     * 去除代码中的空白和注释
     * @param string $content 代码内容
     * @return string
     */
    function whitespace($content) {
        $stripStr = '';
        //分析php源码
        $tokens = token_get_all($content);
        $last_space = false;
        for ($i = 0, $j = count($tokens); $i < $j; $i++) {
            if (is_string($tokens[$i])) {
                $last_space = false;
                $stripStr .= $tokens[$i];
            } else {
                switch ($tokens[$i][0]) {
                    //过滤各种PHP注释
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    //过滤空格
                    case T_WHITESPACE:
                        if (!$last_space) {
                            $stripStr .= ' ';
                            $last_space = true;
                        }
                        break;
                    default:
                        $last_space = false;
                        $stripStr .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }

}
