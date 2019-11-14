<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>查看/重置</title>
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
                <label for="L_username" class="layui-form-label"><span class="x-red">*</span>手机号</label>
                <div class="layui-input-inline"><input type="tel" id="L_username" name="merc_name" required="" value="" class="layui-input"></div>
            </div>
            <div class="layui-form-item">
                <label for="L_repass" class="layui-form-label"><span class="x-red">*</span>身份证号</label>
                <div class="layui-input-inline"><input type="text" id="L_repass" name="merc_cid" required="" lay-verify="required" autocomplete="off" class="layui-input" value=""></div>
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
                $.post("/Admin/Index/order_add",data.field,function (msg) {
                    if(msg == 0){
                        layer.msg("修改失败");
                    }else {
//                        $(".layui-form").empty();
//                        var str = "<div class='layui-form-item'>";
//                            str += "<label for='L_username' class='layui-form-label'>secret_key</label>";
//                            str += "<div class='layui-input-inline' style='width: 350px;'><input type='text' class='layui-input' value='"+msg+"'></div>";
//                            str += "</div>";
//                            $(".layui-form").html(str);
//                        }
                        // 获得frame索引
                        var index = parent.layer.getFrameIndex(window.name);
                        //关闭当前frame
                        parent.location.reload();
                        parent.layer.close(index);
                    }
                });
            });

    });
</script>
</body>