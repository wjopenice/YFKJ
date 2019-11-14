<!doctype html>
<html  class="x-admin-sm">
<head>
	<meta charset="UTF-8">
	<title>YFPay</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="stylesheet" href="/public/Admin/css/font.css">
    <link rel="stylesheet" href="/public/Admin/css/login.css">
	  <link rel="stylesheet" href="/public/Admin/css/xadmin.css">
    <script type="text/javascript" src="/public/Admin/js/jquery.min.js"></script>
    <script src="/public/Admin/lib/layui/layui.js" charset="utf-8"></script>
    <style>.hide{display: none; }</style>
</head>
<body class="login-bg">

    <div class="login layui-anim layui-anim-up">
        <form method="post" class="layui-form">
            <input type="button" value="去授权" lay-submit lay-filter="login" style="width:100%;font-size: 18px;margin-bottom: 100px;margin-top: 100px;" id="empower">
            <input type="button" value="下一步" lay-submit lay-filter="login" class="hide" style="width:100%;font-size: 18px;margin-bottom: 100px;margin-top: 100px;" id="link" onclick="return sendlink();">
            <input type="password" id="pass" placeholder="请输入6位授权码" value="" >
            
            <p class="hide" id="divp" style="margin-bottom: 10px;color: #e65f5f;font-size: 18px;">请保存好以下助记词(商户平台不做保存一旦丢失自行承担)：</p>
            <textarea id="textarea" class="hide" style="width: 330px;
    height: 120px;display: block;font-weight: bold;font-size: 16px;padding: 4px;"></textarea>

            <hr class="hr15">
        </form>
    </div>

    <script>
        function sendlink() {
            var mymessage = confirm('确认助记词已经保存好了？');
            if(mymessage==true){
                window.location.href = '/Admin/Index/index';
            }else{
                
            }
        }
        $(function(){
           $("#empower").click(function () {
               var tel = "<?=$_SESSION['tel']?>";
               var pass = $("#pass").val();
               if(pass.length < 6){
                    alert("请输入大于6位的授权码");
                    return false;
               }
               $.post("/Admin/Ajax/token_register",{tel:tel,pass:pass},function(msg){
                   if(msg.code == 1){
                       $("#textarea").css("display","block")
                       $("#link").css("display","block")
                       $("#divp").css("display","block")

                       $("#empower").css("display","none")
                       $("#pass").css("display","none")
                       $("#textarea").val(msg.mnemonic);
                   }else{
                       alert("授权失败");
                   }
               },"json");
           });
        });
    </script>
    <!-- 底部结束 -->
</body>
</html>
