<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/product_obsolescence.html
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
            <th colspan="5" style="text-align:center">仓库信息</th>
            <th colspan="6" style="text-align:center">产品信息</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>仓库</th>
            <th>库存</th>
            <th>单位</th>
            <th>报警</th>
            <th>识别码</th>
            <th>产品名称</th>
            <th>产品分类</th>
            <th>产品类型</th>
            <th>产品规格</th>
            <th style="width:50px;text-align:center">报废</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo sprintf("%06d",$var["inventory_id"]);?></td>
            <td><?php echo $var["w_name"];?></td>
            <td><?php echo $var["quantity"]<=$var["lowest"]?'<span class="badge badge-important">'.$var["quantity"].'</span>':$var["quantity"];?></td>
            <td><?php echo $var["unit"];?></td>
            <td><?php echo $var["lowest"];?></td>
            <td><?php echo $var["code"];?></td>
            <td><?php echo $var["name"];?></td>
            <td><?php echo $var["category"];?></td>
            <td><?php echo $var["type"]==='1'?'正常':'赠品';?></td>
            <td><?php echo $var["format"];?></td>
            <td style="text-align:center"><a data-trigger="modal" href="<?php echo $this->url->site('inventory/product_obsolescence_submit/'.$var["inventory_id"]);?>" data-title="产品报废" title="报废"><i class="icon-trash"></i> 报废</a></td>
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
        
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/product_obsolescence/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <input type="text" placeholder="识别码/产品名称" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">


                            <select name="warehouse" class="input-medium">
                                <option value="">所有仓库</option>
                                <?php foreach ($warehouse as $var) {?>
                                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
                                <?php } ?>
                            </select>

                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                            <button class="btn" type="button" onclick="$('#more_search').fadeToggle(); $('#financequerysearch').val($('#financequerysearch').val() == 1?0:1)"><i class="icon-rss"></i> 更多搜索</button>

                            

                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                    <tr id="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <input type="hidden" name="financequerysearch" id="financequerysearch" value="<?php echo $_REQUEST["financequerysearch"]?:0;?>" />
                            

                            <select name="c_id" class="input-xlarge">
                                <option value="">所有分类</option>
                                <?php foreach ($category as $var) {?>
                                <option value="<?php echo $var["id"];?>"<?php echo $_REQUEST["c_id"]===$var["id"]?' selected':'';?>><?php echo $var["name"];?></option>
                                <?php } ?>
                            </select>
                            <select name="type" class="input-small">
                                <option value="">类型</option>
                                <option value="1"<?php echo $_REQUEST["type"]==='1'?' selected':'';?>>正常</option>
                                <option value="2"<?php echo $_REQUEST["type"]==='2'?' selected':'';?>>赠品</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个库存记录</small>
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