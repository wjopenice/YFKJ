<?php
namespace Api\controller;
use \Admin\model\User_model;
use \ext\Controller;
use \ext\CodeConfig;
use \Website\model\Shop_user;
class Api extends Controller{
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
    
    public function __construct()
    {
        if(empty($_POST)){
            $this->ajax_return(303);
        }
    }

    public function ajax_return($code,$arrdata = "")
    {
        $codemsg = CodeConfig::getCodeConfig();
        if(!empty($arrdata)){
            $data = [
                "code"=>$code,
                'message'=>$codemsg[$code],
                "data"=>$arrdata
            ];
        }else{
            $data = [
                "code"=>$code,
                'message'=>$codemsg[$code],
            ];
        }
        echo json_encode($data,320);
        exit;
    }

    public function user_select(){
        if(!isset($_POST['app_key'])){
            $this->ajax_return(312);
        }else{
            $app_key = htmlspecialchars(addslashes($_POST['app_key']));
        }
        $db = new Shop_user();
        $result = $db->field("app_key,secret_key,rsapub,rsapri")
            ->table("w_merc")
            ->where("app_key = '{$app_key}'")
            ->find();
        if(!empty($result['app_key'])){
            $this->ajax_return(0,$result);
        }else{
            $this->ajax_return(302);
        }
    }
    
    public function user_addr(){
        if(!isset($_POST['appKey'])){
            $this->ajax_return(312);
        }
        if(!isset($_POST['coinType'])){
            $this->ajax_return(304);
        }
        if(!isset($_POST['sign'])){
            $this->ajax_return(502);
        }
        $appKey = htmlspecialchars(addslashes($_POST['appKey']));
        $coinType = htmlspecialchars(addslashes($_POST['coinType']));
        $sign = htmlspecialchars(addslashes($_POST['sign']));
        $db = new Shop_user();
        $result = $db->field("*")
            ->table("w_merc")
            ->where("app_key = '{$appKey}'")
            ->find();
        if(!empty($result['app_key'])){
            $data['appKey'] = $appKey;
            $data['coinType'] = $coinType;
            $this->is_sign($sign,$data,$result['secret_key']);
            $result = $this->token_address($result);
            $response = json_decode($result,true);
            if($response['code'] == 0){
                $this->ajax_return(0,["address"=>$response['data']['address']]);
            }else{
                $this->ajax_return(505);
            }
        }else{
            $this->ajax_return(302);
        }
    }

    public function token_address($result){
        $url = TOKEN_URL."/api/Tokenapi/getUserAddress";
        $user['coin'] = "eth";
        $user['username'] = rand(00000000000,99999999999);
        $user['agent_key'] = $result['app_key'];
        $newUser = $this->argSort($user);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $key = "&secret_key=".$result['secret_key'];
        $user['sign']=md5($locaUser.$key);
        $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
        return $newData;
    }

    public function user_init(){
        if(!isset($_POST['appKey'])){
            $this->ajax_return(312);
        }
        if(!isset($_POST['sign'])){
            $this->ajax_return(502);
        }
        if(!isset($_POST['password'])){
            $this->ajax_return(320);
        }
        $appKey = htmlspecialchars(addslashes($_POST['appKey']));
        $sign = htmlspecialchars(addslashes($_POST['sign']));
        $pass = htmlspecialchars(addslashes($_POST['password']));
        $db = new Shop_user();
        $result = $db->field("*")
            ->table("w_merc")
            ->where("app_key = '{$appKey}'")
            ->find();
        if(!empty($result['app_key'])){
            $data['appKey'] = $appKey;
            $data['password'] = $pass;
            $this->is_sign($sign,$data,$result['secret_key']);
            $result = $this->token_init($result,$pass);
            $response = json_decode($result,true);
            if($response['code'] == 0){
                $this->ajax_return(0,["mnemonic"=>$response['data']['mnemonic']]);
            }else{
                $this->ajax_return(505);
            }
        }else{
            $this->ajax_return(302);
        }
    }

