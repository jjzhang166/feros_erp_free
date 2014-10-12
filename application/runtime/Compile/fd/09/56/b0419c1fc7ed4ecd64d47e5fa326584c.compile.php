<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/login/patternlock.html
*/
?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo $base["company"];?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/toastr.min.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/patternLock.css');?>" />
        <style>*{color:#fff}body{padding-top:12px;font-family:"ff-tisa-web-pro-1","ff-tisa-web-pro-2","Lucida Grande","Helvetica Neue",Helvetica,Arial,"Hiragino Sans GB","Hiragino Sans GB W3","Microsoft YaHei UI","Microsoft YaHei","WenQuanYi Micro Hei",sans-serif;}
        </style>
    </head>
    <body style="background-color: #3382c0;">
        <table width="100%" height="100%" border="0" align="center" cellpadding="0" cellspacing="0">

            <tr>
                <td>
                    <table width="320" height="320" border="0" align="center" cellpadding="0" cellspacing="0">
                        <tr>
                            <td> <div id="patternContainer"></div></td>
                        </tr>
                    </table>
                </td>

            </tr>
            <tr>
                <td align="center">
                    请绘制手势解锁
                    或<a href="<?php echo $this->url->site('login/quit');?>" onclick="return confirm('你确定要退出系统吗？')" title="退出系统" class="dl-log-quit">[退出]</a>
                </td>

            </tr>
        </table>







        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/jquery-1.8.1.min.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/bootstrap/js/bootstrap.min.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/toastr.min.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/patternLock.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/common.js');?>" ></script>
        <script type="text/javascript">


            $(document).ready(function () {
                var lock = new PatternLock("#patternContainer", {
                    mapper: function (idx) {
                        return (idx % 9);
                    }, onDraw: function (pattern) {
                        $.post("<?php echo $this->url->site('login/lock');?>", {pattern: pattern}, function (data) {
                            if (data.status === 1) {

                                if (data.url !== '') {
                                    url = data.url;
                                } else {
                                    url = location.href;
                                }
                                self.location = url
                            }
                            else
                            if (data.status === 0) {
                                 window.parent.toastr.error(data.message, '操作失败');
                            }
                        }, "json");
                    }
                });
            });
        </script>

    </body>
</html>
