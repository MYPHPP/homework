+function ($) {
    'use strict';
    var DataKey = 'lte.nouse';
    var Default = {
        source         : '',
        params         : {},
        trigger        : '.nouse',
        content        : '.box-body-nouse',
        loadInContent  : false,
        responseType   : '',
        overlayTemplate: '<div class="overlay-nouse"><div class="fa fa-refresh fa-spin"></div></div>',
        onLoadStart    : function () {
        },
        onLoadDone     : function (response) {
            return response;
        }
    };
    var Selector = {
        data: '[data-widget="box-refresh-nouse"]'
    };
    // BoxRefresh Class Definition
    // =========================
    var nouse = function (element, options) {
        if (options.source === '') {
            throw new Error('Source url was not defined. Please specify a url in your BoxRefresh source option.');
        }
    };
}(jQuery);

var IcheckBox = function () {
    //Icheck
    var _masterCheckBox;
    var _CheckBox;

    /*激活ICheck*/
    var handlerInitIcheckBox = function () {
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: ' icheckbox_minimal-grey',
            radioClass: 'iradio_minimal-grey'
        });
    };
    /*控制全选*/
    var handllerIcheckAll = function () {
        _masterCheckBox = $('input[type="checkbox"].minimal.checkbox-master');
        _CheckBox = $('input[type="checkbox"].minimal.checkbox-son');
        _masterCheckBox.on("ifClicked", function (e) {
            if (e.target.checked) {
                //返回true表示未选中
                _CheckBox.iCheck('uncheck');
            } else {
                //选中状态
                _CheckBox.iCheck('check');
            }
        });
    };
    /*反选*/
    var handlerReIcheck = function () {
        _CheckBox.on("ifChanged", function (e) {
            var lengths = _CheckBox.length;
            //选择数量等于全部数量
            if (_CheckBox.filter(':checked').length == lengths) {
                _masterCheckBox.prop('checked', true);
            } else {
                _masterCheckBox.prop('checked', false);
                //也可以用下面写法
                /* _masterCheckBox.removeProp('checked');*/
            }
            _masterCheckBox.iCheck('update');
        })
    };

    return {
        initIcheckBox: function () {
            handlerInitIcheckBox();
            handllerIcheckAll();
            handlerReIcheck();
        }
    }
}();

function ajaxConfirm(tips,data,url,type="POST",datatype="json"){
    layer.confirm(tips,{
        title:"确认操作",
        icon: 3,
        btn: ['是','否'] //按钮
    },function () {
        $.ajax({
            type:type,
            url:url,
            data:data,
            dataType:datatype,
            success:function (e) {
                if(e.code == 1){
                    layer.msg(e.msg,{icon:1});
                    setTimeout("location.reload();", 1000);
                }else{
                    layer.msg(e.msg,{icon:2});
                }
            },
            error(xhr, type, errorThrown){
                layer.msg('访问错误,代码'+xhr.status,{icon:2});
            }
        });
    });
}

function changePageRows(t){
    var val = $(t).val();
    var url = $(t).attr('data-url');
    $.post(url,{row:val},function (res,status) {
        window.location.reload();
    });
}

/*设置cookie*/
function setCookie(key,value,t)
{
    var oDate=new Date();
    oDate.setDate(oDate.getDate()+t);
    document.cookie=key+"="+value+"; expires="+oDate.toDateString()+"; path=/";
}

/*获取cookie*/
function getCookie(key){
    var arr1=document.cookie.split("; ");
    for(var i=0;i<arr1.length;i++){
        var arr2=arr1[i].split("=");
        if(arr2[0]==key){
            return decodeURI(arr2[1]);
        }
    }
}


//封装一个移除cookie的函数
function removeCookie(key){
    setCookie(key,"",-1);//把cookie设置为过期
}

$(function () {
    IcheckBox.initIcheckBox();
    $('.select2').select2();
    $('.delall-confirm').on('click',function () {
        var tips = '是否确认批量删除？';
        var url = $(this).attr('data-url');
        var data = [];
        $('.checkbox-son').each(function() {
            if($(this).is(':checked')){
                data.push($(this).val());
            }
        });
        if(data.length < 1){
            layer.msg('请选择要处理的数据',{icon:2});
            return;
        }
        var senddata = {ids:data};
        ajaxConfirm(tips,senddata,url);
    });

    $('.choose-del').on('click',function () {
        var tips = '是否确认删除改数据？';
        var id = $(this).attr('data-id');
        var url = $(this).attr('data-url');
        id = id*1;
        if(id < 1){
            layer.msg('请正确选择要处理的数据',{icon:2});
        }
        var data = {id:id};
        ajaxConfirm(tips,data,url);
    });
    $('.ajaxForm').on('submit',function () {
        event.preventDefault();
        layer.msg('正在提交，请稍候…', {icon: 16, time: 0, shade: [0.3, "#000"]});
        var form_action = $(this).attr('action');
        var form_method = $(this).attr('method');
        var form_data = new FormData($(this)[0]);
        $.ajax({
            url: form_action,
            dataType: 'json',
            type: form_method,
            data: form_data,
            contentType: false,
            processData: false,
            success: function (result) {
                var code = result.code ? result.code : 2;
                layer.msg(result.msg,{icon:code});
                if(code == 1){
                    setTimeout("location.reload();", 1000);
                }
            },
            error: function (xhr, type, errorThrown) {
                //异常处理；
                console.log('%csubmit fail!', ';color:#dd4b39');
                console.log("type:" + type + ",readyState:" + xhr.readyState + ",status:" + xhr.status);
                console.log("url:" + form_action);
                console.log("data:" + form_data);
                layer.msg('访问错误,代码'+xhr.status,{icon:2});
            }
        });
    });
});