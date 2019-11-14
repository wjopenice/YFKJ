<?php

//需要使用热钱包提币的时候，ERC 20需要往这个热钱包地址打币：0x036c470cf3494d61a60ded5c84a0a791e112401f

//erc 20 USDT coin type:  ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7
//DYX的coin type： ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106

include "RSA.php";

define('AppKey', '3415654e-701d-4a92-9c41-3eeb1e2d91e6');
define('AppSecret', '6dbfb3c4-108f-42d4-a495-a2d2a3cf0893');

class Tool
{
    static function array2url($params) {
        $str = '';
        foreach($params as $key => $val) {

            $str .= "{$key}={$val}&";
        }
        $str = trim($str, '&');
        return $str;
    }

    static function bulidApiSign($params) {
        unset($params['sign']);
        ksort($params);
        $string = self::array2url($params);
        $string = hash_hmac('sha256', $string, 'wtreeappkey');
        return $string;
    }

    static function bulidSign($params) {
        unset($params['sign']);
        ksort($params);
        $string = self::array2url($params);
        $string = hash_hmac('sha256', $string, AppSecret);
        return $string;
    }

    static function buidRsaSign($params)
    {
        unset($params['rsaSign']);
        ksort($params);
        $string = self::array2url($params);

        $result = RSA::privateCode(md5($string));
        return $result;
    }

    /**
     * PHP发送Json对象数据
     *
     * @param $url 请求url
     * @param $jsonStr 发送的json字符串
     * @return array
     */
    static function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //return array($httpCode, $response);
        $result = json_decode($response, true);
        return $result;
    }


    /** 获取冷钱包地址 */
    static function getAddress()
    {
        $data['appKey'] = AppKey;
        $data['coinType'] = 'ETH';
        $sign = self::bulidSign($data);
        $data['sign'] = $sign;
        $jsonStr = json_encode($data, 320);
        $apiUrl = 'http://api.morningpay.io/api/api/getAddress';
        $result = self::http_post_json($apiUrl, $jsonStr);
        return $result;
    }

    /** 获取冷钱包收币记录 */
    function queryColdRecord()
    {
        $data['appKey'] = AppKey;
        $data['coinType'] = 'ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106';
        $data['page'] = 1;
        $sign = bulidSign($data);
        $data['sign'] = $sign;
        $jsonStr = json_encode($data, 320);
        $apiUrl = 'http://api.morningpay.io/api/api/queryColdRecord';
        $result = http_post_json($apiUrl, $jsonStr);
        return $result;
    }
    /** 获取冷钱包提现记录 */
    static function queryWithdrawRecord()
    {
        $data['appKey'] = AppKey;
        $data['coinType'] = 'ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106';
        $data['page'] = 1;
        $sign = bulidSign($data);
        $data['sign'] = $sign;
        $jsonStr = json_encode($data, 320);
        $apiUrl = 'http://api.morningpay.io/api/api/queryWithdrawRecord';
        $result = http_post_json($apiUrl, $jsonStr);
        return $result;
    }

    /** 提币 */
    static function withdraw($withdraw)
    {

        $coinType = '';
        if ($withdraw['currency'] == 'usdt' || $withdraw['currency'] == 'USDT')
        {
            $coinType = 'ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7';
        }
        if ($withdraw['currency'] == 'dyx' || $withdraw['currency'] == 'DYX')
        {
            $coinType = 'ETH_DYX_0x042f972ac93404f0fcbe4e3a0729f0b395232106';
        }
        if (empty($coinType))
        {
            return error('未获取到币种');
        }

        $data['appKey'] = AppKey;
        $data['coinType'] = $coinType;
        $data['to'] = $withdraw['address'];
        $data['quantity'] = $withdraw['money'];
        $data['serialNumber'] = $withdraw['order_no'];
        $data['memo'] = '';
        $data['feeCost'] = 0;
        $data['gasLimit'] = '66000.00000000';
        $data['gasPrice'] = '0.00000004';
        $data['contractAddress'] = '';
        $data['symbol'] = '';
        $rsaSign =  self::buidRsaSign($data);
        $sign =  self::bulidSign($data);
        $data['rsaSign'] = $rsaSign;
        $data['sign'] = $sign;
        $jsonStr = json_encode($data, 320);
        $apiUrl = 'http://api.morningpay.io/api/api/withdraw';
        $result =  self::http_post_json($apiUrl, $jsonStr);
        return $result;
    }

    public function withdraw1($type,$order,$price,$address){
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
        $data['memo'] = "test"; //备注
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


    function addAddress($string)
    {
        // 从文件中读取数据到PHP变量
        $json_string = file_get_contents('address.json');
        // 把JSON字符串转成PHP数组
        $data = json_decode($json_string, true);
        $data[] = $string;
        // 把PHP数组转成JSON字符串
        $json_string = json_encode($data);

        // 写入文件
        file_put_contents('address.json', $json_string);

    }
}








