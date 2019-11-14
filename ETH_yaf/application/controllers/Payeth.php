<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class PayethController extends Controller_Abstract{
    public $url = "http://api.morningpay.io/api/";
    public $appkey = "30062115-eb1c-4dcc-8e01-e19bd8c29101";
    public $appsecrect = "61f23786-3aca-4a60-a84e-9cf8867700c4";
    public $DyxConiType = "ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106";
    public $UsdtConiType = "ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7";
    private $private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAKl3tVHa7Vm830mZgAGXWyRJOF+qkqWE5uQoaDuHz5tBcjnflxV3KNx5sD86cMC+9CpnKNWfUxGgNubG83oIgteOpcBy06Xn6Aw+vz8noSCXMgZpoCoBgjyOl5dr9rQpzMJshMzexFwZ8Ddu07ZiYm2cGhG6ehpbZsHHHOe/2ngdAgMBAAECgYBxrPvyuXEHBfudihrkdlvLvlXTTj7qSnj6yOQKMrKfCUqO6Z2V54WARxxjfVcO48gy/VnV2fbF1vuc2A2QaU8iI2elPpZwxKkhLE6lDQgJVTYoiVZ9d2jVwcifc0HHe+b3VysTgypDJuTz0juzYfSxpaxGnfF+Lp7M8cOf8JKqPQJBANV5I+NxgcNsD4m1GMOAPGJ2jt6pM9R+wXCsMDyfbjGnD+bBzHk09Nw5Iuz6caxwoFSpK2Vq7uZQBOhgJn8O7BcCQQDLOlbXd29hKIilOXlko/MQ0pxDDtItL8E867pR1kmrQ81430sP7eEd7kUDvD9BmXnIJiTPRG3oKfyIjGxBIpnrAkEA0X/Sjbrui5f4Y7/7rpmiKUuLCM/rUsaXFvmVWVlproby3xcgkW88Qwg703AxsPbTEmL3eM+J5zNurZL3FMjTPwJAYeTzSg1FQKb206gQ2rLC9jqNfRvZkFytl7vxX1R63h3mDzB4hu7Ofs10vyzhx6a3a/s7xf+vdaMr+1axGtshCwJAfvCaPgJIqPa8kQ97sncDzkVregv+5eIgDHvSBcc4Fy3QnGrmmfYlT97TwhJxxN6LBFkB0dGIieO35Lo0SI0fmQ==
-----END RSA PRIVATE KEY-----
EOD;
    private $public_key = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuTOrfbRt/vTmBJAAemTz
J3TluvNw9bxOn2uPO/KjDKjegdIVtRWDxO/U6OUqdT2nFW9RMmKjsjqVZ9XsaZcx
XigE0Bnf23n+xBsU5TKT8EXFSazVsja10CUvpzfem6jAfLzyW2SsNKtWvBlRJpoH
fOjspYDDQV4+6z3NrR4PRUUonWwFC0FA4WvGfKeDdnHnGLV/UhghmnyO41bnEd5P
eZAuUP3+02ELRJOZANap1gbQaddn+124qPjVosJih2CYG+Nftw6UEGHatD5VXR6u
Z4tMTo9Xk4Diqr8POTQdyI+ySgLoQIyQwPs+/i4a6sxj1aqhViUuI8tWCemOM1Yh
uwIDAQAB
-----END PUBLIC KEY-----
EOD;

    public $db;
    public function init(){
        $this->db = new dbModel();
    }
    //RSA2签名
    public function resSign($data)
    {
        $newData = md5($data);
        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        //$res = openssl_get_privatekey($this->private_key);
        $res = openssl_pkey_get_private($this->private_key);
        //调用openssl内置签名方法，生成签名$sign
        //openssl_sign($newData, $sign, $res,"SHA256" );
        openssl_private_encrypt($newData,$sign,$res);
        //释放资源
        //openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }
    //用户生成地址
    public function address()
    {
        $api = $this->url."api/getAddress";
        $data['appKey'] = $this->appkey;
        $data['coinType'] = "ETH";
        $result = $this->createLinkstringUrlencode($data);
        //$sendData['coinType'] = $data['coinType'];
        $data['sign'] = $this->sha256($result, $this->appsecrect);
        //$sendData['appKey'] = $data['appKey'];
        $result2 = json_encode($data,320);
        $newData = $this->curl_post($api, $result2);
        return $newData;
    }
    //查询收币记录
    public function selectlog(){
        $api = $this->url."api/queryColdRecord";
        $data['appKey'] = $this->appkey;
        $data['coinType'] = "ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7";
        $data['page'] = 1;
        $result = $this->createLinkstringUrlencode($data);
        $data['sign'] = $this->sha256($result, $this->appsecrect);
        $result2 = json_encode($data,320);
        $newData = $this->curl_post($api, $result2);
        return $newData;
    }
    //查询提现记录
    public function withdrawlog(){
        $api = $this->url."api/queryWithdrawRecord";
        $data['appKey'] = $this->appkey;
        $data['coinType'] = "ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106";
        $data['page'] = 1;
        $result = $this->createLinkstringUrlencode($data);
        $data['sign'] = $this->sha256($result, $this->appsecrect);
        $result2 = json_encode($data,320);
        $newData = $this->curl_post($api, $result2);
        return $newData;
    }
    //提现
    public function withdraw(){
        $api = $this->url."api/withdraw";
        $data['appKey'] = $this->appkey;
        $data['coinType'] = "ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7";
        $data['to'] = "0x4f6e682f921749b2d23663e59835886761372952";  //提币地址
        $data['quantity'] = "0.01000000"; //提币数量
        $data['serialNumber'] = uniqid(); //订单号
        $data['memo'] = "test"; //备注
        $data['feeCost'] = 0; //矿工费
        $data['gasLimit'] = "25200.00000000"; //最多使用gas数量。ETH及ERC 20代币用到
        $data['gasPrice'] = "0.00000004"; //GAS价格
        $data['contractAddress'] = "";
        $data['symbol'] = "";
        $newData = $this->argSort($data);
        $strData = $this->createLinkstringUrlencode($newData);
        file_put_contents(APP_PATH."/log/a.txt",$strData."\r\t",FILE_APPEND);
        $rsaSign = $this->resSign($strData);
        $signstr = $this->sha256($strData, $this->appsecrect);
        $data['rsaSign'] = $rsaSign;
        $data['sign'] = $signstr;
        $result2 = json_encode($data,320);
        $newData = $this->curl_post($api, $result2);
        return $newData;
    }
    public function sha256($data, $key)
    {
        return hash_hmac("sha256", $data, $key);
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
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    protected function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstringUrlencode($para)
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
    public function testAction(){
        $type = isset($_GET['type'])?$_GET['type']:"resSign";
        switch($type){
            case "address":
                var_dump($this->address());
                break;
            case "selectlog":
                echo "<pre>";
                print_r(json_decode( $this->selectlog(),true));
                break;
            case "withdrawlog":
                var_dump($this->withdrawlog());
                break;
            case "withdraw":
                var_dump($this->withdraw());
                break;
            case "resSign":
                var_dump($this->resSign('456'));
                break;
            default:
                var_dump($this->resSign('456'));
                break;
        }
        exit;
    }

    public function callbackAction(){
        $data1 = file_get_contents("php://input");
        file_put_contents(APP_PATH."/log/log1.txt","log1:".$data1."\r\n",FILE_APPEND);
//充值
//        $data1 = '{"blockHight":"8494598","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT","createtime":"1567748382000","address":"0x1f77c901cd0420cd6073ab641da1f9a3cb93c76e","uniquekey":"0x006b1974d87885af4aed34ed46aa3dc7136cc75b90793802e0e934e3c32640be0xdac17f958d2ee523a2206206994597c13d831ec7ETH_USDT0x1f77c901cd0420cd6073ab641da1f9a3cb93c76e","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"dd182993a64b76d039713daa2db819dd10bea87c2b671ad6907c2f94b86b1e40","txid":"0x006b1974d87885af4aed34ed46aa3dc7136cc75b90793802e0e934e3c32640be","type":"cold","userid":"1567578224910","curBlockHight":"0","blockhash":"0x23da194ba38dde2cc500b67b5ee19df0aae9dd1d5e9da4dd0c18cf1866205f3e","secondNoticeCounter":"0","firstNoticeCounter":"0","qty":"302.78000000","id":"50366","updatetime":"1567748382000","direction":"in","status":"new"}';
//提现
//        $data1 = '{"blockHight":"0","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","createtime":"1568007830000","address":"0x261B56Ef88241BE0f307ba5809c80afc64FdD6E0","serialNumber":"5d75e693d7980","uniquekey":"5d75e693d7980","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"f7d217bd9a4a473615671aa078fb413f83eb3242bdea7988bcb62dc0521ab5f3","txid":"0x3902956a87fda2497df2b2070c50e3e219f33fdd98f73be3222d058a52b8606c","type":"withdraw","userid":"1567578224910","curBlockHight":"0","blockhash":"\"\"","secondNoticeCounter":"0","firstNoticeCounter":"99","qty":"1.00000000","id":"50441","updatetime":"1568007830000","direction":"in","status":"new"}';
        
//        $data1 = '{"blockHight":"0","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","createtime":"1570260380000","address":"0xd20c16f36c07bdfaca90f6235984d8008eaec7c6","serialNumber":"WC2019100560378623127621","uniquekey":"WC2019100560378623127621","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"7c4eb334e1653e5a7b17478203e47e68e31b3b796c3fa60f0bd02d529c0c4d34","txid":"0xccb4ad8bcdf8cea003aa652c870c85a18b4aec605821cb1ad9100a5cca174b95","type":"withdraw","userid":"1567578224910","curBlockHight":"0","blockhash":"\"\"","gasUsed":"0E-10","secondNoticeCounter":"0","firstNoticeCounter":"0","qty":"20.00000000","id":"51048","updatetime":"1570260380000","direction":"in","gasPrice":"0E-10","status":"new"}
//';

        $result = json_decode($data1,true);
        if($result['direction'] == 'in' &&  $result['type'] == "cold"){
            file_put_contents(APP_PATH."/log/log2.txt","log1:冷钱包充值成功".$data1."\r\n",FILE_APPEND);
            $address = $result['address'];
            $userinfo = $this->db->field("*")->table("y_user")->where(" token_address = '{$address}'")->find();
            $rechdata['token_num'] = sprintf("%.3f",$result['qty']);
            $rechdata['create_time'] = substr($result['updatetime'],0,-3);
            $rechdata['address'] = $address;
            $rechdata['status'] = 1;
            if(strpos($result['coinType'], 'DYX') !== false){
                $rechdata['currency'] = "DYX";
                if(!empty($userinfo)){
                    $rechdata['u_id'] = $userinfo['id'];
                    $rechuser['token_num'] = bcadd($userinfo['token_num'], $result['qty'], 3);
                    $rechuser['token_available_balance'] = bcadd($userinfo['token_available_balance'], $result['qty'], 3);
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                    $this->db->action($this->db->updateSql('user',$rechuser," id = {$userinfo['id']}"));
                    $log['user_id'] = $userinfo['id'];
                    $log['type'] = "充值";
                    $log['info'] = "成功{$rechdata['token_num']}个DYX";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }else{
                    //没有用户操作
                    $rechdata['u_id'] = 0;
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                }
            }else if(strpos($result['coinType'], 'USDT') !== false){
                $rechdata['currency'] = "USDT";
                if(!empty($userinfo)){
                    $rechdata['u_id'] = $userinfo['id'];
                    $rechuser['usdt_num'] = bcadd($userinfo['usdt_num'], $result['qty'], 3);
                    $rechuser['usdt_available_balance'] = bcadd($userinfo['usdt_available_balance'], $result['qty'], 3);
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                    $this->db->action($this->db->updateSql('user',$rechuser," id = {$userinfo['id']}"));
                    $log['user_id'] = $userinfo['id'];
                    $log['type'] = "充值";
                    $log['info'] = "成功{$rechdata['token_num']}个USDT";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }else{
                    //没有用户操作
                    $rechdata['u_id'] = 0;
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                }
            }else{
                if(!empty($userinfo)){
                    $rechdata['currency'] = $result['coinType'];
                    $rechuser['id'] = $userinfo['id'];
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                    $this->db->action($this->db->updateSql('user',$rechuser," id = {$userinfo['id']}"));
                    $log['user_id'] = $userinfo['id'];
                    $log['type'] = "充值";
                    $log['info'] = "成功{$rechdata['token_num']}个{$result['coinType']}";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }else{
                    //没有用户操作
                    $rechdata['u_id'] = 0;
                    $this->db->action($this->db->insertSql('recharge',$rechdata));
                }
            }
        }else if($result['direction'] == 'in' &&  $result['type'] == "hot"){
            file_put_contents(APP_PATH."/log/log2.txt","log1:热钱包充值成功".$data1."\r\n",FILE_APPEND);
            $address = $result['address'];
            $rechdata['u_id'] = 0;
            $rechdata['currency'] = "HOT";
            $rechdata['token_num'] = sprintf("%.3f",$result['qty']);
            $rechdata['create_time'] = substr($result['updatetime'],0,-3);
            $rechdata['address'] = $address;
            $rechdata['status'] = 1;
            $this->db->action($this->db->insertSql('recharge',$rechdata));
        }else if($result['type'] == "withdraw"){
            file_put_contents(APP_PATH."/log/log3.txt","log1:提现冷钱包成功".$data1."\r\n",FILE_APPEND);
            $order = $result['serialNumber'];
            $orderinfo = $this->db->field("*")->table("y_withdraw")->where(" order_no = '{$order}'")->find();
            if(!empty($orderinfo)){
                $id = $orderinfo['u_id'];
                $this->db->action($this->db->updateSql('withdraw',['status'=>1],"order_no = '{$order}'"));
                $userinfo = $this->db->field("*")->table("y_user")->where(" id = {$id}")->find();
                $price = $orderinfo['token_num'];
                $type = $result['coinType'];
                if(strpos($type, 'DYX') !== false){
                    $userdata['token_num'] = $userinfo['token_num'] - $price;
                    $userdata['token_freeze_balance'] = $userinfo['token_freeze_balance'] - $price;
                    $this->db->action($this->db->updateSql('user',$userdata,"id = {$id}"));
                    $log['user_id'] = $id;
                    $log['type'] = "提现";
                    $log['info'] = "成功{$price}个DYX";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }else if(strpos($type, 'USDT') !== false){
                    $userdata['usdt_num'] = $userinfo['usdt_num'] - $price;
                    $userdata['usdt_freeze_balance'] = $userinfo['usdt_freeze_balance'] - $price;
                    $this->db->action($this->db->updateSql('user',$userdata,"id = {$id}"));
                    $log['user_id'] = $id;
                    $log['type'] = "提现";
                    $log['info'] = "成功{$price}个USDT";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }else{
                    //第三方币
                    $log['user_id'] = $id;
                    $log['type'] = "提现";
                    $log['info'] = "成功{$price}个{$type}";
                    $log['create_time'] = substr($result['updatetime'],0,-3);
                    $this->db->action($this->db->insertSql("user_num_log",$log));
                }
                echo json_encode(['code'=>0]);
                $this->televise($data1);
                exit;
            }else{
                //没有订单
                echo json_encode(['code'=>0]);
                $this->televise($data1);
                exit;
            }
        }
        echo json_encode(['code'=>0]);
        $this->televise($data1);
        exit;
    }

    public function wjyAction(){
        $data1 = '{"blockHight":"0","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","createtime":"1568007830000","address":"0x261B56Ef88241BE0f307ba5809c80afc64FdD6E0","serialNumber":"5d75e693d7980","uniquekey":"5d75e693d7980","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"f7d217bd9a4a473615671aa078fb413f83eb3242bdea7988bcb62dc0521ab5f3","txid":"0x3902956a87fda2497df2b2070c50e3e219f33fdd98f73be3222d058a52b8606c","type":"withdraw","userid":"1567578224910","curBlockHight":"0","blockhash":"\"\"","secondNoticeCounter":"0","firstNoticeCounter":"99","qty":"1.00000000","id":"50441","updatetime":"1568007830000","direction":"in","status":"new"}';
        echo json_encode(['code'=>0]);
        $this->televise($data1);
    }

    public function televise($data){
        $url = "http://103.84.85.154/notify/callback";
        $result = $this->curl_post($url,$data);
        file_put_contents(APP_PATH."/log/a.txt",$data."===>".$result."\r\t",FILE_APPEND);
        exit;
    }

    public function emptyAction()
    {
        // TODO: Implement __call() method.
    }
}

