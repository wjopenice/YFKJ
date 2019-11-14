<?php
namespace Admin\controller;
use Admin\model\User_model;
use \ext\Controller;
use \ext\Image;
use \ext\Page;
use Website\model\Shop_user;

class Index extends Controller
{
    protected $iv = "ZZWBKJ_ZHIHUAWEI";

    private $_isbase64 = true;

    private $_privkey = '-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDHY0BbRvMwhZjC
u9osq9+b/W29/Tbg1LOsoLpP1pFIjUYuCSGnhwBI1ujLgQho/ZLghqOTy9pK7UZy
c6HqL471/QmC1icaxqwttYEMJib99Hq1B90YWm1OxXmyXD6+Ctx2TeJu6dy3jlGh
4m/MsWGn59fV0JbhpCsK0aTDjU93UbWbtY03MT74jXzr2yU+kd0oWj89jQx9mYYS
wE+aybKUK+ADkgAEdFqldLmWovjq3ec2XiuruS6IQ6EcEdpiP+I6cxtv0S/DxY7h
MYrdwd7Hoeaq0VzNTfjVITSPCJPEJmdYggJmWqxrWxFHvo31UjULBkYAYzKFVSeX
whUDh4OlAgMBAAECggEAOe4DR8HQbFuUa5eqpun02NyD4vxL0nzaCyDToNilc1/g
cQWkKolVstQF5QKDUAXEg8l6gzlqHfTcT+p6s5DHx36SIdpYmDX61njymnYHRCeg
8zHqseWr9oP3fLPCYvLC5Phx/mmiAA6wdwlUFDPBPMhmdC/bdol9G/KXHV3UJcUr
Pa6NoCtDc3LM6/gqGi3cvS7EVkogw36CHEYT7UGM5LmluxfpEyUKvSTXXmJPG0ek
L56meS6hxvV6yZ20NY6uW7qi8VaBIszSw4JCzgRI5ol7lEmCgK7OOLsLs5KQV+fd
B12gYp5ltAlgZu93TFGn/J5lm3ZmU9aieHvX+e377QKBgQDtb3f6sTvtOSlT+w+7
7FOYauapeoP39pGDBhRjlc3fpnc9hI0HRyAIoCccVNqMm3Vaz1GuujAziOkNuAm/
jdbbgyXKJIaIjCHz0Cn5eTqaFu3ni8ws0FNoTY/UwCRSRhwxVCKSOVXxzU+Fu2H7
0X8kV3xF5GQnZLbXgXHzH/3edwKBgQDW+jc1efeVSLmmTUQQHHp9AWFw76bu8PVN
X+Q9LjLZ2U2CC1HD7ZuNRNW8FSMx5gu/PyyQ2Ad0fTFG8Wwz5oj/sUCCWqOw3dRI
A6pMS/oq59/493OOvmBQrHFpE6FzTG0f1I4DjZS+dOqxogJhgABzSZOR6zEnkrlO
Ou6cqyopwwKBgBSGGyfnZBe4ZjMlM5H59qvpKbEirv+jJ6bcwqxtCZO4jvwZVht3
8XPtbBQN3apj0bwcyaHB5GLABe0yqF0PhLAK4RxdFef9vP2XU9mFuiQRsVTfh7Gj
/GG6uqEYTstx1gQJksdy/1PcfHBB51FeJZBdo5djC6lAEnIEdXYfoNE3AoGBAM3I
arzH6dV+7idg4vxBJocuoSXIYhVrloTge8yMwqSCPI11ZGmWs0YBVE9cNHp5aVlC
kdh80nYRuO/d/eOYlB+efs/JD9QoJCJjT8sFF42mtGDQDk/fsLbIuy8IWY3S5MqJ
Xv/LsUy3AknTeU/3hXbWQWp2pnDpJHTcOLjDp/KdAoGAaq4fAvMqrevrLRXEB60K
cCax4cVccxy5Bp4gGeAuwufahM0w2E/CnXeSBIGQcmDaMMQo8CbX01XzCW2/ZBkJ
55k7zC3E4UogwdK8eY85PO97Nbdf8jEewsev9DZkifCGmTlQAi7f0FJX0AiRl0e+
2S3/tcaZRSrp7eq6bge09Mo=
-----END PRIVATE KEY-----';
    
    public function index(){
        viewS("Admin","Index","index");
    }
    public function loginout(){
        unset($_SESSION['tel']);
        $this->success("退出成功","/Website/Index/login");
    }

    public function cold_wallet(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getAgentUser";
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
                for($i=0;$i<count($data);$i++){
                    if($data[$i]['username'] == '商户') {
                    }else{
                        $arr[$i] = $data[$i];
                    }
                }
                $newArr = array_merge($arr);
                viewS("Admin","Index","test",$newArr);
            }else{
                echo "暂无数据"; exit;
            }
        }else{
            echo "暂无数据"; exit;
        }
    }

    public function test(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getAgentUser";
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
                for($i=0;$i<count($data);$i++){
                    if($data[$i]['username'] == '商户') {
                    }else{
                        $arr[$i] = $data[$i];
                    }
                }
                $data = array_merge($arr);
                viewS("Admin","Index","test",$data);
            }else{
                echo "暂无数据"; exit;
            }
        }else{
            echo "暂无数据"; exit;
        }
    }

    //钱包开通币种
    public function currency_open(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getCoinList";
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
                viewS("Admin","Index","currency_open",$data);
            }else{
                echo "暂无数据"; exit;
            }
        }else{
            echo "暂无数据"; exit;
        }
    }

