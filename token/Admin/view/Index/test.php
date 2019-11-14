<!DOCTYPE html>
<html class="x-admin-sm">

<head>
    <meta charset="UTF-8">
    <title>冷钱包信息</title>
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
    <span class="layui-breadcrumb"><a href="">冷钱包信息</a></span>
</div>
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    <table class="layui-table layui-form">
                        <thead>
                        <tr>
                            <th>用户ID</th>
                            <th>Coin Type</th>
                            <th>冷钱包地址</th>
                            <th>余额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for($i=0;$i<count($data);$i++):
                            $key = $data[$i]['username'];
                            ?>
                            <?php foreach ($data[$i]['coin'] as $k=>$v): ?>
                            <tr>
                                <td title="<?=$key?>"><?=$key?></td>
                                <td><?=$v['coinname']?></td>
                                <td><?=$v['address']?></td>
                                <td><?=$v['num']?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>