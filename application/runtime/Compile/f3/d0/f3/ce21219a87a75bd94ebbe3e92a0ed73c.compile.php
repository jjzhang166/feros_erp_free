<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/warehousing_queries.html
*/
?><?php if ($this->t_verify->is_post() && $this->t_verify->is_ajax()){ ?>
<?php if (empty($list)){ ?>
<div class="alert alert-block">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h4>Warning!</h4>
    暂时没有相关数据
</div>
<?php }else{ ?>
<?php if (empty($chart)){ ?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th colspan="7" style="text-align:center">仓库信息</th>
            <th colspan="6" style="text-align:center">产品信息</th>
        </tr>
        <tr>
            <th>ID</th>
            <th>仓库</th>
            <th>入库</th>
            <th>库存</th>
            <th>单位</th>
            <th>入库日期</th>
            <th>入库人</th>
            <th>识别码</th>
            <th>产品名称</th>
            <th>产品类型</th>
            <th>产品分类</th>
            <th>供应商</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo sprintf("%06d",$var["w_id"]);?></td>
            <td><?php echo $var["w_name"];?></td>
            <td><?php echo $var["number"];?></td>
            <td><?php echo $var["quantity"];?></td>
            <td><?php echo $var["unit"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["time"]));?></td>
            <td><?php echo $var["w_members"];?></td>
            <td><?php echo $var["code"];?></td>
            <td><?php echo $var["name"];?></td>
            <td><?php echo $var["type"]==='1'?'正常':'赠品';?></td>
            <td><?php echo $var["category"];?></td>
            <td><?php echo $var["company"];?></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_look/'.$var["id"]);?>" title="查看产品"><i class="icon-search"></i> 查看</a></td>
        </tr>
        <?php } ?>

    </tbody>
</table>
<?php }else{ ?>
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th style="width:20px"></th>
            <th>编号</th>
            <th>入库数量</th>
            <th>入库日期</th>
            <th>入库人</th>
            <th>供应商</th>
            <th>备注</th>
            <th style="width:50px;text-align:center">打印</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
    <script type="text/javascript">
        function product_data(id){
        $('.product_data').hide(); $('.product_dataplus').html('<i class=\'icon-plus\'></i>'); $('#product_data' + id).fadeIn(); $('#product_dataplus' + id).html('<i class=\'icon-minus\'></i>');
        }
    </script>
    <tr>
        <td onclick="product_data('<?php echo $var["id"];?>')" class="product_dataplus" id="product_dataplus<?php echo $var["id"];?>"><i class="icon-plus"></i></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["number"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["quantity"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo array_shift($this->t_date->get_lastdate((int)$var["time"]));?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["realname"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["company"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')" title="<?php echo $var["remark"];?>"><?php echo $this->t_string->msubstr($var["remark"],0,5);?></td>
         <td style="text-align:center"><a href="javascript:;" onclick="PrintBarCodeNoBorderTable('<?php echo $this->url->site('prints/warehousing_queries_s/'.$var["id"].'?session_id='.session_id());?>','打印入库<?php echo $var["id"];?>','128Auto',75, 60, 300, 40,'<?php echo $var["number"];?>')" title="打印"><i class="icon-print"></i> 打印</a></td>
        
    </tr>
    <tr id="product_data<?php echo $var["id"];?>" class="product_data" style="display:none">
        <td colspan="8">
            <table class="table table-hover table-striped table-bordered" style="margin-bottom:0px">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>仓库</th>
                        <th>入库</th>
                        <th>库存</th>
                        <th>单位</th>

                        <th>识别码</th>
                        <th>产品名称</th>
                        <th>产品类型</th>
                        <th>产品分类</th>
                        <th style="width:50px;text-align:center">查看</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->m_product->pr_where(null,false)->group('w.id')->where(array('w.o_id'=>$var["id"]))->finds() as $var2) {?>
                    <tr>
                        <td><?php echo sprintf("%06d",$var2["w_id"]);?></td>
                        <td><?php echo $var2["w_name"];?></td>
                        <td><?php echo $var2["number"];?></td>
                        <td><?php echo $var2["quantity"];?></td>
                        <td><?php echo $var2["unit"];?></td>
                        <td><?php echo $var2["code"];?></td>
                        <td><?php echo $var2["name"];?></td>
                        <td><?php echo $var2["type"]==='1'?'正常':'赠品';?></td>
                        <td><?php echo $var2["category"];?></td>
                        <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_look/'.$var2["id"]);?>" title="查看产品"><i class="icon-search"></i> 查看</a></td>
                    </tr>
                    <?php } ?>

                </tbody>
            </table>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
<?php } ?>
<?php } ?>
<?php }else{ ?>
<?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/warehousing_queries/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <input type="text" placeholder="ID/识别码/产品名称" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">

                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timea" value="<?php echo $_REQUEST["timea"];?>" placeholder="创建开始日期">
                            <i class="icon-resize-horizontal"></i>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timeb" value="<?php echo $_REQUEST["timeb"];?>" placeholder="创建结束日期">


<div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" onclick="$('#chartinput').val(0); $('form').submit()" class="btn<?php echo empty($chart)?' active':'';?>"><i class="icon-table"></i> 产品列表</button>
                                <button type="button" onclick="$('#chartinput').val(1); $('form').submit()" class="btn<?php echo $chart==='1'?' active':'';?>"><i class="icon-list-alt"></i> 入库列表</button>
                            </div>
                            <input type="hidden" id="chartinput" name="chart" value="<?php echo $chart;?>" />

                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                            <button class="btn" type="button" onclick="$('#more_search').fadeToggle(); $('#financequerysearch').val($('#financequerysearch').val() == 1?0:1)"><i class="icon-rss"></i> 更多搜索</button>
<?php if (empty($chart)){ ?>
                            <button type="button" class="btn" title="打印" onclick="PrintNoBorderTable('<?php echo $this->url->site('prints/warehousing_queries/'.'?session_id='.session_id());?>&'+$('form').serialize(),'打印入库单')"><i class="icon-print"></i> 打印查询结果</button>
<?php } ?>
                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                    <tr id="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <input type="text" class="input-mini" name="lowesta" value="<?php echo $_REQUEST["lowesta"];?>" placeholder="库存">
                            <i class="icon-resize-horizontal"></i>
                            <input type="text" class="input-mini" name="lowestb" value="<?php echo $_REQUEST["lowestb"];?>" placeholder="库存">
                            <input type="hidden" name="financequerysearch" id="financequerysearch" value="<?php echo $_REQUEST["financequerysearch"]?:0;?>" />
                            <select name="warehouse" class="input-medium">
                                <option value="">所有仓库</option>
                                <?php foreach ($warehouse as $var) {?>
                                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
                                <?php } ?>
                            </select>
                            <select name="supplier" class="input-large">
                                <option value="">所有供应商</option>
                                <?php foreach ($supplier as $var) {?>
                                <option value="<?php echo $var["id"];?>"<?php echo $_REQUEST["supplier"]===$var["id"]?' selected':'';?>><?php echo $var["company"];?></option>
                                <?php } ?>
                            </select>
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个入库记录</small>
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