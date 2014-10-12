<?php

/**
 * FerOS PHP Framework
 * @author feros<admin@feros.com.cn>
 * @copyright ©2014 feros.com.cn
 * @link http://www.feros.com.cn
 * @version 2.0
 */
//1.匹配正整数：/^[1-9]\d*$/
//2.匹配非负整数（正整数+0）：/^\d+$/
//3.匹配中文：/^[\x{4e00}-\x{9fa5}]+$/u
//4.匹配Email：/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/
//5.匹配网址URL：(((f|ht){1}(tp|tps)://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)
//6.匹配字母开头，5-16字符，字母数字下划线：/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/
//7.匹配数字,字母,下划线,中文：/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u
//8.匹配中国邮政编码：/^[1-9]\d{5}$/
//9.匹配IP地址：/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/
//10.匹配中国大陆身份证：/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/

namespace tool;

use base\common;

/**
 * 验证类
 * @author sanliang
 */
class verify extends common {

    /**
     * 是来为GET方式请求
     * @return boolean
     */
    public function is_get() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'GET');
    }

    /**
     * 是来为HTTPS方式请求
     * @return boolean
     */
    public function is_ssl() {
        return(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) || isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] ) ? TRUE : FALSE;
    }

    /**
     * 是来为POST方式请求
     * @return boolean
     */
    public function is_post() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'POST');
    }

    /**
     * 是来为PUT方式请求
     * @return boolean
     */
    public function is_put() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT');
    }

    /**
     * 是来为DELETE方式请求
     * @return boolean
     */
    public function is_delete() {
        return isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE');
    }

    /**
     * 是来为ajax方式请求
     * @return boolean
     */
    public function is_ajax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * 是来为flash方式请求
     * @return boolean
     */
    public function is_flash() {
        return isset($_SERVER['HTTP_USER_AGENT']) && (stripos($_SERVER['HTTP_USER_AGENT'], 'Shockwave') !== FALSE || stripos($_SERVER['HTTP_USER_AGENT'], 'Flash') !== FALSE);
    }

    /**
     * 是否为局域网
     * @param int $ip
     * @return boolean
     */
    public function is_ipprivate($ip = NULL) {
        $i = explode('.', ($ip? : $this->t_client->ip()));
        return ($i[0] == 10) ||($i[0] ==127) || ($i[0] == 172 && $i[1] > 15 && $i[1] < 32) || ($i[0] == 192 && $i[1] == 168) ? true : false;
    }

    /**
     * 判断是否是通过手机访问
     * @return bool
     */
    public function is_mobile() {
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            return TRUE;
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'opera mobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", $user_agent) && strpos($user_agent, 'ipad') == 0)
                return TRUE;
        }
        return FALSE;
    }

    /**
     * 检查字符串是否是UTF8编码
     * @param string $string 字符串
     * @return boolean
     */
    public function is_utf8($string) {
        $c = 0;
        $b = 0;
        $bits = 0;
        $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $c = ord($string[$i]);
            if ($c > 128) {
                if (($c >= 254))
                    return FALSE;
                elseif ($c >= 252)
                    $bits = 6;
                elseif ($c >= 248)
                    $bits = 5;
                elseif ($c >= 240)
                    $bits = 4;
                elseif ($c >= 224)
                    $bits = 3;
                elseif ($c >= 192)
                    $bits = 2;
                else
                    return FALSE;
                if (($i + $bits) > $len)
                    return FALSE;
                while ($bits > 1) {
                    $i++;
                    $b = ord($string[$i]);
                    if ($b < 128 || $b > 191)
                        return FALSE;
                    $bits--;
                }
            }
        }
        return TRUE;
    }

    /**
     * 是否是帐号
     * @param string $account
     * @return boolean
     */
    public static function is_account($account) {
        return (boolean) preg_match('/^[A-Za-z][A-Za-z0-9_\-]{2,20}$/', $account);
    }

    /**
     * 验证是否是电话号码
     * 国际区号-地区号-电话号码的格式（在国际区号前可以有前导0和前导+号），
     * 国际区号支持0-4位
     * 地区号支持0-6位
     * 电话号码支持4到12位
     * @param string $phone 被验证的电话号码
     * @return boolean 如果验证通过则返回TRUE，否则返回FALSE
     */
    public function is_telphone($phone) {
        return (boolean) preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{0,6}[\-\s]?\d{4,12}$/', $phone);
    }

    /**
     * 验证是否是手机号码
     * 国际区号-手机号码
     * @param string $number 待验证的号码
     * @return boolean 如果验证失败返回FALSE,验证成功返回TRUE
     */
    public function is_telnumber($number) {
        return (boolean) preg_match('/^\+?[0\s]*[\d]{0,4}[\-\s]?\d{4,12}$/', $number);
    }

    /**
     * 验证是否是QQ号码
     * QQ号码必须是以1-9的数字开头，并且长度5-15为的数字串
     * @param string $qq 待验证的qq号码
     * @return boolean 如果验证成功返回TRUE，否则返回FALSE
     */
    public function is_qq($qq) {
        return (boolean) preg_match('/^[1-9]\d{4,14}$/', $qq);
    }

    /**
     * 验证是否是邮政编码
     * 邮政编码是4-8个长度的数字串
     * @param string $zipcode 待验证的邮编
     * @return boolean 如果验证成功返回TRUE，否则返回FALSE
     */
    public function is_zipcode($zipcode) {
        return (boolean) preg_match('/^\d{4,8}$/', $zipcode);
    }

    /**
     * 验证是否是有合法的email
     * 
     * @param string $string  被搜索的 字符串
     * @param array $matches  会被搜索的结果,默认为array()
     * @param boolean $ifAll  是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_email($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $string);
    }

    /**
     * 验证是否是合法的email
     * @param string $string 待验证的字串
     * @return boolean 如果是email则返回TRUE，否则返回FALSE
     */
    public function is_email($string) {
        return (boolean) preg_match("/^\w+(?:[-+.']\w+)*@\w+(?:[-.]\w+)*\.\w+(?:[-.]\w+)*$/", $string);
    }

    /**
     * 验证是否有合法的身份证号
     * 
     * @param string $string  被搜索的 字符串
     * @param array $matches  会被搜索的结果,默认为array()
     * @param boolean $ifAll  是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_idcard($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp("/\d{17}[\d|X]|\d{15}/", $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的身份证号
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的身份证号则返回TRUE，否则返回FALSE
     */
    public function is_idcard($string) {
        return is_string($string) && $this->t_ids->check($string);
    }

    /**
     * 验证是否有合法的URL
     * @param string $string  被搜索的 字符串
     * @param array $matches  会被搜索的结果,默认为array()
     * @param boolean $ifAll  是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_url($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的url
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的url则返回TRUE，否则返回FALSE
     */
    public function is_url($string) {
        return (boolean) preg_match('/^(?:http(?:s)?:\/\/(?:[\w-]+\.)+[\w-]+(?:\:\d+)*+(?:\/[\w- .\/?%&=]*)?)$/', $string);
    }

    /**
     * 验证是否有中文
     * @param string $string  被搜索的 字符串
     * @param array $matches  会被搜索的结果,默认为array()
     * @param boolean $ifAll  是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_chinese($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/[\x{4e00}-\x{9fa5}]+/u', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是中文
     * @param string $string 待验证的字串
     * @return boolean 如果是中文则返回TRUE，否则返回FALSE
     */
    public function is_chinese($string) {
        return (boolean) preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $string);
    }

    /**
     * 验证是否有html标记
     * @param string $string  被搜索的 字符串
     * @param array $matches  会被搜索的结果,默认为array()
     * @param boolean $ifAll  是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_html($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/<(.*)>.*|<(.*)\/>/', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的html标记
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的html标记则返回TRUE，否则返回FALSE
     */
    public function is_html($string) {
        return (boolean) preg_match('/^<(.*)>.*|<(.*)\/>$/', $string);
    }

    /**
     * 验证是否有合法的ipv4地址
     * @param string $string   被搜索的 字符串
     * @param array $matches   会被搜索的结果,默认为array()
     * @param boolean $ifAll   是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_ipv4($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/((25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的IP
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的IP则返回TRUE，否则返回FALSE
     */
    public function is_ipv4($string) {
        return (boolean) preg_match('/(?:(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)\.){3}(?:25[0-5]|2[0-4]\d|1\d{2}|0?[1-9]\d|0?0?\d)/', $string);
    }

    /**
     * 验证是否有合法的ipV6
     * @param string $string 被搜索的 字符串
     * @param array $matches 会被搜索的结果,默认为array()
     * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_ipv6($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/\A((([a-f0-9]{1,4}:){6}|
										::([a-f0-9]{1,4}:){5}|
										([a-f0-9]{1,4})?::([a-f0-9]{1,4}:){4}|
										(([a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::([a-f0-9]{1,4}:){3}|
										(([a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::([a-f0-9]{1,4}:){2}|
										(([a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:|
										(([a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
									)([a-f0-9]{1,4}:[a-f0-9]{1,4}|
										(([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
										([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
									)|((([a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}|
										(([a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
									)
								)\Z/ix', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的ipV6
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的ipV6则返回TRUE，否则返回FALSE
     */
    public function is_ipv6($string) {
        return (boolean) preg_match('/\A(?:(?:(?:[a-f0-9]{1,4}:){6}|
										::(?:[a-f0-9]{1,4}:){5}|
										(?:[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){4}|
										(?:(?:[a-f0-9]{1,4}:){0,1}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){3}|
										(?:(?:[a-f0-9]{1,4}:){0,2}[a-f0-9]{1,4})?::(?:[a-f0-9]{1,4}:){2}|
										(?:(?:[a-f0-9]{1,4}:){0,3}[a-f0-9]{1,4})?::[a-f0-9]{1,4}:|
										(?:(?:[a-f0-9]{1,4}:){0,4}[a-f0-9]{1,4})?::
									)(?:[a-f0-9]{1,4}:[a-f0-9]{1,4}|
										(?:(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}
										(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])
									)|(?:(?:(?:[a-f0-9]{1,4}:){0,5}[a-f0-9]{1,4})?::[a-f0-9]{1,4}|
										(?:(?:[a-f0-9]{1,4}:){0,6}[a-f0-9]{1,4})?::
									)
								)\Z/ix', $string);
    }

    /**
     * 验证是否有客户端脚本
     * @param string $string 被搜索的 字符串
     * @param array $matches 会被搜索的结果,默认为array()
     * @param boolean $ifAll 是否进行全局正则表达式匹配，默认为FALSE即仅进行一次匹配
     * @return boolean 如果匹配成功返回TRUE，否则返回FALSE
     */
    public function has_script($string, &$matches = array(), $ifAll = FALSE) {
        return (boolean) $this->validate_regexp('/<script(.*?)>([^\x00]*?)<\/script>/', $string, $matches, $ifAll);
    }

    /**
     * 验证是否是合法的客户端脚本
     * @param string $string 待验证的字串
     * @return boolean 如果是合法的客户端脚本则返回TRUE，否则返回FALSE
     */
    public function is_script($string) {
        return (boolean) preg_match('/<script(?:.*?)>(?:[^\x00]*?)<\/script>/', $string);
    }

    /**
     * 验证是否是非负数
     * @param int $number 需要被验证的数字 
     * @return boolean 如果大于等于0的整数数字返回TRUE，否则返回FALSE
     */
    public function is_nonnegative($number) {
        return is_numeric($number) && 0 <= $number;
    }

    /**
     * 验证是否是正数
     * @param int $number 需要被验证的数字
     * @return boolean 如果数字大于0则返回TRUE否则返回FALSE
     */
    public function is_positive($number) {
        return is_numeric($number) && 0 < $number;
    }

    /**
     * 验证是否是负数
     * @param int $number  需要被验证的数字
     * @return boolean 如果数字小于于0则返回TRUE否则返回FALSE
     */
    public function is_negative($number) {
        return is_numeric($number) && 0 > $number;
    }

    /**
     * 验证是否是不能为空
     * @param mixed $value 待判断的数据
     * @return boolean 如果为空则返回FALSE,不为空返回TRUE
     */
    public function is_required($value) {
        return !empty($value);
    }

    /**
     * 验证字符串的长度
     * @param string $string 要验证的字符串
     * @param string $length 指定的合法的长度
     * @param string $charset 字符编码默认为utf-8编码
     * @return boolean 如果长度大于给定的长度则返回TRUE，否则返回FALSE
     */
    public function is_length($string, $length, $charset = 'utf8') {
        return $this->t_string->strlen($string, $charset) > (int) $length;
    }

    /**
     * 在 $string 字符串中搜索与 $regExp 给出的正则表达式相匹配的内容。
     * @param string $regExp  搜索的规则(正则) 
     * @param string $string  被搜索的 字符串
     * @param array $matches 会被搜索的结果，默认为array()
     * @param boolean $ifAll   是否进行全局正则表达式匹配，默认为FALSE不进行完全匹配
     * @return int 返回匹配的次数
     */
    private function validate_regexp($regExp, $string, &$matches = array(), $ifAll = FALSE) {
        return $ifAll ? preg_match_all($regExp, $string, $matches) : preg_match($regExp, $string, $matches);
    }

}
