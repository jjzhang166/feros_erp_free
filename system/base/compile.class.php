<?php

/**
 * FerOS PHP template engine
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */

namespace base;

/**
 * 模板解析
 * @author sanliang
 */
class compile extends common {

    private $__source, $__template_file, $__ldel, $__rdel, $__view, $_template_preg = array(), $_template_replace = array();

    public function __construct(\base\view $view, &$content, $template) {
        if (empty($content))
            return $content;
        $this->__view = $view;
        $this->__source = $content;
        $this->__template_file = $template;
        $this->__ldel = preg_quote($this->config->view->left_delimiter);
        $this->__rdel = preg_quote($this->config->view->right_delimiter);
        $this->_compile($content);
        $content = "<?php\n/**\n* " . FEROS_NAME . "\n* @author feros<admin@feros.com.cn>\n* @copyright ©" . date('Y') . " feros.com.cn\n* @link http://www.feros.com.cn\n* @version " . FEROS_VERSION . "\n*@view {$template}\n*/\n?>" . $content;
    }

    private function _compile(&$content) {
        $this->compile_var($content);
        $this->compile_xml();
        $this->compile_php();
        $this->compile_code();
        $this->compile_html();
        $content = preg_replace($this->_template_preg, $this->_template_replace, $content);
        $content = preg_replace_callback("/##XML(.*?)XML##/s", array($this, 'xml_substitution'), $content);
    }

    private function xml_substitution($capture) {
        return "<?php echo '<?xml " . stripslashes($capture[1]) . " ?>'; ?>";
    }

    /**
     * 解析标签
     * @param array $content
     * @return string
     */
    private function parse_tag($content) {
        $content = stripslashes($content[0]);
        $content = preg_replace_callback('/\$\w+((\.\w+)*)?/', array($this, 'parse_var'), $content);
        return $content;
    }

    /**
     * 解析变量
     * @param array $var
     * @return string
     */
    private function parse_var($var) {
        if (empty($var[0]))
            return;

        $vars = explode('.', $var[0]);
        $var = array_shift($vars);
        $name = $var;
        foreach ($vars as $val)
            $name .= '["' . trim($val) . '"]';
        return $name;
    }

    private function compile_var(&$content) {
        $content = preg_replace_callback('/(' . $this->__ldel . ')([^\d\s].+?)(' . $this->__rdel . ')/is', array($this, 'parse_tag'), $content);
    }

    private function compile_code() {
        $this->_template_preg[] = '/' . $this->__ldel . '(else if|elseif) (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . 'for (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . 'while (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . '(loop|foreach) (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . 'if (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . 'else' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . "(eval|_)( |[\r\n])(.*?)" . $this->__rdel . '/is';
        $this->_template_preg[] = '/' . $this->__ldel . '_e (.*?)' . $this->__rdel . '/is';
        $this->_template_preg[] = '/' . $this->__ldel . '_p (.*?)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . '\/(if|for|loop|foreach|eval|while)' . $this->__rdel . '/i';
        $this->_template_preg[] = '/' . $this->__ldel . '((( *(\+\+|--) *)*?\!?(([_a-zA-Z][\w]*\(.*?\))|\$((\w+)((\[|\()(\'|")?\$*\w*(\'|")?(\)|\]))*((->)?\$?(\w*)(\((\'|")?(.*?)(\'|")?\)|))){0,})( *\.?[^ \.]*? *)*?){1,})' . $this->__rdel . '/i';
        $this->_template_preg[] = "/(	| ){0,}(\r\n){1,}\";/";
        $this->_template_preg[] = '/' . $this->__ldel . '(\#|\*)(.*?)(\#|\*)' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'view (.*?)' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'site (.*?)' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'base (.*?)' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'runtime' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'viewtime' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'lang (.*?)' . $this->__rdel . '/';
        $this->_template_replace[] = '<?php }else if (\\2){ ?>';
        $this->_template_replace[] = '<?php for (\\1) { ?>';
        $this->_template_replace[] = '<?php while (\\1) { ?>';
        $this->_template_replace[] = '<?php foreach (\\2) {?>';
        $this->_template_replace[] = '<?php if (\\1){ ?>';
        $this->_template_replace[] = '<?php }else{ ?>';
        $this->_template_replace[] = '<?php \\3; ?>';
        $this->_template_replace[] = '<?php echo \\1; ?>';
        $this->_template_replace[] = '<?php print_r(\\1); ?>';
        $this->_template_replace[] = '<?php } ?>';
        $this->_template_replace[] = '<?php echo \\1;?>';
        $this->_template_replace[] = '';
        $this->_template_replace[] = '';
        $this->_template_replace[] = '<?php echo $this->fetch(\\1);?>';
        $this->_template_replace[] = '<?php echo $this->url->site(\\1);?>';
        $this->_template_replace[] = '<?php echo $this->url->base(\\1);?>';
        $this->_template_replace[] = '<?php echo \feros::run_time();?>';
        $this->_template_replace[] = '<?php echo $this->get_runtime();?>';
        $this->_template_replace[] = '<?php echo $this->language->\\1;?>';
    }

    private function compile_php() {
        if (!$this->config->view->php_off) {
            $this->_template_preg[] = '/<\?(=|php|)(.+?)\?>/is';
            $this->_template_replace[] = '&lt;?\\1\\2?&gt;';
        } else {
            $this->_template_preg[] = '/(<\?(?!php|=|$))/i';
            $this->_template_replace[] = '<?php echo \'\\1\'; ?>';
        }
    }

    private function compile_xml() {
        $this->_template_preg[] = "/<\?xml(.*?)\?>/s";
        $this->_template_replace[] = "##XML\\1XML##";
    }

    private function compile_html() {
        $this->_template_preg[] = '/' . $this->__ldel . 'css (.*?)( .*?)?' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'js (.*?)( .*?)?' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'ico (.*?)( .*?)?' . $this->__rdel . '/';
        $this->_template_preg[] = '/' . $this->__ldel . 'php (.*?)' . $this->__rdel . '/';
        $this->_template_replace[] = '<link rel="stylesheet" type="text/css" href="<?php echo $this->url->base(\\1);?>" \\2/>';
        $this->_template_replace[] = '<script type="text/javascript" src="<?php echo $this->url->base(\\1);?>" \\2></script>';
        $this->_template_replace[] = '<link rel="shortcut icon" href="<?php echo $this->url->base(\\1);?>" />';
        $this->_template_replace[] = '<?php require_once("\\1"); ?>';
    }

}
