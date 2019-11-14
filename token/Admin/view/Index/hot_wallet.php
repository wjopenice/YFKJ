<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>热钱包信息</title>
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
    <span class="layui-breadcrumb"><a href="">热钱包信息</a></span>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-table layui-form" id="thistable" lay-filter="thistable" class="layui-table" lay-data="{skin: 'row', even: true, page:true,limit:10, url:'/Admin/Ajax/hot_wallet?tel=<?=$_SESSION['tel']?>'}">
                        <thead>
                        <tr>
                            <th lay-data="{field:'coinType'}">Coin Type</th>
                            <th lay-data="{field:'address'}">热钱包地址</th>
                            <th lay-data="{field:'qty'}">余额</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    layui.use(['table','layer'], function() {
        var table = layui.table, $ = layui.$, form = layui.form;

        //监听单元格事件
        table.on('tool(thistable)', function(obj){
            var data = obj.data;
            if(obj.event === 'edit'){

            } else if (obj.event === 'del')
            {

            }
        });

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

        $('.searchTable .layui-btn').on('click', function(){
            var type = $(this).data('type');
            active[type] ? active[type].call(this) : '';
        });




    });

</script>
</html>