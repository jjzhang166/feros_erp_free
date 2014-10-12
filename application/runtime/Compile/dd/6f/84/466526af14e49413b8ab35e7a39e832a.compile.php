<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/supplier.html
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
            <th>供应商名称</th>
            <th>联系人</th>
            <th>电话</th>
            <th>传真</th>
            <th>地址</th>
            <th>更新日期</th>
            <th>更新人</th>
            <th style="width:50px;text-align:center">删除</th>
            <th style="width:50px;text-align:center">修改</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo $var["company"];?></td>
            <td><?php echo $var["name"];?></td>
            <td><?php echo $var["tel"];?></td>
            <td><?php echo $var["fax"];?></td>
            <td><?php echo $var["address"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["replace"]));?></td>
            <td><?php echo $var["realname_replace"];?></td>
            <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('inventory/supplier_delete/'.$var["id"]);?>"><i class="icon-remove"></i> 删除</a></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/supplier_edit/'.$var["id"]);?>"  title="修改供应商"><i class="icon-edit"></i> 修改</a></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/supplier_look/'.$var["id"]);?>" title="查看供应商"><i class="icon-search"></i> 查看</a></td>
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
            <li>供应商名称首字母检索 <span class="divider">/</span></li>
            <?php foreach ($pinyin as $var) {?>
            <li<?php echo $py===$var?' class="active"':'';?>><?php if ($py===$var){ ?><?php echo $var;?><?php }else{ ?><a href="<?php echo $this->url->site('inventory/supplier/'.$var);?>"><?php echo $var;?></a><?php } ?><span class="divider">/</span></li>
            <?php } ?>
            <li<?php echo empty($py)?' class="active"':'';?>><?php if (empty($py)){ ?>全部<?php }else{ ?><a href="<?php echo $this->url->site('inventory/supplier');?>">全部</a><?php } ?></li>
        </ul>

        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/supplier/'.$py);?>" accept-charset="utf-8" method="get">
            <input type="text" placeholder="供应商名称/联系人姓名" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-xlarge">

            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
            <a href="<?php echo $this->url->site('inventory/supplier_add');?>" title="新增供应商" class="btn"><i class="icon-plus"></i> 新增</a>
            <input type="hidden" id="paginationinput" name="pagination" value="1" />
            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
        </form>

        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个供应商</small>
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
                        $.post($('#forminventorysupplier').attr('action'), $('#forminventorysupplier').serialize(), function(data) {
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