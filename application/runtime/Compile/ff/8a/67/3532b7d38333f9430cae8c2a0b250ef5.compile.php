<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/statistics/sales_returns.html
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
            <th>ID</th>
            <th>退货日期</th>
            <th>会员</th>
            <th>操作人</th>
            <th>退货数量</th>
            <th>入库</th>
            <th>出库</th>
            <th>积分变更</th>
            <th>产品识别码</th>
            <th>产品名称</th>
            <th>产品分类</th>
            <th>产品备注</th>
            <th style="width:50px;text-align:center">订单</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <?php $var["product_data"]=unserialize($var["product_data"]);; ?>
        <tr>
            <td><?php echo sprintf("%06d",$var["id"]);?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int) $var["time"]));?></td>
            <td><?php echo $var["memberrealname"];?></td>
            <td><?php echo $var["realname"];?></td>
            <td><?php echo $var["quantity"];?></td>
            <td><?php echo $var["w_name"];?></td>
            <td><?php echo $var["w_name2"];?></td>
            <td><?php echo $var["is_points"]?'<i class="icon-ok"></i>':'';?></td>
            <td><?php echo $var["product_data"]["code"];?></td>
            <td><?php echo $var["product_data"]["name"];?></td>
            <td><?php echo $var["product_data"]["category"];?></td>
            <td title="<?php echo $var["remark"];?>"><?php echo $this->t_string->msubstr($var["remark"],0,5);?></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_sales_look/'.$var["ordersid"]);?>" title="查看产品"><i class="icon-search"></i> 查看</a></td>
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
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('statistics/sales_returns/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <input type="text" placeholder="识别码/产品名称" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-medium">
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timea" value="<?php echo $_REQUEST["timea"];?>" placeholder="创建开始日期">
                            <i class="icon-resize-horizontal"></i>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timeb" value="<?php echo $_REQUEST["timeb"];?>" placeholder="创建结束日期">
                            <div class="btn-group" data-toggle="buttons-radio">
                                <button type="button" onclick="$('#chartinput').val(0); $('form').submit()" class="btn<?php echo empty($chart)?' active':'';?>"><i class="icon-bar-chart"></i> 宏观图</button>
                                <button type="button" onclick="$('#chartinput').val(1); $('form').submit()" class="btn<?php echo $chart==='1'?' active':'';?>"><i class="icon-table"></i> 表格</button>
                            </div>
                            <input type="hidden" id="chartinput" name="chart" value="<?php echo $chart;?>" />

                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                            <button class="btn" type="button" onclick="$('.more_search').fadeToggle(); $('#financequerysearch').val($('#financequerysearch').val() == 1?0:1)"><i class="icon-rss"></i> 更多搜索</button>

                            

                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                    <tr class="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <input type="hidden" name="financequerysearch" id="financequerysearch" value="<?php echo $_REQUEST["financequerysearch"]?:0;?>" />

                            <input type="text" placeholder="订单号" name="number" value="<?php echo $_REQUEST["number"];?>" class="input-medium">
                            <input type="text" placeholder="会员姓名" name="realname" value="<?php echo $_REQUEST["realname"];?>" class="input-small">
                            <input type="text" placeholder="会员卡号" name="card" value="<?php echo $_REQUEST["card"];?>" class="input-small">
                            <input type="text" name="id_card" value="<?php echo $_REQUEST["id_card"];?>" placeholder="会员身份证号" class="input-small">
                            <input type="text" placeholder="会员电话" name="card" value="<?php echo $_REQUEST["tel"];?>" class="input-small">
                            <input type="text" placeholder="会员QQ" name="qq" value="<?php echo $_REQUEST["qq"];?>" class="input-small">
                            <select name="type" class="input-small">
                                <option value="">类型</option>
                                <option value="1"<?php echo $_REQUEST["type"]==='1'?' selected':'';?>>正常</option>
                                <option value="2"<?php echo $_REQUEST["type"]==='2'?' selected':'';?>>赠品</option>
                            </select>
                        </td>

                    </tr>
                    <tr class="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <select name="warehouse" class="input-medium">
                                <option value="">入库</option>
                                <?php foreach ($warehouse as $var) {?>
                                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
                                <?php } ?>
                            </select>
                            <select name="warehouse2" class="input-medium">
                                <option value="">出库</option>
                                <?php foreach ($warehouse as $var) {?>
                                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse2"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
                                <?php } ?>
                            </select>
                            <select name="c_id" class="input-xlarge">
                                <option value="">所有分类</option>
                                <?php foreach ($category as $var) {?>
                                <option value="<?php echo $var["id"];?>"<?php echo $_REQUEST["c_id"]===$var["id"]?' selected':'';?>><?php echo $var["name"];?></option>
                                <?php } ?>
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
        <?php if (empty($chart)){ ?>
         <p>
            <small><i class="icon-info-sign"></i> 合计退回:<strong><?php echo array_sum($list["return"]);?></strong>个</small>
        </p>
        <div id="main" style="height:450px;border:0px solid #ccc;padding:10px;"></div>

        <?php }else{ ?>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个销售记录，合计退回:<strong><?php echo array_sum($list["return"]);?></strong>个</small>
        </p>
        <div id="tablelist">

        </div>
        <div class="pagination pagination-centered">
            <ul id='pagination'>

            </ul>
        </div>
        <?php } ?>

    </div>
    <?php echo $this->fetch('public/js');?>
    <?php if (empty($chart)){ ?>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/echarts/esl/esl.js');?>" ></script>
    <script type="text/javascript" src="<?php echo $this->url->base('assets/echarts/echarts-plain.js');?>" ></script>
    <script type="text/javascript">
                option = {
                title : {
                text: '销售退货宏观图',
                        subtext: '<?php echo $_REQUEST["timea"];?>至<?php echo $_REQUEST["timeb"];?>'
                },
                        tooltip : {
                        trigger: 'axis'
                        },
                        legend: {
                        data:['退回']
                        },
                        toolbox: {
                        show : true,
                                feature : {
                                mark : {show: true},
                                        dataView : {show: true, readOnly: false},
                                        magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                                        restore : {show: true},
                                        saveAsImage : {show: true}
                                }
                        },
                        calculable : true,
                        xAxis : [
                        {
                        type : 'category',
                                boundaryGap : false,splitLine : {show : false},
                                data : [<?php echo implode(',', $list["date"]);?>]
                        }
                        ],
                        yAxis : [
                        {
                        type : 'value'
                        }
                        ],
                        series : [
                        {
                        name:'退回',
                                type:'line',
                                smooth:true,
                                itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                data:[<?php echo implode(',', $list["return"]);?>],
            markLine : {
                data : [
                    {type : 'average', name : '平均值'}
                ]
            }
                        }
                        ]
                };
                echarts.init(document.getElementById('main')).setOption(option);</script>
    <?php }else{ ?>
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
    <?php } ?>
</body>
</html>
<?php } ?>