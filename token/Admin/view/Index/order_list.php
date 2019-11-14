<!DOCTYPE html>
<html class="x-admin-sm">
    
    <head>
        <meta charset="UTF-8">
        <title>API密钥信息</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
        <link rel="stylesheet" href="/public/Admin/css/font.css">
        <link rel="stylesheet" href="/public/Admin/css/xadmin.css">
        <script src="/public/Admin/lib/layui/layui.js" charset="utf-8"></script>
        <script type="text/javascript" src="/public/Admin/js/xadmin.js"></script>
    </head>
    
    <body>
        <div class="x-nav">
            <span class="layui-breadcrumb">
                <a href="">API密钥信息</a>
            </span>
        </div>
        <div class="layui-fluid">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-body ">
                            <table class="layui-table layui-form">
                                <thead>
                                    <tr>
                                        <th style="width: 30%;">App key</th>
                                        <th style="width: 30%;">Secret key</th>
                                        <th style="width: 30%;">回调地址</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?=$data['app_key']?></td>
                                        <td>
                                            <?php if($data['secret'] == 0): ?>
                                                <a title="查看/重置" onclick="xadmin.open('查看','/Admin/Index/order_add?id=<?=$data['merc_id']?>',500,300)" href="javascript:;"><i class="layui-icon" style="color: blue;">查看</i></a>
                                            <?php else: ?>
                                                <?=$data['secret']?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?=$data['api_address']?> <a title="修改" onclick="xadmin.open('修改','/Admin/Index/order_edit?id=<?=$data['merc_id']?>',500,300)" href="javascript:;" style="color: blue;"><i class="layui-icon">修改</i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="layui-card-body ">
                            <table class="layui-table layui-form">
                                <thead>
                                <tr>
                                    <th>商户自己的 RSA PUBLIC KEY</th>
                                    <th>商户自己的 RSA PRIVATE KEY</th>
                                    <th>平台 RSA PUBLIC KEY</th>
                                </tr>
                                </thead>
                                <tbody>            
                                <tr>
                                    <td style="width: 30%;">
                                        <a href="<?=$data['rsapub']?>" style="color: blue;">RSA PUBLIC KEY下载</a>
                                    </td>
                                    <td style="width: 30%;">
                                        <a href="<?=$data['rsapri']?>" style="color: blue;">RSA PRIVATE KEY下载</a>
                                    </td>
                                    <td style="width: 30%;">
                                        <a href="/yf_rsa_public_key.pem" style="color: blue;">平台 RSA PUBLIC KEY下载</a>
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
    <script>layui.use(['table','laydate', 'form'],
        function() {
            var laydate = layui.laydate;

            //搜索，重新加载
            var  active = {
                reload: function(){
                    //执行重载
                    table.reload('thistable', {
                        page: {
                            curr: 1 //重新从第 1 页开始
                        }
                        ,where: {
                            search: {
                                title: $('input[name="title"]').val(),

                            }
                        }
                    });
                }
            };

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