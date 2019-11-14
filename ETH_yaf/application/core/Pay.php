<?php
namespace app\core;
class Pay{
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
        $data['coinType'] = "ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106";
        $data['page'] = 1;
        $result = $this->createLinkstringUrlencode($data);
        $data['sign'] = $this->sha256($result, $this->appsecrect);
        $result2 = json_encode($data,320);
        $newData = $this->curl_post($api, $result2);
        var_dump($newData);
        exit;
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
        var_dump($newData);
        exit;
    }
    //提现
    public function withdraw($type,$order,$price,$address){
        $api = $this->url."api/withdraw";
        $data['appKey'] = $this->appkey;
        if($type == 1){
            $data['coinType'] = $this->DyxConiType;
        }else if($type == 2){
            $data['coinType'] = $this->UsdtConiType;
        }
        $data['to'] = $address;  //提币地址
        $data['quantity'] = $price; //提币数量
        $data['serialNumber'] = $order; //订单号
        $data['memo'] = ""; //备注
        $data['feeCost'] = 0; //矿工费
        $data['gasLimit'] = "66000.00000000"; //最多使用gas数量。ETH及ERC 20代币用到
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
        return json_decode($newData,true);
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
        foreach ($para as $key=>$val){
            $arg .= $key . "=" . urlencode($val) . "&";
        }
//        while (list ($key, $val) = each($para)) {
//            $arg .= $key . "=" . urlencode($val) . "&";
//        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }
    public function test(){
        $type = isset($_GET['type'])?$_GET['type']:"resSign";
        switch($type){
            case "address":
                var_dump($this->address());
                break;
            case "selectlog":
                var_dump($this->selectlog());
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

    public function abc($type,$order,$price,$address){
        if($type == 1){
            echo $this->DyxConiType;
        }else{
            echo $this->UsdtConiType;
        }

    }
}