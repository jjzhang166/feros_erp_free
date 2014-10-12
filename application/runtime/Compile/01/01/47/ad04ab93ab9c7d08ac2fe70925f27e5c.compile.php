<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/home/home.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <?php if (!empty($base["desktop_news"])){ ?>
        <div class="alert alert-block alert-error fade in">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h4 class="alert-heading">消息</h4>
            <p><?php echo $base["desktop_news"];?></p>
        </div>
        <?php } ?>
<dl>
                            <dt>服务热线</dt>
                            <dd><a href="tel:4006233325" target="_blank" ><i class="fa fa-phone"></i> 4006233325</a></dd>
                            <dd>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;售后服务 请按 1</dd>
                            <dd>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;技术支持 请按 2</dd>
                            <dd>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;域名主机 请按 3</dd>
                            <dd>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;项目合作 请按 5</dd>
                            <dt>联系地址</dt>
                            <dd>中山市石岐区康华路40号2楼</dd>
                            <dt>官方网址</dt>
                            <dd><a href="http://feros.com.cn/erp.html" target="_blank" title="官方网址"><i class="fa fa-home"></i> www.feros.com.cn</a></dd>
                            <dt>服务邮箱</dt>
                            <dd><a href="mailto:admin@feros.com.cn?subject=咨询" target="_blank" title="邮箱咨询"><i class="fa fa-envelope"></i> admin@feros.com.cn</a></dd>
                            <dt>在线客服</dt>
                            <dd><a href="http://wpa.qq.com/msgrd?v=3&uin=1286522207&site=qq&menu=yes" target="_blank"><i class="fa fa-qq"></i>1286522207</a></dd>
                            <dd><a href="http://wpa.qq.com/msgrd?v=3&uin=1878331213&site=qq&menu=yes" target="_blank"><i class="fa fa-qq"></i>1878331213</a></dd>
                        </dl>


    </div>

    <?php echo $this->fetch('public/js');?>

</body>
</html>