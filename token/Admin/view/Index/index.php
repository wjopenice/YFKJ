<!doctype html>
<html class="x-admin-sm">
    <head>
        <meta charset="UTF-8">
        <title>FYPay</title>
        <meta name="renderer" content="webkit|ie-comp|ie-stand">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
        <meta http-equiv="Cache-Control" content="no-siteapp" />
        <link rel="stylesheet" href="<?=ROOT?>public/Admin/css/font.css">
        <link rel="stylesheet" href="<?=ROOT?>public/Admin/css/xadmin.css">
        <script src="<?=ROOT?>public/Admin/lib/layui/layui.js" charset="utf-8"></script>
        <script type="text/javascript" src="<?=ROOT?>public/Admin/js/xadmin.js"></script>
        <script>
            // 是否开启刷新记忆tab功能
            // var is_remember = false;
        </script>
    </head>
    <body class="index">
        <!-- 顶部开始 -->
        <div class="container">
            <div class="logo">
                <a href="/Website/Index/login">FYPay</a></div>
            <div class="left_open">
                <a><i title="展开左侧栏" class="iconfont">&#xe699;</i></a>
            </div>
            <ul class="layui-nav right" lay-filter="">
                <li class="layui-nav-item">
                    <a href="javascript:;"><?=$_SESSION['tel']?></a>
                    <dl class="layui-nav-child">
                        <!-- 二级菜单 -->
                        <dd><a href="/Admin/Index/loginout">退出</a></dd>
                    </dl>
                </li>
            </ul>
        </div>
        <!-- 顶部结束 -->
        <!-- 中部开始 -->
        <!-- 左侧菜单开始 -->
        <div class="left-nav">
            <div id="side-nav">
                <ul id="nav">
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('账户信息','/Admin/Index/welcome')">
                            <i class="iconfont left-nav-li" lay-tips="账户信息">&#xe6b8;</i>
                            <cite>账户信息</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('API密钥信息','/Admin/Index/order_list')">
                            <i class="iconfont left-nav-li" lay-tips="API信息">&#xe749;</i>
                            <cite>API信息</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('开通币种','/Admin/Index/currency_open')">
                            <i class="iconfont left-nav-li" lay-tips="币种列表">&#xe70c;</i>
                            <cite>开通币种</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('热钱包信息','/Admin/Index/hot_wallet')">
                            <i class="iconfont left-nav-li" lay-tips="热钱包信息">&#xe6f6;</i>
                            <cite>热钱包信息</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('冷钱包信息','/Admin/Index/cold_wallet')">
                            <i class="iconfont left-nav-li" lay-tips="冷钱包信息">&#xe6f4;</i>
                            <cite>冷钱包信息</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('充币订单','/Admin/Index/recharge_order')">
                            <i class="iconfont left-nav-li" lay-tips="充币订单">&#xe6ce;</i>
                            <cite>充币订单</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('提币订单','/Admin/Index/withdrawal_order')">
                            <i class="iconfont left-nav-li" lay-tips="提币订单">&#xe6e8;</i>
                            <cite>提币订单</cite>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="xadmin.add_tab('接口调试','/Admin/Index/api_test')">
                            <i class="iconfont left-nav-li" lay-tips="接口调试">&#xe6e8;</i>
                            <cite>接口调试</cite>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- <div class="x-slide_left"></div> -->
        <!-- 左侧菜单结束 -->
        <!-- 右侧主体开始 -->
        <div class="page-content">
            <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
                <ul class="layui-tab-title">
                    <li class="home">
                        <i class="layui-icon">&#xe68e;</i>账户信息</li></ul>
                <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
                    <dl>
                        <dd data-type="this">关闭当前</dd>
                        <dd data-type="other">关闭其它</dd>
                        <dd data-type="all">关闭全部</dd></dl>
                </div>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <iframe src='/Admin/Index/welcome' frameborder="0" scrolling="yes" class="x-iframe"></iframe>
                    </div>
                </div>
                <div id="tab_show"></div>
            </div>
        </div>
        <div class="page-content-bg"></div>
        <style id="theme_style"></style>
        <!-- 右侧主体结束 -->
        <!-- 中部结束 -->
    </body>

</html>