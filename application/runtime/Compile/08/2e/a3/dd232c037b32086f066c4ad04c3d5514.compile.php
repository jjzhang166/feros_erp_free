<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/meun_add.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">

        <div id="legend" class="">
            <legend class="">新增菜单</legend>
        </div>
        <form class="form-horizontal" action="<?php echo $this->url->site('system/meun_add');?>" style="margin:0px" accept-charset="utf-8" method="post">

            <table class="table table-hover">
                <tr>
                    <th style="width:130px;line-height:30px;text-align:right">目录</th>
                    <td>
                        <div id="meun_category"> 
                           <select class="input-medium meun_grade" data-value="<?php echo $m_id;?>" name="grade">
                            </select>
                            <select class="input-medium meun_parentid" data-value="<?php echo $parentid;?>" name="parentid">
                            </select>
                        </div>

                    </td>
                </tr>
                <tr>
                    <th style="width:130px;line-height:30px;text-align:right">菜单名称</th>
                    <td><input type="text" name="name" placeholder="输入分类名称" class="input-xlarge" required></td>
                </tr>
                <tr class="controller">
                    <th style="width:130px;line-height:30px;text-align:right">CONTROLLER</th>
                    <td><input type="text" name="controller" placeholder="输入CONTROLLER" class="input-xlarge"></td>
                </tr>
                <tr class="action">
                    <th style="width:130px;line-height:30px;text-align:right">ACTION</th>
                    <td><input type="text" name="action" placeholder="输入ACTION" class="input-xlarge"></td>
                </tr>
                <tr>
                    <th style="width:130px;line-height:30px;text-align:right">备注</th>
                    <td><input type="text" name="remark" placeholder="输入备注" class="input-xlarge"></td>
                </tr>
                <tr>
                    <th style="width:130px;line-height:30px;text-align:right"></th>
                    <td><label class="checkbox inline">
                            <input name="status" type="checkbox" value="1">
                            显示
                        </label></td>
                </tr>
                <tr class="closeable">
                    <th style="width:130px;line-height:30px;text-align:right"></th>
                    <td><label class="checkbox inline">
                            <input name="closeable" type="checkbox" value="1">
                            可关闭
                        </label></td>
                </tr>
                <tr class="closeable">
                    <th style="width:130px;line-height:30px;text-align:right"></th>
                    <td><label class="checkbox inline">
                            <input name="home" type="checkbox" value="1">
                            首页
                        </label></td>
                </tr>
                <tr>
                    <th style="width:130px;line-height:30px;text-align:right"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></th>
                    <td>
                        <button type="submit" class="btn btn-primary input-small"><i class="icon-save"></i> 保存</button> <button type="reset" class="btn input-small"><i class="icon-refresh"></i> 重置</button> 
                        <a href="<?php echo $this->url->site('system/meun');?>" title="取消" class="btn"><i class="icon-double-angle-left"></i> 取消</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#meun_category').cxSelect({
                selects: ['meun_grade', 'meun_parentid'],
                nodata: 'none',
                url: "<?php echo $this->url->site('json/menu');?>"
            });
            $('.meun_grade').change(function() {
                meungrade = $(this).val();
                if (meungrade == 0) {
                    $('.action').hide();
                    $('.controller').show();
                    $('.closeable').hide();
                } else if (meungrade > 0) {
                    $('.controller').hide();
                    $('.action').hide();
                    $('.closeable').hide();
                }
            })
            $('.meun_parentid').change(function() {
                meunvar = $(this).val();
                if (meunvar == 0) {
                    $('.controller').hide();
                    $('.action').hide();
                    $('.closeable').hide();
                } else if (meunvar > 0) {
                    $('.action').show();
                    $('.closeable').show();
                }
            })
        });
    </script>   
</body>
</html>