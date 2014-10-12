<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/stock_alarm.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        
        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('inventory/stock_alarm/'.$py);?>" accept-charset="utf-8" method="get">
            <select name="type" class="input-small">
                <option value="">类型</option>
                <option value="1"<?php echo $_REQUEST["type"]==='1'?' selected':'';?>>正常</option>
                <option value="2"<?php echo $_REQUEST["type"]==='2'?' selected':'';?>>赠品</option>
            </select>
            <select name="warehouse" class="input-medium">
                <option value="">所有仓库</option>
                <?php foreach ($warehouse as $var) {?>
                <option value="<?php echo $var["w_id"];?>"<?php echo $_REQUEST["warehouse"]===$var["w_id"]?' selected':'';?>><?php echo $var["w_name"];?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn btn-primary input-small" title="查询"><i class="icon-search"></i> 搜索</button>

            <button type="button" class="btn" title="打印" onclick="PrintNoBorderTable('<?php echo $this->url->site('prints/stock_alarm/'.'?session_id='.session_id());?>&'+$('form').serialize(),'打印退货订单')"><i class="icon-print"></i> 打印查询结果</button>


        </form>
        <?php if (empty($list)){ ?>
        <div class="alert alert-block">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Warning!</h4>
            暂时没有相关数据
        </div>
        <?php }else{ ?>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo count($list);?></strong>个库存报警记录</small>
        </p>
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>仓库</th>
                    <th>库存</th>
                    <th>报警</th>
                    <th>单位</th>
                    <th>识别码</th>
                    <th>产品名称</th>
                    <th>产品分类</th>
                    <th>产品类型</th>
                    <th style="width:50px;text-align:center">查看</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $var) {?>
                <tr>
                    <td><?php echo $var["w_name"];?></td>
                    <td><?php echo $var["quantity"];?></td>
                    <td><?php echo $var["lowest"];?></td>
                    <td><?php echo $var["unit"];?></td>
                    <td><?php echo $var["code"];?></td>
                    <td><?php echo $var["name"];?></td>
                    <td><?php echo $var["category"];?></td>
                    <td><?php echo $var["type"]==='1'?'正常':'赠品';?></td>
                    <td style="text-align:center"><a href="<?php echo $this->url->site('inventory/product_look/'.$var["id"]);?>" title="查看产品"><i class="icon-search"></i> 查看</a></td>
                </tr>
                <?php } ?>

            </tbody>
        </table>



        <?php } ?>
    </div>
    <?php echo $this->fetch('public/js');?>
</body>
</html>