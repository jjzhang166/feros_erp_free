<?php
/**
* FEROS PHP Framework
* @author feros<admin@feros.com.cn>
* @copyright ©2014 feros.com.cn
* @link http://www.feros.com.cn
* @version 2.0.1
*@view /Users/feisanliang/Documents/开发项目/erp2/application/view/inventory/product_sales.html
*/
?><?php echo $this->fetch('public/head');?>
</head>
<body>
    <div class="container-fluid">
        <form id='inventorystorage' class="form-horizontal" action="<?php echo $this->url->site('inventory/product_sales');?>" ajax='no' accept-charset="utf-8" method="post"autocapitalize="off" autocomplete="off" autocorrect="off">
            <table class="table table-hover">
                <input type="hidden" name="way" id='wayso' value="<?php echo $_REQUEST["way"]?:'so';?>" />
                <tr>
                    <th style="width:100px;line-height:30px;text-align:right">购买会员</th>
                    <td><input type="text" class="input-xlarge" name="numbername" value="<?php echo $info["realname"];?>" id="automember">
                        <input type="hidden" name="member" value="<?php echo $_REQUEST["member"];?>" id="memberuid" />
                        <div class="input-prepend input-append">
                            <button type="submit" class="btn" onclick="$('#wayso').val('so'); $('form').attr('ajax', 'no')"><i class="icon-search"></i></button>
                        </div>
                    </td>
                </tr>
                <?php if (!empty($info)){ ?>
                <tr>
                    <th style="width:100px;text-align:right">会员分组</th>
                    <td>
                        <?php echo $info["name"];?>
                    </td>
                </tr>
                <tr>
                    <th style="width:100px;text-align:right">会员折扣</th>
                    <td>
                        <?php echo $discounts;?>折
                    </td>
                </tr>
                <tr>
                    <th style="width:100px;text-align:right">积分</th>
                    <td>
                        <?php echo $info["points"];?>
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th style="line-height:30px;text-align:right">选择产品</th>
                    <td style="line-height:30px;">
                        <input type="text" name="code" placeholder="产品识别码或搜索" class="input-xlarge" id="autocomplete">
                        <div class="input-prepend input-append">
                            <input type="hidden" name="product_id[]" id="productid" value="" />
                            <button type="submit" class="btn" onclick="$('#wayso').val('so'); $('form').attr('ajax', 'no')"><i class="icon-search"></i></button>
                        </div>
                    </td>
                </tr>
                <?php if (!empty($product)){ ?>
                <?php foreach ($product as $key=>$var) {?>
                <tr id="tabletbody<?php echo $key;?>">
                    <td colspan="2">
                       <table class="table table-hover table-striped" style="margin-bottom:0px">
                            <tbody>
                                <tr>
                                    <th style="width:50px;line-height:30px;text-align:right">识别码</th>
                                    <td style="width:200px;line-height:30px;">
                                        <?php echo $var["code"];?>
                                    </td>
                                    <th style="width:50px;line-height:30px;text-align:right">产品</th>
                                    <td style="width:200px;line-height:30px;"><input type="hidden" name="product_id[]" id="productid" value="<?php echo $var["id"];?>" />
                                        <?php echo $var["name"];?>
                                    </td>

                                    <th style="width:50px;line-height:30px;text-align:right">单价</th>
                                    <td style="width:200px;line-height:30px;">
                                        <?php echo $var["sales"];?>
                                    </td>
                                    <th style="width:50px;line-height:30px;text-align:right">积分</th>
                                    <td style="line-height:30px;">

                                        <div class="input-append">
                                            <input type="text" name=""  value="<?php echo $var["points"];?>" placeholder="积分" class="input-small" disabled>
                                            <?php if (!empty($info)){ ?>
                                            <span class="add-on">
                                                <label class="checkbox" for="appendedCheckbox">
                                                    <input type="checkbox" class="" name="points[]" value="1"<?php echo $_REQUEST['points'][$key]?' checked':'';?>> 积分购买
                                                </label>
                                            </span>
                                            <?php } ?>
                                        </div>


                                    </td>




                                </tr>
                                <tr>
                                    <th style="line-height:30px;text-align:right">数量</th>
                                    <td style="line-height:30px;">
                                        <div class="input-prepend input-append">
                                            <button type="button" class="btn" onclick="$('#quantity<?php echo $key;?>').val((Number($('#quantity<?php echo $key;?>').val()) - 1)); calculatemoney(<?php echo $var["sales"];?>, 'quantity<?php echo $key;?>', 'discounts<?php echo $key;?>', 'money<?php echo $key;?>')"><i class="icon-minus"></i></button>
                                            <input type="number" id="quantity<?php echo $key;?>" onkeyup="calculatemoney(<?php echo $var["sales"];?>, 'quantity<?php echo $key;?>', 'discounts<?php echo $key;?>', 'money<?php echo $key;?>')" name="product_quantity[]" value="<?php echo $_REQUEST['product_quantity'][$key]?:1;?>" placeholder="数量" class="input-small">
                                            <button type="button" class="btn" onclick="$('#quantity<?php echo $key;?>').val(Number($('#quantity<?php echo $key;?>').val()) + 1); calculatemoney(<?php echo $var["sales"];?>, 'quantity<?php echo $key;?>', 'discounts<?php echo $key;?>', 'money<?php echo $key;?>')"><i class="icon-plus"></i></button>
                                        </div>
                                    </td>
                                    <th style="line-height:30px;text-align:right">折扣</th>
                                    <td style="line-height:30px;">
                                        <input type="text" id="discounts<?php echo $key;?>" onkeyup="calculatemoney(<?php echo $var["sales"];?>, 'quantity<?php echo $key;?>', 'discounts<?php echo $key;?>', 'money<?php echo $key;?>')" name="product_discount[]" value="<?php echo $_REQUEST['product_discount'][$key]?:$discounts;?>" placeholder="折扣" class="input-small discounts">
                                    </td>
                                    <th style="line-height:30px;text-align:right">金额</th>
                                    <td style="line-height:30px;">
                                        <span id="money<?php echo $key;?>"></span>


                                    </td>
                                    <th style="line-height:30px;text-align:right">仓库</th>
                                    <td style="line-height:30px;">
                                        <select name="product_warehouse[]" class="input-medium">
                                            <option value="">选择</option>
                                            <?php foreach ($var["warehouse"] as $vars) {?>
                                            <option value="<?php echo $vars["w_id"];?>"<?php echo $vars["w_default"]==='1'||$_REQUEST['product_warehouse'][$key]===$vars["w_id"]?' selected':'';?>><?php echo $vars["w_name"];?> <?php echo $vars["quantity"];?></option>
                                            <?php } ?>
                                        </select>

                                        <button type="button" class="btn" onclick="$('#tabletbody<?php echo $key;?>').empty()"><i class="icon-remove"></i></button>
                                    </td>

                                </tr>
                            <script type="text/javascript">
                                        $(document).ready(function () {
                                calculatemoney(<?php echo $var["sales"];?>, 'quantity<?php echo $key;?>', 'discounts<?php echo $key;?>', 'money<?php echo $key;?>');
                                });</script>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
                <tr>
                    <th style="line-height:30px;text-align:right">总金额</th>
                    <td style="line-height:30px;"><span id="productsummoney"></span></td>

                </tr>
                <tr>
                    <th style="line-height:30px;text-align:right">发货日期</th>
                    <td><input size="16" type="text" class="input-medium" onClick="WdatePicker({dateFmt: 'yyyy-MM-dd HH:mm'})" name="ship" value="<?php echo $_REQUEST["time"]?:date('Y-m-d H:i');?>" placeholder="创建开始日期"></td>

                </tr>


                <tr>
                    <th style="line-height:30px;text-align:right">销售备注</th>
                    <td><textarea name="remark" type="" class="input-xlarge" style="height:60px"></textarea></td>
                </tr>
                <tr>
                    <td style="line-height:30px;text-align:right"><span class="loading"><img src="<?php echo $this->url->base('assets/img/loading.gif');?>"></td>
                            <td colspan="5">
                                <button type="submit" class="btn btn-primary input-small" onclick="$('#wayso').val('go'); $('form').removeAttr('ajax')"><i class="icon-save"></i> 保存</button> <button type="reset" class="btn input-small"><i class="icon-refresh"></i> 重置</button>
                            </td>
                </tr>
            </table>
        </form>
    </div>
    <?php echo $this->fetch('public/js');?>
    <script type="text/javascript">
                var discounts;
                $(document).ready(function () {
        //post_sisyphus();
        $('#autocomplete').autocomplete({
        serviceUrl: "<?php echo $this->url->site('json/product');?>",
                onSelect: function (suggestion) {
                $('#productid').val(suggestion.id);
                        $('#wayso').val('so'); $('form').attr('ajax', 'no')
                        $('form').submit();
                }
        });
                $('#automember').autocomplete({
        serviceUrl: "<?php echo $this->url->site('json/member');?>",
                onSelect: function (suggestion) {
                //discounts = suggestion.discounts;
                $('#memberuid').val(suggestion.id);
                        //$('form').submit();
                        //$('.discounts').val(discounts);
                        //$('#productname').val(suggestion.value);
                }
        });
        });
                function calculatemoney(price, quantity, discounts, money) {
                var quantitynum = $('#' + quantity).val();
                        var discountsnum = $('#' + discounts).val();
                        if (!quantitynum.match(new RegExp("^[0-9]+$")) || quantitynum <= 0){
                $('#' + quantity).val('1');
                        quantitynum = 1;
                }

                if (discountsnum === '' || discountsnum < 0 || discountsnum > 10){
                $('#' + discounts).val('');
                        discountsnum = 10;
                }
//((price * quantitynum) * (discountsnum / 10)).toFixed(2)
                price = ((price * quantitynum) * (Number(discountsnum * 10) / 100)).toFixed(2);
                        //alert(price);
                        $('#' + money).html(price);
                        sum();
                }
        function sum(){
        var sumnum = 0;
                for (var i = 0; i < <?php echo count($product);?>; i++){
        var check = $('#money' + i).html().match(/[-+]?\d+/g);
                if (check != null){
        sumnum = sumnum + Number($('#money' + i).html());
        }
        }
        $('#productsummoney').html(sumnum.toFixed(2));
        }
    </script>
</body>
</html>