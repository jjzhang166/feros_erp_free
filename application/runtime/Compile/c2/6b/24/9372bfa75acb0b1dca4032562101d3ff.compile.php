<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/finance/add.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">

        <form class="form-horizontal" action="<?php echo $this->url->site('finance/add');?>" accept-charset="utf-8" method="post">
            <div id="legend" class="">
                <legend class="">新增账务</legend>
            </div>
            <table class="table table-hover">
                <tr>
                    <th style="line-height:30px;text-align:right">分类</th>
                    <td>
                        <div id="finance_category"> 
                            <select class="input-small finance_type" name="type" required>
                                <option>选择类型</option>
                            </select>
                            <select class="input-medium finance_c_id" name="c_id">
                                <option>选择分类</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">银行</th>
                    <td><select name="b_id" class="input-xlarge" required>
                            <?php foreach ($bank as $var) {?>
                            <option value="<?php echo $var["id"];?>"<?php echo $var["default"]==='1'?' selected':'';?>><?php echo $var["py"]?$var["py"].'-':'';?><?php echo $var["bank"];?></option>
                            <?php } ?>
                        </select></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">金额</th>
                    <td><input type="text" name="money" class="input-xlarge" required></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">日期</th>
                    <td><input class="input-xlarge" name="datetime" type="text" value="" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'})"></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">经办人</th>
                    <td><select name="attn" class="input-xlarge">
                            <option value="">请选择</option>
                            <?php foreach ($staff as $var) {?>
                            <option value="<?php echo $var["uid"];?>"><?php echo $var["py"];?><?php echo $var["realname"];?></option>
                            <?php } ?>
                        </select></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">备注</th>
                    <td><textarea name="remark" type="" class="input-xlarge" style="height:60px"></textarea></td>
                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></th>
                    <td>
                        <button type="submit" class="btn btn-primary input-small"><i class="icon-save"></i> 保存</button> <button type="reset" class="btn input-small"><i class="icon-refresh"></i> 重置</button>
                    </td>
                </tr>
            </table>



        </form>
    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#finance_category').cxSelect({
                selects: ['finance_type', 'finance_c_id'],
                nodata: 'none',
                url: "<?php echo $this->url->site('json/finance_category');?>"
            });
            post_sisyphus();
        });
    </script>   
</body>
</html>
