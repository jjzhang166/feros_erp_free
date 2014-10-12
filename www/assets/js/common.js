$(document).ready(function () {
    form_post();
    $('form').attr('autocomplete', 'off');
});

function form_post() {
    $('form').submit(function () {
        if ($(this).attr('method') === 'post' && $(this).attr('ajax') !== 'no') {
            $('.loading').show();
            submit = $(":submit", this);
            submit.attr("disabled", "true");
            $.post($(this).attr('action'), $(this).serialize(), function (data) {
                $('.loading').hide();
                submit.removeAttr('disabled');
                if (data.status === 1) {
                    window.parent.toastr.success(data.message);
                    if (data.url !== '') {
                        url = data.url;
                    } else {
                        url = location.href;
                    }
                    setTimeout("self.location=url", 1000);
                }
                else
                if (data.status === 0) {
                    window.parent.toastr.error(data.message);
                }
            }, "json").error(function (data) {
                $('.loading').hide();
                submit.removeAttr('disabled');
                window.parent.toastr.warning('服务器连接时发现错误');
            });
            return false;
        }
    });
    $('.loading').hide();
}
function ajax_post(action, serialize) {
    $.post(action, serialize, function (data) {
        if (data.status === 1) {
            window.parent.toastr.success(data.message);
        }
        else
        if (data.status === 0) {
            window.parent.toastr.error(data.message);
        }
    }, "json").error(function (data) {
        window.parent.toastr.warning('服务器连接时发现错误');
    });
}
