<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/meun.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <p><a href="<?php echo $this->url->site('system/meun_add');?>" title="新增菜单" class="btn"><i class="icon-plus"></i> 新增菜单</a></p>
        <ul class="nav nav-tabs">
            <?php foreach ($list as $key=>$var) {?>
            <li menuid="<?php echo $var["id"];?>"<?php echo $_COOKIE["systemmeunnavtabs"]===$var["controller"]||(empty($_COOKIE["systemmeunnavtabs"])&&$key=='0')?' class="active"':'';?> onclick="$.cookie('systemmeunnavtabs', '<?php echo $var["controller"];?>');"><a href="#<?php echo $var["controller"];?>" data-toggle="tab"><?php echo $var["name"];?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <?php foreach ($list as $key=>$var) {?>
            <div class="tab-pane fade in<?php echo $_COOKIE["systemmeunnavtabs"]===$var["controller"]||(empty($_COOKIE["systemmeunnavtabs"])&&$key=='0')?' active':'';?>" id="<?php echo $var["controller"];?>">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>菜单标题</th>
                            <th>GROUP</th>
                            <th>CONTROLLER</th>
                            <th>ACTION</th>
                            <th>备注</th>
                            <th style="width:50px">显示</th>
                            <th style="width:60px">可关闭</th>
                            <th style="width:50px;text-align:center">新增</th>
                            <th style="width:50px;text-align:center">修改</th>
                            <th style="width:50px;text-align:center">删除</th>
                            <th style="width:50px;text-align:center">排序</th>
                        </tr>
                    </thead>
                    <tr menuid="<?php echo $var["id"];?>">
                        <td colspan="2"><?php echo $var["name"];?></td>
                        <td colspan="2"><?php echo $var["controller"];?></td>
                        <td></td>
                        <td><?php echo $var["status"]?'<i class="icon-ok"></i>':'';?></td>
                        <td></td>
                        <td><a href="<?php echo $this->url->site('system/meun_add/'.$var["id"]);?>" title="新增菜单"><i class="icon-plus"></i> 新增</a></td>
                        <td style="text-align:center"><a href="<?php echo $this->url->site('system/menu_edit/'.$var["id"]);?>"><i class="icon-edit"></i> 修改</a></td>
                        <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('system/menu_delete/'.$var["id"]);?>"><i class="icon-remove"></i> 删除</a></td>
                        <td style="text-align:center" onmouseover="onmouseoverdragsort()" onmouseout="onmouseoutdragsort()"><i class="icon-move"></i> 排序</td>
                    </tr>
                    <?php foreach ($var["s"] as $var2) {?>
                    <tr menuid="<?php echo $var2["id"];?>">
                        <td></td>
                        <td colspan="3"><?php echo $var2["name"];?></td>
                        <td></td>
                        <td><?php echo $var2["status"]?'<i class="icon-ok"></i>':'';?></td>
                        <td></td>
                        <td><a href="<?php echo $this->url->site('system/meun_add/'.$var2["m_id"].'/'.$var2["id"]);?>" title="新增菜单"><i class="icon-plus"></i> 新增</a></td>
                        <td style="text-align:center"><a href="<?php echo $this->url->site('system/menu_edit/'.$var2["id"]);?>"><i class="icon-edit"></i> 修改</a></td>
                        <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('system/menu_delete/'.$var2["id"]);?>"><i class="icon-remove"></i> 删除</a></td>
                        <td style="text-align:center" onmouseover="onmouseoverdragsort()" onmouseout="onmouseoutdragsort()"><i class="icon-move"></i> 排序</td>
                    </tr>
                    <?php foreach ($var2["s"] as $var3) {?>
                    <tr menuid="<?php echo $var3["id"];?>">
                        <td><?php echo $var3["name"];?></td>
                        <td colspan="2"></td>
                        <td><?php echo $var3["action"];?></td>
                        <td><?php echo $var3["remark"];?></td>
                        <td><?php echo $var3["status"]?'<i class="icon-ok"></i>':'';?></td>
                        <td><?php echo $var3["closeable"]?'<i class="icon-ok"></i>':'';?></td>
                        <td></td>
                        <td style="text-align:center"><a href="<?php echo $this->url->site('system/menu_edit/'.$var3["id"]);?>"><i class="icon-edit"></i> 修改</a></td>
                        <td style="text-align:center"><a data-trigger="confirm" data-content="你确定要删除吗？删除后无法恢复" href="<?php echo $this->url->site('system/menu_delete/'.$var3["id"]);?>"><i class="icon-remove"></i> 删除</a></td>
                        <td style="text-align:center" onmouseover="onmouseoverdragsort()" onmouseout="onmouseoutdragsort()"><i class="icon-move"></i> 排序</td>
                    </tr>
                    <?php } ?>
                    <?php } ?>

                </table>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function () {
            onmouseoutdragsort();
            $("ul").dragsort("destroy");
            $("ul").dragsort({dragSelector: "li", dragBetween: true, dragEnd: saveOrder2});
        });
        function saveOrder2() {
            var data = $("ul li").map(function () {
                return $(this).attr('menuid');
            }).get();
            ajax_post("<?php echo $this->url->site('system/menu_sort');?>", 'sort=' + data.join("|"));
        }
        function saveOrder() {
            var data = $("table tbody tr").map(function () {
                return $(this).attr('menuid');
            }).get();
            ajax_post("<?php echo $this->url->site('system/menu_sort');?>", 'sort=' + data.join("|"));
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