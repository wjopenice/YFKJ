<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>修改</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="/public/Admin/css/font.css">
    <link rel="stylesheet" href="/public/Admin/css/xadmin.css">
    <script type="text/javascript" src="/public/Admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/public/Admin/js/xadmin.js"></script>
</head>

<body>
<div class="layui-fluid">
    <div class="layui-row">
        <form class="layui-form">
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"><span class="x-red">*</span>回调地址</label>
                <div class="layui-input-inline" style="width: 300px">
                    <input type="url" id="L_repass" name="api_address" required="" lay-verify="required" autocomplete="off" class="layui-input" value="<?=$data['api_address']?>">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"></label>
                <input type="hidden" name="merc_id" value="<?=$data['merc_id']?>">
                <a class="layui-btn" lay-filter="save" lay-submit="">确认</a></div>
        </form>
    </div>
</div>
<script>layui.use(['form', 'layer'],
    function() {
        $ = layui.jquery;
        var form = layui.form,
            layer = layui.layer;

        //监听提交
        form.on('submit(save)',
            function(data) {
                $.post("/Admin/Index/order_edit",data.field,function (msg) {
                     if(msg == 1){
                         //发异步，把数据提交给php
                         layer.alert("修改成功", {
                                 icon: 6
                             },
                             function() {
                                 // 获得frame索引
                                 var index = parent.layer.getFrameIndex(window.name);
                                 //关闭当前frame
                                 parent.location.reload();
                                 parent.layer.close(index);
                             });
                         return false;
                     }else{
                         layer.msg("修改失败");
                     }
                });
            });
    });
</script>
<script>var _hmt = _hmt || []; (function() {
    var hm = document.createElement("script");
    hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(hm, s);
})();</script>
</body>