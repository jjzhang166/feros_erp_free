<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/member/birthday.html
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

            <th>会员姓名</th>

            <th>性别</th>
            <th>会员卡号</th>
            <th>联系电话</th>
            <th>QQ</th>
            <th>会员分类</th>
            <th>会员折扣</th>
            <th>会员积分</th>
            <th>会员生日</th>
            <th>创建人</th>
            <th>创建日期</th>
            <th style="width:50px;text-align:center">查看</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $var) {?>
        <tr>
            <td><?php echo $var["realname"];?></td>
            <td><?php echo $var["sex"];?></td>
            <td><?php echo $var["card"];?></td>
            <td><?php echo $var["tel"];?></td>
            <td><?php echo $var["qq"];?></td>
            <td><?php echo $var["category"];?></td>
            <td><?php echo $var["discounts"];?></td>
            <td><?php echo $var["points"];?></td>
            <td><?php echo $var["birthday"]!=='0000-00-00'?$var["birthday"]:'';?></td>
            <td><?php echo $var["s_realname"];?></td>
            <td><?php echo array_shift($this->t_date->get_lastdate((int)$var["time"]));?></td>
            <td style="text-align:center"><a href="<?php echo $this->url->site('member/index_look/'.$var["id"]);?>" title="查看供应商"><i class="icon-search"></i> 查看</a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php } ?>
<?php }else{ ?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li>姓名首字母检索 <span class="divider">/</span></li>
            <?php foreach ($pinyin as $var) {?>
            <li<?php echo $py===$var?' class="active"':'';?>><?php if ($py===$var){ ?><?php echo $var;?><?php }else{ ?><a href="<?php echo $this->url->site('member/birthday/'.$var);?>"><?php echo $var;?></a><?php } ?><span class="divider">/</span></li>
            <?php } ?>
            <li<?php echo empty($py)?' class="active"':'';?>><?php if (empty($py)){ ?>全部<?php }else{ ?><a href="<?php echo $this->url->site('member/birthday');?>">全部</a><?php } ?></li>
        </ul>

        <form id="forminventorysupplier" class="form-inline" action="<?php echo $this->url->site('member/birthday/'.$py);?>" accept-charset="utf-8" method="get">
            <table class="table table-hover">
                <tbody>

                    <tr>
                        <td>
                            <select name="g_id" class="input-medium">
                                <option value="">会员分组</option>
                                <?php foreach ($group as $var) {?>
                                <option value="<?php echo $var["id"];?>"<?php echo $_REQUEST["g_id"]===$var["id"]?' selected':'';?>><?php echo $var["name"];?></option>
                                <?php } ?>
                            </select>
                          
                           
                            <button type="submit" class="btn btn-primary input-small" title="查询会员"><i class="icon-search"></i> 搜索</button>
                            <input type="hidden" id="paginationinput" name="pagination" value="1" />
                            <input type="hidden" id="countinput" name="count" value="<?php echo $count;?>" />
                        </td>
                    </tr>
                </tbody>
            </table>

        </form>

        <p>
            <small><i class="icon-info-sign"></i> 查询到了<strong><?php echo $count;?></strong>个会员</small>
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