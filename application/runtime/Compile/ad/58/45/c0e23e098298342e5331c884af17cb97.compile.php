<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/system/database_cache.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">

        <form class="form-horizontal" action="<?php echo $this->url->site('system/database_cache');?>" accept-charset="utf-8" method="post">
            <fieldset>
                <div id="legend" class="">
                    <legend class="">缓存管理</legend>
                </div>
                <div class="control-group">
                    <label class="control-label"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></span></label>

                    <!-- Button -->
                    <div class="controls">
                        <button type="submit" class="btn"><i class="icon-inbox"></i> 清空数据缓存</button>
                    </div>
                </div>

            </fieldset>
        </form>



    </div>
    <?php echo $this->fetch('public/js');?>
</body>
</html>
