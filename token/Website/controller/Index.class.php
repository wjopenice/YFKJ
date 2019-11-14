<?php

namespace Website\controller;
use \ext\Controller;
use \ext\Rsa;
use \Website\model\Shop_user;
use \ext\Builduser;
use \ext\Image;

class Index extends Controller
{
    const KEY = "yfpay";
    public $user;
    public function __construct()
    {
        $this->user = new Shop_user();
    }
    
    public function login(){
        if(!empty($_POST)){
           $tel = htmlspecialchars(addslashes($_POST['tel']));
           $password = $this->hashkey(addslashes($_POST['password']),self::KEY);
           $sql = "SELECT * FROM w_merc WHERE merc_name= :user AND merc_pass= :pass ";
           $stmt = $this->user->pdo->prepare($sql);
           $stmt->execute([':user'=>$tel,':pass'=>$password]);
           $data = $stmt->fetch(2);
           if(!empty($data)){
               if($data['status'] == 0){
                   $_SESSION['tel'] = $tel;
                   $this->success("登录成功","/Admin/Index/empower");
               }else{
                   $_SESSION['tel'] = $tel;
                   $this->success("登录成功","/Admin/Index/index");
               }
           }else{
               $this->error("登录失败");
           }
        }else{
            viewS("Website","Index","login");
        }
    }

    public function loginout(){
        setcookie("tel","",time()-1);
        $this->success("退出成功",BASE_URL."/Website/Index/login");
    }

    public function register(){
         if(!empty($_POST)){
             //攻击  XSS  SQL  CSRF（POST攻击）  DDOS(哈希碰撞)
             $tel = addslashes($_POST['tel']);
             $merc_cid = addslashes($_POST['merc_cid']);
             $password = $this->hashkey($_POST['password'],self::KEY);
             $builduser = new Builduser();
             $result = $builduser->user_add();
             $rsa = new Rsa();
             $keys = $rsa->new_rsa_key();
             $dir = APP_PATH."/public/userrsa/".$tel;
             if(!file_exists($dir)){
                 mkdir($dir,0777,true);
             }
             $pri_url = $dir."/rsa_private_key.pem";
             $pub_url = $dir."/rsa_public_key.pem";
             file_put_contents($pri_url,$keys['privkey']);
             file_put_contents($pub_url,$keys['pubkey']);
             $data['merc_id'] = NULL;
             $data['app_key'] = $result['app_key'];
             $data['secret_key'] = $result['secret_key'];
             $data['merc_name'] = $tel;
             $data['merc_pass'] = $password;
             $data['merc_cid'] = $merc_cid;
             $data['api_address'] = "";
             $data['rsapub'] = "/public/userrsa/".$tel."/rsa_public_key.pem";
             $data['rsapri'] = "/public/userrsa/".$tel."/rsa_private_key.pem";
             $data['status'] = 0;
             $date = $this->user->action($this->user->insertSql("merc",$data));
             if($date){
                 $this->success("注册成功","/Website/Index/login");
             }else{
                 $this->error("注册失败");
             }
         }else{
             viewS("Website","Index","register");
         }
    }

    public function hashkey($data,$key){
        return hash_hmac("sha256",$data,$key);
    }

    public function ajaxcode(){
        $data = strtolower($_POST['d']);
        $code = $_SESSION['yzm'];
        if($data == $code){
            echo json_encode(["code"=>1]);
        }else{
            echo json_encode(["code"=>0]);
        }
    }

    public function code(){
        $img = new Image();
        header("content-type:image/png");
        $img::code(140,39,20,20,25,30,APP_PATH."/public/Website/font/msyhbd.ttc");
    }
}