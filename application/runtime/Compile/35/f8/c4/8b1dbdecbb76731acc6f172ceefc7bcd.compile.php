<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/app/leisure.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <button type="button" class="btn" onclick="ferosClient.runAppEx('music/index.app');"><i class="icon-music"></i> 音乐盒</button>
    </div>
    <?php echo $this->fetch('public/js');?>
</body>
</html>