var readyHandler = function () {
    var size = ferosClient.getScreenSize();
    var x = Math.floor((size.width - 646) / 2);
    var y = Math.floor((size.height - 425) / 2);
    ferosClient.setSize(646, 425);
    ferosClient.move(x, y);
}
addEventListener("ferosClientReady", readyHandler);


$(document).ready(function () {
//    $.cookie('feros_url', feros_url, {path: '/'});
//    $.cookie('feros_authorize', feros_authorize, {path: '/'});
//    $.cookie('feros_key', feros_key, {path: '/'});
    form_post();
});

function form_post() {
    $('form').submit(function () {
        if ($('#account').val() == '') {
            toastr.error('请输入登录账号');
            return false;
        }
        if ($('#password').val() == '') {
            toastr.error('请输入登录密码');
            return false;
        }
        if ($(this).attr('method') === 'post') {
            submit = $(":submit", this);
            submit.attr("disabled", "true");
            $('.loading').show();
            $.post($(this).attr('action'), $(this).serialize() + '&feros_client=true', function (data) {
                $('.loading').hide();
                submit.removeAttr('disabled');
                if (data.status === 1) {

                    if (data.url !== '') {
                        $.scojs_message(data.message, $.scojs_message.TYPE_OK);
//                        ferosClient.max();
                        ferosClient.runApp("../app/feros.app");
                        ferosClient.close();
                        //ferosClient.loadUrl(data.url);

                        //ferosClient.runApp(index_app);
                        //ferosClient.runAppEx("../music/index.app");

                    } else {
                        toastr.error('服务器参数出错');
                    }
                }
                else
                if (data.status === 0) {
                    toastr.error(data.message);
                }
            }, "json").error(function (data) {
                $('.loading').hide();
                submit.removeAttr('disabled');
                toastr.warning('连接服务器时发生错误', '请求中断');
            });
            return false;
        }
        return false;
    });
    $('.loading').hide();
}
