<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/base.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">

        <form class="form-horizontal" action="<?php echo $this->url->site('system/base');?>" accept-charset="utf-8" method="post">
            <div id="legend" class="">
                <legend class="">基础设置</legend>
            </div>
            <table class="table table-hover">
                <tr>
                    <th style="width:20%;line-height:30px;text-align:right">公司名称</th>
                    <td><input type="text" name="company" value="<?php echo $var["company"];?>" placeholder="输入公司名称" class="input-xlarge" required></td>
                </tr>
                <tr>
                    <th style="width:20%;line-height:30px;text-align:right">系统标题</th>
                    <td><input type="text" name="title" value="<?php echo $var["title"];?>" placeholder="输入系统标题" class="input-xlarge" required></td>
                </tr>
                <tr>
                    <th style="width:20%;line-height:30px;text-align:right">会员生日提前多少天提醒</th>
                    <td><input type="text" name="birthday" value="<?php echo $var["birthday"]?:2;?>" placeholder="输入消费积分" class="input-small" required></td>
                </tr>
                <tr>
                    <th style="width:20%;line-height:30px;text-align:right">1元消费兑换积分</th>
                    <td><input type="text" name="points" value="<?php echo $var["points"];?>" placeholder="输入消费积分" class="input-small"></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">允许变更会员积分</th>
                    <td>
                        <input type="checkbox" value="1" name="allowpoints"<?php echo $var["allowpoints"]?' checked':'';?>/>
                    </td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">每页查询多少条记录</th>
                    <td><input type="text" name="queqry" value="<?php echo $var["queqry"]?:12;?>" placeholder="" class="input-small" required></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">默认查询多少天数据</th>
                    <td><input type="text" name="days" value="<?php echo $var["days"]?:30;?>" placeholder="" class="input-small" required></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">权限局域网</th>
                    <td>
                        <input type="checkbox" value="1" name="LAN"<?php echo $var["LAN"]?' checked':'';?>/>

                    </td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">登录验证码</th>
                    <td>
                        <input type="checkbox" value="1" name="loginqrcode"<?php echo $var["loginqrcode"]?' checked':'';?>/>

                    </td>
                </tr>
                <tr>
                    <th style="width:20%;line-height:30px;text-align:right">桌面消息</th>
                    <td><input type="text" name="desktop_news" value="<?php echo $var["desktop_news"];?>" placeholder="" class="input-xlarge"></td>
                </tr>



                <tr>
                    <th style="line-height:30px;text-align:right"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></th>
                    <td>
                        <button type="submit" class="btn btn-primary input-small"><i class="icon-save"></i> 保存</button> <button type="reset" class="btn input-small"><i class="icon-refresh"></i> 重置</button>    </td>
                </tr>
            </table>
        </form>

    </div>
    <?php echo $this->fetch('public/js');?>
</body>
</html>
