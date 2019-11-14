<?php
namespace Admin\controller;
use Admin\model\User_model;
use \ext\Controller;
use \ext\Image;
use \ext\Page;
use Website\model\Shop_user;

class Ajax extends Controller
{
    public function ajax_return($arrdata)
    {
         $data = [
             "code"=>0,
             'count'=>100,
             "data"=>$arrdata
         ];
         echo json_encode($data,320);
         unset($arrdata);
         exit;
    }
    public function currency_open(){
        
    }
    public function hot_wallet(){
        $user = $_GET['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getAgentBalance";
            $user['agent_key'] = $result['app_key'];
            $user['coin_type'] = "eth_eth_0x7ee430a530d598d40868311bd6e62433a5963975";
            $newUser = $this->argSort($user);
            $locaUser = $this->createLinkstringUrlencode($newUser);
            $key = "&secret_key=".$result['secret_key'];
            $user['sign']=md5($locaUser.$key);
            $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
            $responseData = json_decode($newData,true);
            var_dump($newData) ;
            exit;
        }else{
            echo "暂无数据"; exit;
        }
        $res = [
            ["coinType"=>"","address"=>"","qty"=>""],
            ["coinType"=>"","address"=>"","qty"=>""]
        ];
        $this->ajax_return($res);
    }
    public function order_list(){
        $res = [
            ["uniquekey"=>"","serialNumber"=>"","address"=>"","coinType"=>"","qty"=>"","createtime"=>""],
            ["uniquekey"=>"","serialNumber"=>"","address"=>"","coinType"=>"","qty"=>"","createtime"=>""]
        ];
        $this->ajax_return($res);
    }
    public function recharge_order(){
        $user =  $_GET['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getCoinInchange";
            $user['page'] = 0;
            $user['limit'] = 10;
            $user['agent_key'] = $result['app_key'];
            $newUser = $this->argSort($user);
            $locaUser = $this->createLinkstringUrlencode($newUser);
            $key = "&secret_key=".$result['secret_key'];
            $user['sign']=md5($locaUser.$key);
            $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
            $responseData = json_decode($newData,true);
            if($responseData['code'] === 0){
                $data = $responseData['data']['list'];
                $arr = [];
                foreach ($data as $k=>$v){
                    $arr[$k]['uniquekey'] = $v['addtime'];
                    $arr[$k]['address'] = $v['to_address'];
                    $arr[$k]['coinType'] = $v['coinname'];
                    $arr[$k]['qty'] = $v['num'];
                    $arr[$k]['createtime'] = date("Y-m-d H:i:s",$v['addtime']);
                }
                $this->ajax_return($arr);
            }else{
                echo "暂无数据"; exit;
            }
        }else{
            echo "暂无数据"; exit;
        }
    }
    public function withdrawal_order(){
        $user = $_GET['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getcoinWithdrawList";
            $user['page'] = 0;
            $user['limit'] = 100;
            $user['agent_key'] = $result['app_key'];
            $newUser = $this->argSort($user);
            $locaUser = $this->createLinkstringUrlencode($newUser);
            $key = "&secret_key=".$result['secret_key'];
            $user['sign']=md5($locaUser.$key);
            $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
            $responseData = json_decode($newData,true);
            if($responseData['code'] === 0){
                $data = $responseData['data']['list'];
                $arr = [];
                foreach ($data as $k=>$v){
                    $arr[$k]['uniquekey'] = $v['addtime'];
                    $arr[$k]['serialNumber'] = $v['addtime'];
                    $arr[$k]['address'] = $v['to_address'];
                    $arr[$k]['coinType'] = $v['coinname'];
                    $arr[$k]['qty'] = $v['num'];
                    $arr[$k]['createtime'] = date("Y-m-d H:i:s",$v['addtime']);
                }
                $this->ajax_return($arr);
            }else{
                echo "暂无数据"; exit;
            }
        }else{
            echo "暂无数据"; exit;
        }
    }
    public function token_register(){
        $tel = $_POST['tel'];
        $pass = $_POST['pass'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$tel}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/registerAgent";
            $user['app_key'] = $result['app_key'];
            $user['name'] = $result['merc_name'];
            $user['coin_passwd'] = $pass;
            $user['secret_key'] = $result['secret_key'];
            $newUser = $this->argSort($user);
            $locaUser = $this->createLinkstringUrlencode($newUser);
            $key = "&secret_key=".$result['secret_key'];
            $user['sign']=md5($locaUser.$key);
            $sendData = json_encode(['data'=>$user]);
            $newData = $this->curl_post($url,$sendData);
            $resdata = json_decode($newData,true);
            if($resdata['code'] == 0){
                $db->action($db->updateSql("merc",["status"=>1],"merc_name = {$tel}"));
                echo json_encode(["code"=>1,"mnemonic"=>$resdata['data']['mnemonic']]); exit;
            }else{
                echo json_encode(["code"=>0]); exit;
            }
        }else{
            echo "暂无数据";
        }
    }
    public function piebase(){
        viewS("Admin","Highcharts6","examples/pie-basic/index");
    }
    public function menu_del(){
        $id = $_GET['id'];
        $page = $_GET['page'];
        $db = new User_model();
        $sql = $db->deleteSql("product_info","id={$id}");
        $bool = $db->action($sql);
        if($bool){
            $this->success("删除成功",BASE_URL."/Admin/Index/prd?page={$page}");
        }else{
            $this->error("删除失败");
        }
//        1、两次sql
//        2、触发器
//        3、外键
//        4、存储过程
    }
    public function pie(){
        $db = new Shop_user();
        $sql = $db->select("name,price AS y")->from("shop_product_info")->getSql();
        $data = $db->action($sql);

        foreach ($data as $k=>$v){
            $data[$k]['y'] = (int)$v['y'];
        }
        echo json_encode($data);
    }
    public function code(){
       $img = new Image();
       header("content-type:image/png");
       $img::code(230,50);
    }
    public function curl_post($url, $data_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string)
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return $result;
    }
    public function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }
    public function createLinkstringUrlencode($para)
    {
        $arg = "";
        foreach ($para as $k=>$v){
            $arg .= $k . "=" . $v . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
    public function hamc256($data,$key){
        return hash_hmac("sha256",$data,$key);
    }
}