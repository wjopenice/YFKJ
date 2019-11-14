<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FY PAY</title>
    <link rel="stylesheet" href="/public/Website/css/public.css">
    <link rel="stylesheet" href="/public/Website/css/register.css">
    <link rel="stylesheet" href="/public/font-awesome/css/font-awesome.min.css">
    <style>
        .toast{
            position: absolute;
            width: 240px;
            height: 40px;
            background: #333333ad;
            text-align: center;
            line-height: 40px;
            color: #fff;
            font-size: 14px;
            top: 50%;
            margin-top: -20px;
            left: 50%;
            margin-left: -120px;
            border-radius: 6px;
            display: none;

        }
        .fa{display: none;}
        .fa-check{color: #00FF00}
        .fa-times{color: red}
    </style>
</head>
<body>
<div class="box">
    <div class="content">
        <div class="log-box warp-1200 ov pr">
            <div class="fl l-ban"><img src="/public/Website/images/image_zhuce.png" height="544" width="633" alt=""></div>
            <div class="fr login-warp pr">
                <form id="form1" name="form1" action="/Website/Index/register" method="post">
                    <p class="tit">YF Pay-商户注册</p>
                    <div class="input-box">
                        <i></i>
                        <input type="tel" name="tel" id="tel" placeholder="请输入手机号" required>
                        <span style="display: inline-block;width: 50px;position: absolute;right: -18px;top: 14px;">
                            <a class="fa fa-check" id="tel1" aria-hidden="true"></a>
                        </span>
                    </div>
                    <div class="input-box input-box3"><i></i>
                        <input type="text" name="merc_cid" id="merc_cid" placeholder="请输入身份证号" required>
                        <span style="display: inline-block;width: 50px;position: absolute;right: -18px;top: 14px;">
                            <a class="fa fa-check" id="merc_cid1" aria-hidden="true"></a>
                        </span>
                    </div>
                    <div class="input-box input-box1"><i></i>
                        <input type="password" name="password" id="password" placeholder="设置大于6位数密码" required>
                        <span style="display: inline-block;width: 50px;position: absolute;right: -18px;top: 14px;">
                            <a class="fa fa-check" id="password1" aria-hidden="true"></a>
                        </span>
                    </div>
                    <div class="input-box input-box1"><i></i>
                        <input type="password" name="passwordRepeat" id="passwordRepeat" placeholder="确认密码" required>
                        <span style="display: inline-block;width: 50px;position: absolute;right: -18px;top: 14px;">
                            <a class="fa fa-check" id="passwordRepeat1" aria-hidden="true"></a>
                        </span>
                    </div>
                    <div class="input-box input-box2"><i></i>
                        <input type="text" name="code" id="code" placeholder="请输入验证码" required value="">
                        <span id="dyMobileButton">
                            <img src="/Website/Index/code" onclick="this.src='/Website/Index/code?data='+Math.random()" style="margin-right: 20px;" />
                        </span>
                        <span style="display: inline-block;width: 50px;position: absolute;right: -18px;top: 14px;">
                            <a class="fa fa-check" id="code1" aria-hidden="true"></a>
                        </span>
                    </div>
                    <button id="submit" type="submit" class="btn">注册</button>
                </form>
                <div class="go-to"><span>已有账户？</span><a href="/Website/Index/login">马上登录</a></div>
                <p class="toast">hhhhhhhhhh</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<script src="/public/Website/js/jquery.min.js"></script>
<script>
    var a = 0;
    var b = 0;
    var c = 0;
    var d = 0;
    $(function(){
        $("#tel").keyup(function () {
            var data = $(this).val();
            let exp = /^1[3-9]\d{9}$/;
            if(exp.test(data)){
                $("#tel1").css("display","inline-block")
                a = 1;
            }else{
                $("#tel1").css("display","none")
                a = 0;
            }
        });
        $("#merc_cid").keyup(function () {
            var data = $(this).val();
            let exp = /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|x)$/;
            if(exp.test(data)){
                $("#merc_cid1").css("display","inline-block")
                b = 1;
            }else{
                $("#merc_cid1").css("display","none")
                b = 0;
            }
        });
        $("#password").keyup(function () {
            var data = $(this).val();
            let len = data.length;
            if(len >= 6){
                $("#password1").css("display","inline-block")
            }else{
                $("#password1").css("display","none")
            }
        });

        $("#passwordRepeat").keyup(function () {
            var data = $(this).val();
            var redata = $("#password").val();
            if(redata.length >= 6){
                if(data == redata){
                    $("#passwordRepeat1").css("display","inline-block")
                    c = 1;
                }else{
                    $("#passwordRepeat1").css("display","none")
                    c = 0;
                }
            }else{
                $("#passwordRepeat1").css("display","none")
                c = 0;
            }
        });

        $("#code").keyup(function () {
            var data = $(this).val();
            $.post("/Website/Index/ajaxcode",{d:data},function (msg) {
                console.log(msg);
                 if(msg.code == 1){
                     $("#code1").css("display","inline-block")
                     d = 1;
                 }else{
                     $("#code1").css("display","none")
                     d = 0;
                 }
            },"json");
        });
        
        $("#submit").click(function () {
            if(a == 0){
                $(".toast").show();
                $(".toast").html("手机号错误");
                window.setTimeout(()=>{
                    $(".toast").hide();
                },2000)
                return false;
            }
            if(b == 0){
                $(".toast").show();
                $(".toast").html("身份证错误");
                window.setTimeout(()=>{
                    $(".toast").hide();
                },2000)
                return false;
            }
            if(c == 0){
                $(".toast").show();
                $(".toast").html("两次密码不一致");
                window.setTimeout(()=>{
                    $(".toast").hide();
                },2000)
                return false;
            }
            if(d == 0){
                $(".toast").show();
                $(".toast").html("验证码错误");
                window.setTimeout(()=>{
                    $(".toast").hide();
                },2000)
                return false;
            }

            if(a == 1 && b == 1 && c == 1 && d == 1){
                $("#form1").submit();
            }

        });
    });
</script>
