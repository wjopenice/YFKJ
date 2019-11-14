<!DOCTYPE html>
<html class="x-admin-sm">
<head>
    <meta charset="UTF-8">
    <title>账户信息</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="<?=ROOT?>public/Admin/css/font.css">
    <link rel="stylesheet" href="<?=ROOT?>public/Admin/css/xadmin.css">
    <script src="<?=ROOT?>public/Admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="<?=ROOT?>public/Admin/js/xadmin.js"></script>
</head>
<body>
<div class="x-nav">
          <span class="layui-breadcrumb">
            <a href="">账户信息</a>
          </span>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body layui-table-body layui-table-main">
                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>手机号</th>
                            <th>身份证号</th>
                            <th>密码</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?=$data['merc_name']?></td>
                            <td><?=$data['merc_cid']?></td>
                            <td>
                                <a onclick="xadmin.open('修改密码','/Admin/Index/member_password?id=<?=$data['merc_id']?>',600,400)" title="修改密码" href="javascript:;">
                                    <i class="layui-icon">修改密码</i>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['laydate','form'], function(){
        var laydate = layui.laydate;
        var  form = layui.form;


        // 监听全选
        form.on('checkbox(checkall)', function(data){

            if(data.elem.checked){
                $('tbody input').prop('checked',true);
            }else{
                $('tbody input').prop('checked',false);
            }
            form.render('checkbox');
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });

    });

</script>
</html>