<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/product.html
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
            <th>识别码</th>
            <th>名称</th>
            <th>销售价</th>
            <th>兑换积分</th>

            <th>库存</th>
            <th>仓库</th>

            <th>类型</th>
            <th>分类</th>
            <th>创建日期</th>
            <th>更新日期</th>
            <th style="width:50px;text-align:center">修改</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo $var["code"];?></td>
            <td><?php echo $var["name"];?></td>

            <td><?php echo $var["sales"];?></td>
            <td><?php echo $var["points"];?></td>

            <td><?php echo $var["quantity"];?><?php echo $var["unit"];?></td>
            <td><?php echo $var["warehouse"];?></td>

            <td><?php echo $var["type"]==='1'?'正常':'赠品';?></td>
            <td><?php echo $var["category"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["create"]));?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["update"]));?></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_edit/'.$var["id"]);?>"  title="修改产品"><i class="icon-edit"></i> 修改</a></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_look/'.$var["id"]);?>" title="查看产品"><i class="icon-search"></i> 查看</a></td>
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
        <ul class="breadcrumb">
            <li>产品名称首字母检索 <span class="divider">/</span></li>
            <?php foreach ($pinyin as $var) {?>
            <li<?php echo $py===$var?' class="active"':'';?>><?php if ($py===$var){ ?><?php echo $var;?><?php }else{ ?><a href="<?php echo $this->url->site('inventory/product/'.$var);?>"><?php echo $var;?></a><?php } ?><span class="divider">/</span></li>
            <?php } ?>
            <li<?php echo empty($py)?' class="active"':'';?>><?php if (empty($py)){ ?>全部<?php }else{ ?><a href="<?php echo $this->url->site('inventory/product');?>">全部</a><?php } ?></li>
        </ul>
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/product/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <input type="text" placeholder="识别码/产品名称" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">

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
                            <select name="staff_uid" class="input-small">
                                <option value="">操作人</option>
                                <?php foreach ($this->m_staff->finds() as $staff) {?>
                                <option value="<?php echo $staff["uid"];?>"<?php echo $_REQUEST["staff_uid"]===$staff["uid"]?' selected':'';?>><?php echo $staff["realname"];?></option>
                                <?php } ?>
                            </select>



                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                           

                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个产品记录</small>
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