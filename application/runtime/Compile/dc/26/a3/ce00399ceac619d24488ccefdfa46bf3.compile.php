<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/finance/query.html
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
            <th>分类</th>
            <th>收入</th>
            <th>支出</th>
            
            <th>银行</th>

            <th>状态</th>
            <th>经办人</th>
            <th>经办日期</th>
            <th>创建人</th>
            <th>创建日期</th>
            <th>备注</th>
            <th style="width:50px;text-align:center">撤销</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
             <td><?php echo $var["c_name"];?></td>
            <td><?php echo $var["type"]?'+'.$var["money"]:'';?></td>
            <td><?php echo $var["type"]?'':'<span style="color:#f18130">'.'-'.$var["money"].'</span>';?></td>
           
            <td><?php echo $var["bank"];?></td>

            <td><?php echo $var["status"]?'<i class="icon-ok"></i> 正常':'<i class="icon-remove"></i> 撤销';?></td>
            <td><?php echo $var["realname_attn"];?></td>
            <td><?php echo $var["datetime"]?array_shift($this->t_date->get_lastdate((int)$var["datetime"])):'';?></td>
            <td><?php echo $var["realname"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["create"]));?></td>
            <td><?php echo $var["remark"];?></td>
            <td style="text-align:center">
                <?php if (!empty($var["status"])){ ?>
                <a data-trigger="confirm" data-content="你确定要撤销吗？撤销后变动金额将返回银行" href="<?php echo $this->url->site('finance/query_delete/'.$var["id"]);?>"><i class="icon-refresh"></i> 撤销</a>
                <?php } ?>
            </td>

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


        <form class="form-inline" id="financequery" action="<?php echo $this->url->site('finance/query/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timea" value="<?php echo $_REQUEST["timea"];?>" placeholder="创建开始日期">
                            <i class="icon-resize-horizontal"></i>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="timeb" value="<?php echo $_REQUEST["timeb"];?>" placeholder="创建结束日期">


                            <select class="input-small" name="b_id">
                                <option value="">银行</option>
                                <?php foreach ($this->m_finance_bank->finds() as $var) {?>
                                <option value="<?php echo $var["id"];?>"<?php echo $var["id"]===$_REQUEST["b_id"]?' selected':'';?>><?php echo $var["bank"];?></option>
                                <?php } ?>
                            </select>
                            <select class="input-small" name="status">
                                <option value="">状态</option>
                                <option value="1"<?php echo $_REQUEST["status"]=='1'?' selected':'';?>>正常</option>
                                <option value="0"<?php echo $_REQUEST["status"]=='0'?' selected':'';?>>撤销</option>

                            </select>

                            <select name="attn" class="input-small">
                                <option value="">经办人</option>
                                <?php foreach ($staffs as $staff) {?>
                                <option value="<?php echo $staff["uid"];?>"<?php echo $_REQUEST["attn"]===$staff["uid"]?' selected':'';?>><?php echo $staff["realname"];?></option>
                                <?php } ?>
                            </select>
                            <select name="uid" class="input-small">
                                <option value="">创建人</option>
                                <?php foreach ($staffs as $staff) {?>
                                <option value="<?php echo $staff["uid"];?>"<?php echo $_REQUEST["uid"]===$staff["uid"]?' selected':'';?>><?php echo $staff["realname"];?></option>
                                <?php } ?>
                            </select>

                            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>
                             <button type="button" class="btn" title="打印" onclick="PrintNoBorderTable('<?php echo $this->url->site('prints/finance/'.'?session_id='.session_id());?>&'+$('form').serialize(),'打印入库单')"><i class="icon-print"></i> 打印查询结果</button>

                            <button class="btn" type="button" onclick="$('#more_search').fadeToggle(); $('#financequerysearch').val($('#financequerysearch').val() == 1?0:1)"><i class="icon-rss"></i> 更多搜索</button>
                        </td>
                    </tr>
                    <tr id="more_search"<?php echo $_REQUEST["financequerysearch"]?'':' style="display:none"';?>>
                        <td>
                            <input type="hidden" name="financequerysearch" id="financequerysearch" value="<?php echo $_REQUEST["financequerysearch"]?:0;?>" />
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="datetimea" value="<?php echo $_REQUEST["datetimea"];?>" placeholder="经办开始日期">
                            <i class="icon-resize-horizontal"></i>
                            <input size="16" type="text" class="form_datetime input-small" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd'})" name="datetimeb" value="<?php echo $_REQUEST["datetimeb"];?>" placeholder="经办结束日期">
                            <input type="text" class="input-mini" name="moneya" value="<?php echo $_REQUEST["moneya"];?>" placeholder="金额">
                            <i class="icon-resize-horizontal"></i>
                            <input type="text" class="input-mini" name="moneyb" value="<?php echo $_REQUEST["moneyb"];?>" placeholder="金额">
                            <input type="text" class="input-medium" name="remark" value="<?php echo $_REQUEST["remark"];?>" placeholder="备注">


                            <span id="finance_category"> 
                                <select class="input-small finance_type" name="selecttype" data-value="<?php echo $_REQUEST["selecttype"];?>" data-first-title="类型" data-first-value="" onchange="$('#typeinput').val($(this).val())"></select>
                                <select class="input-medium finance_c_id" name="selectc_id" data-value="<?php echo $_REQUEST["selectc_id"];?>" data-first-title="分类" data-first-value="" onchange="$('#c_idinput').val($(this).val())"></select>
                            </span>

                        </td>
                    </tr>
                </tbody>
            </table>





            <input type="hidden" id="typeinput" name="type" value="<?php echo $_REQUEST["selecttype"];?>" />
            <input type="hidden" id="c_idinput" name="c_id" value="<?php echo $_REQUEST["selectc_id"];?>" />

            <input type="hidden" id="paginationinput" name="pagination" value="1" />
            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
        </form>

        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个账务记录
           <?php if (!empty($revenue)){ ?> 收入:<strong><?php echo $revenue;?></strong><?php } ?> <?php if (!empty($expenditure)){ ?>支出:<strong><?php echo $expenditure;?></strong><?php } ?>
            </small>
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
                $(document).ready(function () {
        $('#finance_category').cxSelect({
        selects: ['finance_type', 'finance_c_id'],
                nodata: 'none',
                url: "<?php echo $this->url->site('json/finance_category');?>"
        });
        <?php if (!empty($count)){ ?>
        $('#pagination').jqPaginator({
        totalCounts: <?php echo $count;?>, pageSize:<?php echo $base["queqry"];?>,
                currentPage: 1,
                onPageChange: function(num, type) {
                $('#paginationinput').val(num);
                        $.post($('#financequery').attr('action'), $('#financequery').serialize(), function (data) {
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