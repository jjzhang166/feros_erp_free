<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/home/log.html
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
            <th>动作</th>
            <th>操作人</th>
            <th>状态</th>
            <th>客户端</th>
            <th>时间</th>
            <th>IP</th>
            <th>地址</th>
            <th>URL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo $var["title"];?></td>
            <td><?php echo $var["realname"];?></td>
            <td><?php echo $var["status"]?'<span class="label label-success">成功</span>':'<span class="label label-important">失败</span>';?></td>
            <td><?php echo $var["client"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["time"]));?></td>
            <td>演示不显示</td>
            <td><?php echo $var["country"];?></td>
            <td>演示不显示</td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php }else{ ?>
<?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        
        <form id="formlog_operate" class="form-inline" action="<?php echo $this->url->site('home/log/'.$py);?>" accept-charset="utf-8" method="get">
            
            <input type="text" placeholder="动作/IP/地址" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">

            <input style="width:110px" type="text" class=" input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timea" value="<?php echo $_REQUEST["timea"];?>" placeholder="开始日期">

            <i class="icon-resize-horizontal"></i>
            <input style="width:110px" type="text" class=" input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timeb" value="<?php echo $_REQUEST["timeb"];?>" placeholder="结束日期">


           
         


            <div class="btn-group" data-toggle="buttons-radio">
                <button type="button" onclick="$('#statusinput').val(1)" class="btn<?php echo $_REQUEST["status"]=='1'?' active':'';?>">成功</button>
                <button type="button" onclick="$('#statusinput').val(0)" class="btn<?php echo $_REQUEST["status"]=='0'?' active':'';?>">失败</button>
                <button type="button" onclick="$('#statusinput').val('')" class="btn<?php echo $_REQUEST["status"]==''?' active':'';?>">全部</button>
            </div>
            <button type="submit" class="btn btn-primary input-small" title="查询银行"><i class="icon-search"></i> 搜索</button>
            <input type="hidden" id="paginationinput" name="pagination" value="1" />
            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
            <input type="hidden" id="statusinput" name="status" value="<?php echo $_REQUEST["status"];?>" />
        </form>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个日志</small>
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
    <script type="text/javascript">
        $(document).ready(function () {
           
        });
    </script>
</body>
</html>
<?php } ?>