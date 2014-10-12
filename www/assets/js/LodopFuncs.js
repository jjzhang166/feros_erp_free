var CreatedOKLodop7766 = null;

function getLodop(oOBJECT, oEMBED) {
    var strHtmInstall = "打印控件未安装 请访问官网下载";
    var strHtmUpdate = "打印控件需要升级 请访问官网下载";
    var strHtm64_Install = "打印控件未安装 请访问官网下载";
    var strHtm64_Update = "打印控件需要升级 请访问官网下载";
    var strHtmFireFox = "注意：如曾安装过旧版，请先卸它";
    var strHtmChrome = "如果此前正常，请联系费尔软件技术专员）";
    var LODOP;
    try {
        //=====判断浏览器类型:===============
        var isIE = (navigator.userAgent.indexOf('MSIE') >= 0) || (navigator.userAgent.indexOf('Trident') >= 0);
        var is64IE = isIE && (navigator.userAgent.indexOf('x64') >= 0);
        //=====如果页面有Lodop就直接使用，没有则新建:==========
        if (oOBJECT != undefined || oEMBED != undefined) {
            if (isIE)
                LODOP = oOBJECT;
            else
                LODOP = oEMBED;
        } else {
            if (CreatedOKLodop7766 == null) {
                LODOP = document.createElement("object");
                LODOP.setAttribute("width", 0);
                LODOP.setAttribute("height", 0);
                LODOP.setAttribute("style", "position:absolute;left:0px;top:-100px;width:0px;height:0px;");
                if (isIE)
                    LODOP.setAttribute("classid", "clsid:2105C259-1E0C-4534-8141-A753534CB4CA");
                else
                    LODOP.setAttribute("type", "application/x-print-lodop");
                document.documentElement.appendChild(LODOP);
                CreatedOKLodop7766 = LODOP;
            } else
                LODOP = CreatedOKLodop7766;
        }
        ;
        //=====判断Lodop插件是否安装过，没有安装或版本过低就提示下载安装:==========
        if ((LODOP == null) || (typeof (LODOP.VERSION) == "undefined")) {
            if (navigator.userAgent.indexOf('Chrome') >= 0)
                window.parent.toastr.warning(strHtmChrome);
            if (navigator.userAgent.indexOf('Firefox') >= 0)
                window.parent.toastr.warning(strHtmFireFox);
            if (is64IE)
                window.parent.toastr.warning(strHtm64_Install);
            else
            if (isIE)
                window.parent.toastr.warning(strHtmInstall);
            else
                window.parent.toastr.warning(strHtmInstall);
            return LODOP;
        } else
        if (LODOP.VERSION < "6.1.9.0") {
            if (is64IE)
                window.parent.toastr.warning(strHtm64_Update);
            else
            if (isIE)
                window.parent.toastr.warning(strHtmUpdate);
            else
                window.parent.toastr.warning(strHtmUpdate);
            return LODOP;
        }
        ;
        LODOP.SET_LICENSES("", "280979910649981105612890038639", "688858710010010811411756128900", "");
        return LODOP;
    } catch (err) {
        if (is64IE)
            window.parent.toastr.warning(strHtm64_Install);
        else
            window.parent.toastr.warning(strHtmInstall);
        return LODOP;
    }
    ;
}
