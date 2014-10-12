<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/product_categories.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <form class="form-inline" action="<?php echo $this->url->site('system/product_categories/'.$py);?>" accept-charset="utf-8" method="get">
            <a data-trigger="modal" href="<?php echo $this->url->site('system/product_categories_add');?>" data-title="新增产品类" title="新增产品类" class="btn"><i class="icon-plus"></i> 新增分类</a>

        </form>
        <?php if (empty($list)){ ?>
        <div class="alert alert-block">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4>Warning!</h4>
            暂时没有相关数据
        </div>
        <?php }else{ ?>
        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo count($list);?></strong>个分类</small>
        </p>
        <table class="table table-hover table-striped" id="bank_list">
            <thead>
                <tr>
                    <th>分类名称</th>
                    <th style="width:50px;text-align:center">修改</th>
                    <th style="width:50px;text-align:center">删除</th>
                    <th style="width:50px;text-align:center">排序</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $var) {?>
                <tr categoryid="<?php echo $var["id"];?>">
                    <td><?php echo $var["name"];?></td>
                    <td style="text-align:center"><a data-trigger="modal" href="<?php echo $this->url->site('system/product_categories_edit/'.$var["id"]);?>" data-title="修改产品类" title="修改产品类"><i class="icon-edit"></i> 修改</a></td>
                    <td style="text-align:center"><?php if (empty($var["staff"])){ ?><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('system/product_categories_delete/'.$var["id"]);?>"><i class="icon-remove"></i> 删除</a><?php } ?></td>
                    <td style="text-align:center" onmouseover="onmouseoverdragsort()" onmouseout="onmouseoutdragsort()"><i class="icon-move"></i> 排序</td>
                </tr>
                <?php } ?>
            </tbody>
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
                return $(this).attr('categoryid');
            }).get();
            ajax_post("<?php echo $this->url->site('system/product_categories_sort');?>", 'sort=' + data.join("|"));
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