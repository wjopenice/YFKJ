<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>开通币种</title>
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
    <span class="layui-breadcrumb"><a href="">开通币种</a></span>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body ">
                    <form class="layui-form layui-col-space5">
                        <div class="layui-inline layui-show-xs-block">
                            <select name="cateid">
                                <option>全部</option>
                                <option>ETH</option>
                                <option>BTC</option>
                                <option>EOS</option>
                            </select>
                        </div>
                        <div class="layui-inline layui-show-xs-block">
                            <button class="layui-btn"  lay-submit="" lay-filter="sreach">确认</button>
                        </div>
                    </form>
                </div>
                <div class="layui-card-body">
                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>币名</th>
                            <th>Coin Type</th>
                            <th>公链体系</th>
                            <th>开通状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $k=>$v): ?>
                        <tr>
                            <td><?=$v['name']?></td>
                            <td><?=$v['coin_type']?></td>
                            <td><?=$v['type']?></td>
                            <?php if($v['status']==1): ?>
                                <td>已开通</td>
                            <?php else: ?>
                                <td><button type="button" class="layui-btn layui-btn-fluid" style="width: 80px;" onclick="hh()">未开通</button></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>BTC</td>
                            <td>BTC</td>
                            <td>BTC</td>
                            <td><button type="button" class="layui-btn layui-btn-fluid" style="width: 80px;" onclick="hh()">未开通</button></td>
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
    layui.use(['laydate', 'form'], function() {
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });

    });
    function hh() {
        layer.msg("xxxxx");
        // layer.open({
        //     type: 1
        //     , offset: 'auto' //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
        //     , id: 'layerDemo' //防止重复弹出
        //     , content: '<div style="padding: 20px 100px;">' + 123 + '</div>'
        //     , btn: '关闭全部'
        //     , btnAlign: 'c' //按钮居中
        //     , shade: 0 //不显示遮罩
        //     , yes: function () {
        //         layer.closeAll();
        //     }
        // });
    }
</script>
</html>