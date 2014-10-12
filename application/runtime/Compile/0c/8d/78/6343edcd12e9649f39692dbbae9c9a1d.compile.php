<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/public/message.html
*/
?><?php if (!empty($close)){ ?>
<script type="text/javascript">
    var closeHandler = function () {
        ferosClient.close();
    }
    addEventListener("ferosClientReady", closeHandler);
</script>
<?php }else{ ?>
<?php echo $this->fetch('public/head');?>
<?php if (!empty($url)){ ?>
<script type="text/javascript">
    function Jump() {
        window.location.href = '<?php echo $url;?>';
    }
    document.onload = setTimeout("Jump()", <?php echo $time;?> * 1000);

</script>
<?php } ?>
</head>
<body>
    <div class="container-fluid">
        <?php if ($status===1){ ?>
        <div class="Prompt">
            <div class="Prompt_top"></div>
            <div class="Prompt_con">
                <dl>
                    <dt>提示信息</dt>
                    <dd><span class="Prompt_ok"></span></dd>
                    <dd>
                        <h2><?php echo $message;?></h2><?php if (!empty($url)){ ?>
                        <p>系统将在 <span style="color:blue;font-weight:bold"><?php echo $time;?></span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="<?php echo $url;?>">这里</A> 关闭</p>
                        <?php } ?>
                    </dd>
                </dl>
                <div class="c"></div>
            </div>
            <div class="Prompt_btm"></div>
        </div>
        <?php }else{ ?>
        <div class="Prompt">
            <div class="Prompt_top"></div>
            <div class="Prompt_con">
                <dl>
                    <dt>提示信息</dt>
                    <dd><span class="Prompt_x"></span></dd>
                    <dd>
                        <h2 style="color:red"><?php echo $message;?></h2><?php if (!empty($url)){ ?>
                        <p>系统将在 <span style="color:blue;font-weight:bold"><?php echo $time;?></span> 秒后自动关闭，如果不想等待,直接点击 <A HREF="<?php echo $url;?>">这里</A> 关闭</p>
                        <?php } ?>
                </dl>
                <div class="c"></div>
            </div>
            <div class="Prompt_btm"></div>
        </div>
        <?php } ?>
    </div>
    <?php echo $this->fetch('public/js');?>

</body>
</html>
<?php } ?>