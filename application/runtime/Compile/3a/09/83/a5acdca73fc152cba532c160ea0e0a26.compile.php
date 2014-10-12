<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/staff.html
*/
?><?php if ($this->t_verify->is_post() && $this->t_verify->is_ajax()){ ?>
<?php if (empty($list)){ ?>
<div class="alert alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4>Warning!</h4>
    暂时没有相关数据
</div>
<?php }else{ ?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>姓名</th>
            <th>状态</th>
            <th>部门</th>
            <th>账号</th>
            <th>邮箱</th>
            <th>手机</th>
            <th style="width:50px;text-align:center">删除</th>
            <th style="width:50px;text-align:center">修改</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo $var["realname"];?></td>
            <td><?php echo $this->m_staff->status($var["status"]);?></td>
            <td><?php echo $var["category"];?></td>
            <td><?php echo $var["username"];?></td>
            <td><?php echo $var["email"];?></td>
            <td><?php echo $var["mobile"];?></td>
            <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复，为了安全，其日志等重要信息不会删除" href="<?php echo $this->url->site('system/staff_delete/'.$var["uid"]);?>"><i class="icon-remove"></i> 删除</a></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('system/staff_edit/'.$var["uid"]);?>"  title="修改供应商"><i class="icon-edit"></i> 修改</a></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('system/staff_look/'.$var["uid"]);?>" title="查看供应商"><i class="icon-search"></i> 查看</a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php }else{ ?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li>首字母检索 <span class="divider">/</span></li>
            <?php foreach ($pinyin as $var) {?>
            <li<?php echo $py===$var?' class="active"':'';?>><?php if ($py===$var){ ?><?php echo $var;?><?php }else{ ?><a href="<?php echo $this->url->site('system/staff/'.$var);?>"><?php echo $var;?></a><?php } ?><span class="divider">/</span></li>
            <?php } ?>
            <li<?php echo empty($py)?' class="active"':'';?>><?php if (empty($py)){ ?>全部<?php }else{ ?><a href="<?php echo $this->url->site('system/staff');?>">全部</a><?php } ?></li>
        </ul>

        <form class="form-inline" action="<?php echo $this->url->site('system/staff/'.$py);?>" accept-charset="utf-8" method="get">
            <input type="text" placeholder="姓名/账号/手机号/邮箱/首字母" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-xlarge">
            <select name="status" class="input-small">
                <?php foreach ($this->m_staff->status() as $key=>$var) {?>
                <option value="<?php echo $key;?>"<?php echo $_REQUEST["status"]==$key?' selected':'';?>><?php echo $var;?></option>
                <?php } ?>
            </select>

            <select name="c_id" class="input-xlarge">
                <option value="">请选择部门</option>
                <?php foreach ($category as $var) {?>
                <option value="<?php echo $var["id"];?>"<?php echo $_REQUEST["c_id"]===$var["id"]?' selected':'';?>><?php echo $var["name"];?></option>
                <?php } ?>
            </select>


            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>

            <input type="hidden" id="paginationinput" name="pagination" value="1" />
            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
        </form>

        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个数据</small>
        </p>
        <div id="tablelist">

        </div>
        <div class="pagination pagination-centered">
            <ul id='pagination'>

            </ul>
        </div>


    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function() {
        <?php if (!empty($count)){ ?>
        $('#pagination').jqPaginator({
        totalCounts: <?php echo $count;?>,
                pageSize:<?php echo $base["queqry"];?>,
                currentPage: 1,
                onPageChange: function(num, type) {
                $('#paginationinput').val(num);
                        $.post($('#formlog_operate').attr('action'), $('#formlog_operate').serialize(), function(data) {
                        $('#tablelist').html(data);
                        });
                }
        });
        <?php } ?>
        });
    </script>
</body>
</html>
<?php } ?>