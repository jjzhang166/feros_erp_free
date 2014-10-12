<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/staff_add.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <form class="form-horizontal" action="<?php echo $this->url->site('system/staff_add');?>" accept-charset="utf-8" method="post">
            <div id="legend" class="">
                <legend class="">新增员工</legend>
            </div>


            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#home" data-toggle="tab">基本资料</a></li>
                <li><a href="#profile" data-toggle="tab">员工权限</a></li>
            </ul>
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active" id="home">
                    <table class="table table-hover">
                        <tr>
                            <th style="line-height:30px;text-align:right">员工部门<font color="#ff0000">*</font></th>
                            <td>
                                <select name="c_id" class="input-xlarge" required>
                                    <option value="">请选择部门</option>
                                    <?php foreach ($list as $var) {?>
                                    <option value="<?php echo $var["id"];?>"><?php echo $var["name"];?></option>
                                    <?php } ?>
                                </select>   
                            </td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">员工姓名<font color="#ff0000">*</font></th>
                            <td><input type="text" name="realname" placeholder="" class="input-xlarge" required></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">登录账号<font color="#ff0000">*</font></th>
                            <td><input type="text" name="username" placeholder="" class="input-xlarge" required></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">登录密码<font color="#ff0000">*</font></th>
                            <td><input type="password" name="password" placeholder="" class="input-xlarge" required></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">电子邮箱</th>
                            <td><input type="email" name="email" placeholder="" class="input-xlarge"></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">手机号码</th>
                            <td><input type="text" name="mobile" placeholder="" class="input-xlarge"></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">基本工资</th>
                            <td><input type="text" name="wage" value="" placeholder="" class="input-xlarge"></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">备注</th>
                            <td><textarea name="remark" type="" class="input-xlarge" style="height:60px"></textarea></td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right">员工状态</th>
                            <td>
                                <?php foreach ($this->m_staff->status() as $key=>$var) {?>
                                <label class="radio inline">
                                    <input type="radio" value="<?php echo $key;?>" name="status"<?php echo $key=='1'?' checked="checked"':'';?>>
                                    <?php echo $var;?>
                                </label>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="line-height:30px;text-align:right"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></th>
                            <td>
                                <button type="submit" class="btn btn-primary input-small"><i class="icon-save"></i> 保存</button> <button type="reset" class="btn input-small"><i class="icon-refresh"></i> 重置</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="tab-pane fade" id="profile">

                    <?php foreach ($menu as $var) {?>
                    <div id="legend" class="">
                        <legend class=""><h5><?php echo $var["name"];?></h5></legend>
                    </div>
                    <?php foreach ($var["s"] as $var2) {?>
                    <div class="control-group">
                        <label class="control-label"><strong><?php echo $var2["name"];?></strong></label>
                        <div class="controls">
                            <?php foreach ($var2["s"] as $var3) {?>
                            <label class="checkbox inline"><input type="checkbox" name="menu[]" value="<?php echo $var3["id"];?>"><?php echo $var3["name"];?></label>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>

        </form>
    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function () {
            post_sisyphus()
        });
    </script>
</body>
</html>
