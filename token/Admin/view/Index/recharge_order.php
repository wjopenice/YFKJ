<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>充币订单</title>
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
    <span class="layui-breadcrumb"><a href="">充币订单</a></span>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space5">
                        <div class="layui-inline layui-show-xs-block" style="width: 500px;">
                            <select name="cateid">
                                <option>BTC</option>
                                <option>ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b3952</option>
                                <option>ETH_USDT_0xdac17f958d2ee523a2206206994597c13d</option>
                                <option>EOS</option>
                            </select>
                        </div>
                        <div class="layui-inline layui-show-xs-block" style="width: 300px;">
                            <input type="text" name="username" placeholder="平台单号" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block" style="width: 300px;">
                            <input type="text" name="username" placeholder="冷钱包地址" autocomplete="off" class="layui-input">
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <button class="layui-btn"  lay-submit="" lay-filter="sreach">确认</button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body">
                    <table class="layui-table layui-form" id="thistable" lay-filter="thistable" class="layui-table" lay-data="{skin: 'row', even: true, page:true,limit:10, url:'/Admin/Ajax/recharge_order?tel=<?=$_SESSION['tel']?>'}">
                        <thead>
                        <tr>
                            <th lay-data="{field:'uniquekey'}">平台单号</th>
                            <th lay-data="{field:'address'}">冷钱包地址</th>
                            <th lay-data="{field:'coinType'}">币种</th>
                            <th lay-data="{field:'qty'}">数量</th>
                            <th lay-data="{field:'createtime'}">时间</th>
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
    layui.use(['table','table','laydate', 'form'], function() {
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