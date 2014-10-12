<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/sales_records_check.html
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
            <th colspan="7" style="text-align:center">订单信息</th>
            <th colspan="5" style="text-align:center">产品信息</th>
        </tr>
        <tr><th style="width:20px"></th>
            <th>订单编号</th>
            <th>状态</th>
            <th>金额</th>
            <th>积分</th>
            <th>创建日期</th>
            <th>会员</th>
            <th>发货日期</th>
            <th>创建人</th>
            <th>状态</th>
            <th>产品数量</th>
            <th style="width:50px;text-align:center">打印</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
    <script type="text/javascript">
        function product_data(id){
        $('.product_data').hide(); $('.product_dataplus').html('<i class=\'icon-plus\'></i>'); $('#product_data' + id).fadeIn(); $('#product_dataplus' + id).html('<i class=\'icon-minus\'></i>');
        }
    </script>
    <tr<?php echo $var["status"]==='-1'?' class="warning"':($var["status"]==='-2'?' class="error"':'');?>>
        <td onclick="product_data('<?php echo $var["id"];?>')" class="product_dataplus" id="product_dataplus<?php echo $var["id"];?>"><i class="icon-plus"></i></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["number"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $this->m_orders->get_status($var["status"]);?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["amount"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["points"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo array_shift($this->t_date->get_lastdate((int) $var["time"]));?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["realname"]?:'<span class="label label-important">没有客户</span>';?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo date('Y-m-d',$var["ship"]);?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["staff_realname"];?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["print"]==0?'<span class="label label-important">未打印</span>':'<span class="label label-success">已打印</span>';?></td>
        <td onclick="product_data('<?php echo $var["id"];?>')"><?php echo $var["count_data"];?></td>
        <td style="text-align:center"><a href="javascript:;" onclick="PrintBarCodeNoBorderTable('<?php echo $this->url->site('prints/orders/'.$var["id"].'?session_id='.session_id());?>','打印订单<?php echo $var["id"];?>','128Auto',75, 60, 300, 40,'<?php echo $var["number"];?>')" title="打印"><i class="icon-print"></i> 打印</a></td>
        <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_sales_look/'.$var["id"]);?>" title="查看记录"><i class="icon-search"></i> 查看</a></td>
    </tr>
    <tr id="product_data<?php echo $var["id"];?>" class="product_data" style="display:none">
        <td colspan="13">
            <table class="table table-hover table-striped table-bordered" style="margin-bottom:0px">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>状态</th>
                        <th>数量</th>
                        <th>退货</th>
                        <th>购买金额</th>
                        <th>购买积分</th>
                        <th>购买折扣</th>
                        <th>出库仓库</th>
                        <th>识别码</th>
                        <th>产品名称</th>
                        <th>产品单价</th>
                        <th>产品分类</th>
                        <th>产品类型</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->m_orders_data->get_data($var["id"]) as $var2) {?>
                    <?php $var2["product_data"]=unserialize($var2["product_data"]);; ?><tr>
                        <td><?php echo sprintf("%06d",$var["id"]);?></td>
                        <td><?php echo $this->m_orders_data->get_status($var2["status"]);?></td>
                        <td><?php echo $var2["quantity"];?></td>
                        <td><?php echo $var2["return"];?></td>
                        <td><?php echo $var2["amount"];?></td>
                        <td><?php echo $var2["points"];?></td>
                        <td><?php echo $var2["discounts"];?></td>
                        <td><?php echo $var2["w_name"];?></td>
                        <td><?php echo $var2["product_data"]["code"];?></td>
                        <td><?php echo $var2["product_data"]["name"];?></td>
                        <td><?php echo $var2["product_data"]["sales"];?></td>
                        <td><?php echo $var2["product_data"]["category"];?></td>
                        <td><?php echo $var2["product_data"]["type"]==='1'?'正常':'赠品';?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </td>
    </tr>
    <?php } ?>

</tbody>
</table>
<?php }else{ ?>

<table class="table table-hover table-striped table-bordered" style="margin-bottom:0px">
    <thead>
        <tr>
            <th>ID</th>
            <th>状态</th>
            <th>数量</th>
            <th>退货</th>
            <th>购买金额</th>
            <th>购买积分</th>
            <th>购买折扣</th>
            <th>出库仓库</th>
            <th>会员</th>
            <th>创建人</th>
            <th>创建时间</th>
             <th>发货日期</th>
            <th>识别码</th>
            <th>产品名称</th>
            <th>产品单价</th>
            <th>产品分类</th>
            <th>产品类型</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <?php $var["product_data"]=unserialize($var["product_data"]);; ?><tr>
            <td><?php echo sprintf("%06d",$var["id"]);?></td>
            <td><?php echo $this->m_orders_data->get_status($var["status"]);?></td>
            <td><?php echo $var["quantity"];?></td>
            <td><?php echo $var["return"];?></td>
            <td><?php echo $var["amount"];?></td>
            <td><?php echo $var["points"];?></td>
            <td><?php echo $var["discounts"];?></td>
            <td><?php echo $var["w_name"];?></td>
            <td><?php echo $var["realname"]?:'<span class="label label-important">没有客户</span>';?></td>
            <td><?php echo $var["staff_realname"];?></td>
             <td><?php echo array_shift($this->t_date->get_lastdate((int) $var["time"]));?></td>
             <td><?php echo date('Y-m-d',$var["ship"]);?></td>
            <td><?php echo $var["product_data"]["code"];?></td>
            <td><?php echo $var["product_data"]["name"];?></td>
            <td><?php echo $var["product_data"]["sales"];?></td>
            <td><?php echo $var["product_data"]["category"];?></td>
            <td><?php echo $var["product_data"]["type"]==='1'?'正常':'赠品';?></td>
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
        
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/sales_records_check/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
<input type="text" placeholder="订单号" name="number" value="<?php echo $_REQUEST["number"];?>" class="input-medium">
                            <input type="text" placeholder="识别码/产品名称" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timea" value="<?php echo $_REQUEST["timea"];?>" placeholder="创建开始日期">
                            <i class="icon-resize-horizontal"></i>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timeb" value="<?php echo $_REQUEST["timeb"];?>" placeholder="创建结束日期">

                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" onclick="$('#chartinput').val(0); $('form').submit()" class="btn<?php echo empty($chart)?' active':'';?>"><i class="icon-list-alt"></i> 订单列表</button>
                                <button type="button" onclick="$('#chartinput').val(1); $('form').submit()" class="btn<?php echo $chart==='1'?' active':'';?>"><i class="icon-table"></i> 产品列表</button>
                            </div>
                            <input type="hidden" id="chartinput" name="chart" value="<?php echo $chart;?>" />

                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                            <button class="btn" type="button" onclick="$('.more_search').fadeToggle(); $('#financequerysearch').val($('#financequerysearch').val() == 1?0:1)"><i class="icon-rss"></i> 更多搜索</button>
<?php if ($chart==='1'){ ?>
<button type="button" class="btn" title="打印" onclick="PrintNoBorderTable('<?php echo $this->url->site('prints/orders_list/'.$var["id"].'?session_id='.session_id());?>&'+$('form').serialize(),'打印销售订单')"><i class="icon-print"></i> 打印查询结果</button>
<?php } ?>
                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                    <tr class="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            
                            <input type="hidden" name="financequerysearch" id="financequerysearch" value="<?php echo $_REQUEST["financequerysearch"]?:0;?>" />
                            <input type="text" class="input-mini" name="amounta" value="<?php echo $_REQUEST["amounta"];?>" placeholder="金额">
                            <i class="icon-resize-horizontal"></i>
                            <input type="text" class="input-mini" name="amountb" value="<?php echo $_REQUEST["amountb"];?>" placeholder="金额">
                            <input type="text" class="input-mini" name="pointsa" value="<?php echo $_REQUEST["pointsa"];?>" placeholder="积分">
                            <i class="icon-resize-horizontal"></i>
                            <input type="text" class="input-mini" name="pointsb" value="<?php echo $_REQUEST["pointsb"];?>" placeholder="积分">


                            <input type="text" placeholder="会员姓名" name="realname" value="<?php echo $_REQUEST["realname"];?>" class="input-small">
                            <input type="text" placeholder="会员卡号" name="card" value="<?php echo $_REQUEST["card"];?>" class="input-small">
                            <input type="text" name="id_card" value="<?php echo $_REQUEST["id_card"];?>" placeholder="会员身份证号" class="input-small">
                            <input type="text" placeholder="会员电话" name="card" value="<?php echo $_REQUEST["tel"];?>" class="input-small">
                            <input type="text" placeholder="会员QQ" name="qq" value="<?php echo $_REQUEST["qq"];?>" class="input-small">

                        </td>

                    </tr>
                    <tr class="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <select name="warehouse" class="input-medium">
                                <option value="">所有仓库</option>
                                <?php foreach ($warehouse as $var) {?>
                                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
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
                                <option value="">创建人</option>
                                <?php foreach ($this->m_staff->finds() as $staff) {?>
                                <option value="<?php echo $staff["uid"];?>"<?php echo $_REQUEST["staff_uid"]===$staff["uid"]?' selected':'';?>><?php echo $staff["realname"];?></option>
                                <?php } ?>
                            </select>
                            <select name="status" class="input-small">
                                <option value="">状态</option>
                                <option value="1"<?php echo $_REQUEST["status"]==='1'?' selected':'';?>>已完成</option>
                                <option value="-1"<?php echo $_REQUEST["status"]==='-1'?' selected':'';?>>有退货</option>
                                <option value="-2"<?php echo $_REQUEST["status"]==='12'?' selected':'';?>>已退货</option>
                            </select>
                        </td>

                    </tr>
                </tbody>
            </table>
        </form>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个销售记录</small>
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

        });</script>

</body>
</html>
<?php } ?>