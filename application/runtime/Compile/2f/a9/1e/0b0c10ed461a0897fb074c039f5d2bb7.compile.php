<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/index/index.html
*/
?><!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $base["company"];?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/bootstrap/css/scojs.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/dpl-min.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/bui-min.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/main-min.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/toastr.min.css');?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/font-awesome.min.css');?>" />
        <link rel="shortcut icon" href="<?php echo $this->url->base('favicon.ico');?>">
    </head>
    <body>

        <div class="header">

            <div class="dl-title">
                <span class="dl-title-text"><?php echo $base["title"];?></span>
            </div>

            <div class="dl-log">欢迎您，<span class="dl-log-user"><?php echo $member["realname"];?></span>
               
<?php if (!empty($member["patternlock"])){ ?>
                <a href="<?php echo $this->url->site('index/quit/lock');?>" onclick="return confirm('你确定要离开系统吗？')" title="离开系统" class="dl-log-quit"><i class="icon-reply"></i></a>
                <?php } ?>
                <a href="#" onclick="ferosClient.mini()" class="dl-log-quit"><i class="icon-minus"></i></a>

                <a href="<?php echo $this->url->site('login/quit');?>" onclick="return confirm('你确定要退出系统吗？')" title="退出系统" class="dl-log-quit"><i class="icon-remove"></i></a>

            </div>
        </div>
        <div class="content">
            <div class="dl-main-nav">
                <div class="dl-inform"><div class="dl-inform-title">费尔进销存<s class="dl-inform-icon dl-up"></s></div></div>
                <ul id="J_Nav"  class="nav-list ks-clear">
                    <?php $i=0; ?><?php foreach ($menu as $var) {?><?php if ($var["parentid"]==0){ ?><?php ++$i; ?><li class="nav-item<?php echo $i==1?' dl-selected':'';?>"><div class="nav-item-inner <?php echo $var["ico"];?>"><?php echo $var["name"];?></div></li><?php } ?><?php } ?>
                </ul>
            </div>
            <ul id="J_NavContent" class="dl-tab-conten">

            </ul>
        </div>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/jquery-2.1.1.min.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/bootstrap/js/sco.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/bui.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/toastr.min.js');?>" ></script>
        <script type="text/javascript" src="<?php echo $this->url->base('assets/js/config.js');?>" ></script>
        <script type="text/javascript">
            BUI.use('common/main', function () {
                var config=<?php echo $bui;?>;
                new PageUtil.MainPage({modulesConfig:config});
            });
            //window.setTimeout(showremind, 5000);
            function showremind() {
                $.post("<?php echo $this->url->site('index/remind');?>", function (data) {
                    if (data.status === 1) {
                        window.parent.toastr.info(data.message);
                    }
                    window.setTimeout(showremind, 5000);
                }, "json");
            }
        </script>
    </body>
</html>
