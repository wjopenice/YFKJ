<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FY PAY</title>
    <link rel="stylesheet" href="/public/Website/css/public.css">
    <link rel="stylesheet" href="/public/Website/css/login.css">
    <script src="/public/Website/js/jquery.min.js"></script>
    <script src="/public/Website/js/layer.js"></script>
</head>
<body>
    <div class="box">
        <div class="content">
            <div class="log-box warp-1200 ov pr">
                <div class="fl l-ban"><img src="/public/Website/images/image_zhuce.png" height="544" width="633" alt=""></div>
                <div class="fr login-warp">
                    <p class="tit">YF Pay-商户登录</p>
                    <form action="/Website/Index/login" method="post">
                        <div class="input-box"><i></i><input type="text" name="tel" class="username" placeholder="请输入手机号"></div>
                        <div class="input-box input-box1"><i></i><input type="password"  name="password" class="username" placeholder="请输入您的登录密码"></div>
                        <button class="btn">立即登录</button>
                    </form>
                    <div class="go-to"><span>没有账户？</span><a href="/Website/Index/register">马上注册</a></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
