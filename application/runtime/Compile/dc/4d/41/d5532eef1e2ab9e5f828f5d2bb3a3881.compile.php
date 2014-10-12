<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/home/password.html
*/
?><?php echo $this->fetch('public/head');?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->url->base('assets/css/patternLock.css');?>" />
</head>
<body>
    <div class="container-fluid">

        <form class="form-horizontal" action="<?php echo $this->url->site('home/password');?>" accept-charset="utf-8" method="post">
            <fieldset>
                <div id="legend" class="">
                    <legend class="">修改密码</legend>
                </div>
                <div class="control-group">

                    <!-- Text input-->
                    <label class="control-label" for="old_password">旧密码</label>
                    <div class="controls">
                        <input type="password" name="old_password" placeholder="输入旧密码" class="input-xlarge" required>

                    </div>
                </div>



                <div class="control-group">

                    <!-- Text input-->
                    <label class="control-label" for="new_password">新密码</label>
                    <div class="controls">
                        <input type="password" name="new_password" placeholder="输入新密码" class="input-xlarge" required>
                        <p class="help-block">修改成功后系统将自动关闭，重新登录即可</p>
                    </div>
                </div>

                <div class="control-group">

                    <!-- Text input-->
                    <label class="control-label" for="con_password">密码确认</label>
                    <div class="controls">
                        <input type="password" name="con_password" placeholder="再输入一次密码" class="input-xlarge" required>

                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></label>

                    <!-- Button -->
                    <div class="controls">
                        <button type="submit" class="btn btn-primary input-small"><i class="icon-save"></i> 修改</button>
                        <a href="<?php echo $this->url->site('home/password_gesture');?>"  class="btn"><i class="icon-hand-up"></i> 设置手势密码</a>
                    </div>
                </div>

            </fieldset>
        </form>

    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/js/patternLock.js');?>" ></script>
</body>
</html>