    public function token_init($result,$pass){
        $url = TOKEN_URL."/api/Tokenapi/registerAgent";
        $user['app_key'] = $result['app_key'];
        $user['name'] = $result['merc_name'];
        $user['coin_passwd'] = $pass;
        $user['secret_key'] = $result['secret_key'];
        $newUser = $this->argSort($user);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $key = "&secret_key=".$result['secret_key'];
        $user['sign']=md5($locaUser.$key);
        $sendData = json_encode(['data'=>$user],320);
        $newData = $this->curl_post($url,$sendData);
        return $newData;
    }

    public function user_recharge(){
        if(!isset($_POST['appKey'])){
            $this->ajax_return(312);
        }
        if(!isset($_POST['sign'])){
            $this->ajax_return(502);
        }
        $appKey = htmlspecialchars(addslashes($_POST['appKey']));
        $sign = htmlspecialchars(addslashes($_POST['sign']));
        $page = !isset($_POST['page']) ? 0:addslashes($_POST['page']);
        $limit = !isset($_POST['limit']) ? 10:addslashes($_POST['limit']);
        $db = new Shop_user();
        $result = $db->field("*")
            ->table("w_merc")
            ->where("app_key = '{$appKey}'")
            ->find();
        if(!empty($result['app_key'])){
            $data['appKey'] = $appKey;
            $data['page'] = $page;
            $data['limit'] = $limit;
            $this->is_sign($sign,$data,$result['secret_key']);
            $result = $this->token_recharge($result,$page,$limit);
            $response = json_decode($result,true);
            if($response['code'] == 0){
                $this->ajax_return(0,$response['data']['list']);
            }else{
                $this->ajax_return(505);
            }
        }else{
            $this->ajax_return(302);
        }
        
    }

    public function token_recharge($result,$page,$limit){
        $url = TOKEN_URL."/api/Tokenapi/getCoinInchange";
        $user['page'] = $page;
        $user['limit'] = $limit;
        $user['agent_key'] = $result['app_key'];
        $newUser = $this->argSort($user);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $key = "&secret_key=".$result['secret_key'];
        $user['sign']=md5($locaUser.$key);
        $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
        return $newData;
    }

    public function user_withdraw(){
        if(!isset($_POST['appKey'])){
            $this->ajax_return(312);
        }
        if(!isset($_POST['sign'])){
            $this->ajax_return(502);
        }
        $appKey = htmlspecialchars(addslashes($_POST['appKey']));
        $sign = htmlspecialchars(addslashes($_POST['sign']));
        $page = !isset($_POST['page']) ? 0:addslashes($_POST['page']);
        $limit = !isset($_POST['limit']) ? 10:addslashes($_POST['limit']);
        $db = new Shop_user();
        $result = $db->field("*")
            ->table("w_merc")
            ->where("app_key = '{$appKey}'")
            ->find();
        if(!empty($result['app_key'])){
            $data['appKey'] = $appKey;
            $data['page'] = $page;
            $data['limit'] = $limit;
            $this->is_sign($sign,$data,$result['secret_key']);
            $result = $this->token_recharge($result,$page,$limit);
            $response = json_decode($result,true);
            if($response['code'] == 0){
                $this->ajax_return(0,$response['data']['list']);
            }else{
                $this->ajax_return(505);
            }
        }else{
            $this->ajax_return(302);
        }
    }

    public function token_withdraw($result,$page,$limit){
        $url = TOKEN_URL."/api/Tokenapi/getcoinWithdrawList";
        $user['page'] = $page;
        $user['limit'] = $limit;
        $user['agent_key'] = $result['app_key'];
        $newUser = $this->argSort($user);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $key = "&secret_key=".$result['secret_key'];
        $user['sign']=md5($locaUser.$key);
        $newData = $this->curl_post($url,json_encode(['data'=>$user],320));
        return $newData;
    }

