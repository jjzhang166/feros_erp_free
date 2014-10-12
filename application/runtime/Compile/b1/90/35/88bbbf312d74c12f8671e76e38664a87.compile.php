<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/finance/bank.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li>名称首字母检索 <span class="divider">/</span></li>
            <?php foreach ($pinyin as $var) {?>
            <li<?php echo $py===$var?' class="active"':'';?>><?php if ($py===$var){ ?><?php echo $var;?><?php }else{ ?><a href="<?php echo $this->url->site('finance/bank/'.$var);?>"><?php echo $var;?></a><?php } ?><span class="divider">/</span></li>
            <?php } ?>
            <li<?php echo empty($py)?' class="active"':'';?>><?php if (empty($py)){ ?>全部<?php }else{ ?><a href="<?php echo $this->url->site('finance/bank');?>">全部</a><?php } ?></li>
        </ul>


        <form class="form-inline" action="<?php echo $this->url->site('finance/bank/'.$py);?>" accept-charset="utf-8" method="get">
            <input type="text" placeholder="银行名称或首字母" name="keyword" value="<?php echo $_REQUEST["keyword"];?>" class="input-xlarge">

            <button type="submit" class="btn btn-primary input-small" title="查询银行"><i class="icon-search"></i> 搜索</button>
            <a data-trigger="modal" href="<?php echo $this->url->site('finance/bank_add');?>" data-title="新增银行" title="新增银行" class="btn"><i class="icon-plus"></i> 银行</a>

        </form>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo count($list);?></strong>个数据</small>
        </p>

        <?php if (empty($list)){ ?>
        <div class="alert alert-block">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Warning!</h4>
            暂时没有相关数据
        </div>
        <?php }else{ ?>
        <table class="table table-hover table-striped" id="bank_list">
            <thead>
                <tr>
                    <th style="width:30px">#<?php $i=1; ?></th>
                    <th>银行名称</th>
                    <th>金额</th>
                    <th>备注</th>
                    <th style="width:50px;text-align:center">默认</th>
                    <th style="width:50px;text-align:center">修改</th>
                    <th style="width:50px;text-align:center">删除</th>
                    <th style="width:50px;text-align:center">排序</th>
                </tr>
            </thead>
            <tbody>
                <?php $sum=0; ?>
                <?php foreach ($list as $var) {?>
                <tr bankid="<?php echo $var["id"];?>"<?php echo $var["money"]<=0?' class="error"':'';?>>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $var["bank"];?></td>
                    <td><?php echo $var["money"];?><?php $sum+=$var["money"]; ?></td>
                    <td><?php echo $var["remark"];?></td>
                    <td><?php echo $var["default"]?'<i class="icon-ok"></i>':'';?></td>
                    <td style="text-align:center"><a data-trigger="modal" href="<?php echo $this->url->site('finance/bank_edit/'.$var["id"]);?>" data-title="修改银行" title="修改银行"><i class="icon-edit"></i> 修改</a></td>
                    <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('finance/bank_delete/'.$var["id"]);?>"><i class="icon-remove"></i> 删除</a></td>
                    <td style="text-align:center" onmouseover="onmouseoverdragsort()" onmouseout="onmouseoutdragsort()"><i class="icon-move"></i> 排序</td>
                </tr>
                <?php } ?>
            </tbody>
            <thead>
                <tr>
                    <th></th>
                    <th>合计</th>
                    <th><?php echo $sum;?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
        </table>
        <?php } ?>
    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function () {
            onmouseoutdragsort();
        });
        function saveOrder() {
            var data = $("table tbody tr").map(function () {
                return $(this).attr('bankid');
            }).get();
            ajax_post("<?php echo $this->url->site('finance/bank_sort');?>", 'sort=' + data.join("|"));
        }
        function onmouseoutdragsort() {
            $("table tbody").dragsort("destroy");
        }
        function onmouseoverdragsort() {
            $("table tbody").dragsort({dragSelector: "tr", dragBetween: true, dragEnd: saveOrder});
        }
    </script>
</body>
</html>