//用户地址生成
    public function address(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/getUserAddress";
            $user['coin'] = "eth";
            $user['username'] = rand(00000000000,99999999999);
            $user['agent_key'] = $result['app_key'];
            $newUser = $this->argSort($user);
            $locaUser = $this->createLinkstringUrlencode($newUser);
            $key = "&secret_key=".$result['secret_key'];
            $user['sign']=md5($locaUser.$key);
            $newData = $this->curl_post($url,json_encode(['data'=>$user],320));

            var_dump($newData)  ;
            exit;
        }else{
            echo "暂无数据"; exit;
        }
        //0x0255c62368e65e4213811d61bdd5b5898978b3d2
    }

//提现
    public function tx(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $result = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $user = [];
        if(!empty($result['merc_id'])){
            $url = TOKEN_URL."/api/Tokenapi/coinWithdraw";
            $reqData =  [
                'coin' => 'dyx',
                'num' => 0.001,
                'from_addr' => '0x0255c62368e65e4213811d61bdd5b5898978b3d2',
               // 'agent_key' =>$result['app_key'],
                'to_addr' => '0x37f30e211e1b8e02fd8c70f05476d36ee443a358'
            ];
            $encrypt_data = json_encode($reqData,320);
            $encrypt_key = '2019102910eDu189';
            $encrypted = $this->encryptWithOpenssl($encrypt_data,$encrypt_key,$this->iv);
            $encode = $this->priv_encode($encrypt_key);
            $user['agent_key'] = $result['app_key'];
            $user['encode']= $encode;
            $user['encrypted']= $encrypted;
            $jsonstr =  json_encode(['data'=>$user],320);
            echo $jsonstr;
            $newData = $this->curl_post($url,$jsonstr);
            $responseData = json_decode($newData,true);
            var_dump($responseData);
            exit;
        }else{
            echo "暂无数据"; exit;
        }
    }


    public function hot_wallet(){
        viewS("Admin","Index","hot_wallet");
    }
    public function member_password(){
        viewS("Admin","Index","member_password");
    }
    public function order_add(){
        $db = new User_model();
        if(!empty($_POST)){
            $merc_name = $_POST['merc_name'];
            $merc_cid = $_POST['merc_cid'];
            $merc_id = $_POST['merc_id'];
            $data = $db->field("merc_id,api_address")
                ->table("w_merc")
                ->where("merc_id = {$merc_id} AND merc_name = {$merc_name} AND merc_cid = '{$merc_cid}'")
                ->find();
            if(!empty($data['merc_id'])){
                $data = $db->field("secret_key")->table("w_merc")->where("merc_id = {$merc_id}")->find();

                $_SESSION['secret_key'] = ['val' => $data['secret_key'], 'time' => time() + 60];

                echo 1;

            }else{
                echo 0;
            }
        }else{
            $merc_id = $_GET['id'];
            $data = $db->field("merc_id,api_address")->table("w_merc")->where("merc_id = {$merc_id}")->find();
            viewS("Admin","Index","order_add",$data);
        }
    }
    public function order_edit(){
        $db = new User_model();
        if(!empty($_POST)){
            $api_address = $_POST['api_address'];
            $merc_id = $_POST['merc_id'];
            $bool = $db->action($db->updateSql("merc",["api_address"=>$api_address],"merc_id = {$merc_id}"));
            if($bool){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            $merc_id = $_GET['id'];
            $data = $db->field("merc_id,api_address")->table("w_merc")->where("merc_id = {$merc_id}")->find();
            viewS("Admin","Index","order_edit",$data);
        }
    }
    public function order_list(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $data = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        $secret_key =  isset($_SESSION['secret_key']) ? $_SESSION['secret_key'] : 0;
        $secret = 0;
        if ($secret_key['time'] < time())
        {
            $secret = $secret_key['val'];
        }
        $data['secret'] = $secret;
        viewS("Admin","Index","order_list",$data);
    }
    public function recharge_order(){
        viewS("Admin","Index","recharge_order");
    }
    public function welcome(){
        $user = $_SESSION['tel'];
        $db = new User_model();
        $data = $db->field("*")->table("w_merc")->where("merc_name = {$user}")->find();
        viewS("Admin","Index","welcome",$data);
    }
    public function withdrawal_order(){
        viewS("Admin","Index","withdrawal_order");
    }

    public function empower(){
        viewS("Admin","Index","empower");
    }

    public function test2(){
        //$url = TOKEN_URL."/Api/Api/remote_withraw";
        $key = "ba881a98-5ea8-4ee1-daee-a678ad79c795";
        $data['appKey'] = '1cff7c37-30c5-09ef-18c8-74d1df5de82c';
        $data['coinType'] = 'dyx';
        $data['price'] = 1;
        $data['from'] = '0x61010e9524f84879ea210EafB97Fe0a0AcfC6B0F ';
        $data['to'] = '0x9ef3f6506e38c89b691d2c3576361bf1bf204c6f ';
        $data['order_on'] = '1573452499 ';
        $newUser = $this->argSort($data);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $data['sign'] = hash_hmac("sha256",$locaUser.$key,$key);
        print_r($data);
        exit;
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
            $arg .= $k . "=" . urlencode($v) . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    public function decryptWithOpenssl($data,$key,$iv){
        return openssl_decrypt(base64_decode($data),"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv);
    }
    public function encryptWithOpenssl($data,$key,$iv){
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$key,OPENSSL_RAW_DATA,$iv));
    }

    public  function priv_encode($data){
        $outval = '';
        $res = openssl_pkey_get_private($this->_privkey);
        openssl_private_encrypt($data, $outval, $res);
        if($this->_isbase64){
            $outval = base64_encode($outval);
        }
        return $outval;
    }
}