    public function remote_withraw(){
        if(!isset($_POST['appKey'])){
            $this->ajax_return(312);
        }
        if(!isset($_POST['sign'])){
            $this->ajax_return(502);
        }
        if(!isset($_POST['coinType'])){
            $this->ajax_return(304);
        }
        if(!isset($_POST['price'])){
            $this->ajax_return(321);
        }
        if(!isset($_POST['from'])){
            $this->ajax_return(322);
        }
        if(!isset($_POST['to'])){
            $this->ajax_return(323);
        }
        if(!isset($_POST['order_on'])){
            $this->ajax_return(324);
        }

        $db = new User_model();
        $dbdata = $db->field("order_on")->table("w_withdraw")->where("order_on = '{$_POST['order_on']}'")->find();
        if(!empty($dbdata['order_on'])){
            if($dbdata['order_on'] == $_POST['order_on']){
                $this->ajax_return(325);
            }
        }

        $appKey = htmlspecialchars(addslashes($_POST['appKey']));
        $sign = htmlspecialchars(addslashes($_POST['sign']));
        $coinType = htmlspecialchars(addslashes($_POST['coinType']));
        $price = htmlspecialchars(addslashes($_POST['price']));
        $from= htmlspecialchars(addslashes($_POST['from']));
        $to = htmlspecialchars(addslashes($_POST['to']));
        $order_on = htmlspecialchars(addslashes($_POST['order_on']));
        $db = new Shop_user();
        $result = $db->field("*")
            ->table("w_merc")
            ->where("app_key = '{$appKey}'")
            ->find();
        if(!empty($result['app_key'])){
            $data['appKey'] = $appKey;
            $data['coinType'] = $coinType;
            $data['price'] = $price;
            $data['from'] = $from;
            $data['to'] = $to;
            $data['order_on'] = $order_on;
            $this->is_sign($sign,$data,$result['secret_key']);
            $result = $this->server_withraw($result,$coinType,$price,$from,$to,$order_on);
            $response = json_decode($result,true);
            if($response['code'] == 0){
                $this->ajax_return(0,["result"=>$response['data']['result']]);
            }else{
                $this->ajax_return(505);
            }
        }else{
            $this->ajax_return(302);
        }
    }

    public function server_withraw($result,$coinType,$price,$from,$to,$order_on){
        $url = TOKEN_URL."/api/Tokenapi/coinWithdraw";
        $reqData =  [
            'coin' => strtolower($coinType),
            'num' => $price,
            'from_addr' => $from,
            'to_addr' => $to
        ];
        $encrypt_data = json_encode($reqData,320);
        $encrypt_key = uniqid().rand(000,999);
        $db = new User_model();
        $res['id'] = NULL;
        $res['order_on'] = $order_on;
        $res['coinType'] = $coinType;
        $res['num'] = $price;
        $res['from_addr'] = $from;
        $res['to_addr'] = $to;
        $res['create_time'] = time();
        $res['send_time'] = 0;
        $res['uniquekey'] = $encrypt_key;
        $res['status'] = 0;
        $res['txid'] = "";
        $db->action($db->insertSql("withdraw",$res));
        $encrypted = $this->encryptWithOpenssl($encrypt_data,$encrypt_key,$this->iv);
        $encode = $this->priv_encode($encrypt_key);
        $user['agent_key'] = $result['app_key'];
        $user['encode']= $encode;
        $user['encrypted']= $encrypted;
        $jsonstr =  json_encode(['data'=>$user],320);
//        echo "encrypt_data==>".$encrypt_data."<hr>";
//        echo "encrypt_key==>".$encrypt_key."<hr>";
//        echo "encrypted==>".$encrypted."<hr>";
//        echo "encode==>".$encode."<hr>";
//        echo "jsonstr==>".$jsonstr;
        $newData = $this->curl_post($url,$jsonstr);
//        var_dump($newData);
//        exit;
        return $newData;
    }
    
    public function is_sign($data,$lock,$key){
        $newUser = $this->argSort($lock);
        $locaUser = $this->createLinkstringUrlencode($newUser);
        $signData = $this->hamc256($locaUser.$key,$key);
        if($data == $signData){
            return true;
        }else{
            $this->ajax_return(503);
        }
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
    public function hamc256($data,$key){
        return hash_hmac("sha256",$data,$key);
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