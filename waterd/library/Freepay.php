<?php

class Freepay
{
    public $AppKey = '1cff7c37-30c5-09ef-18c8-74d1df5de82c';
    public $AppSecret = 'ba881a98-5ea8-4ee1-daee-a678ad79c795';
    public function array2url($params) {
        $str = '';
        foreach($params as $key => $val) {

            $str .= "{$key}={$val}&";
        }
        $str = trim($str, '&');
        $str .= $this->AppSecret;
        return $str;
    }


   public function bulidSign($params) {
        unset($params['sign']);
        ksort($params);
        $string = $this->array2url($params);
        $string = hash_hmac('sha256', $string, $this->AppSecret);
        return $string;
    }

    /**
     * PHP发送Json对象数据
     *
     * @param $url 请求url
     * @param $jsonStr 发送的json字符串
     * @return array
     */
    public function http_post_json($url, $jsonStr)
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

    public function http_post($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 4);
        curl_setopt($ch, CURLOPT_ENCODING, ""); //必须解压缩防止乱码
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; zh-CN) AppleWebKit/535.12 (KHTML, like Gecko) Chrome/22.0.1229.79 Safari/535.12");
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $output = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($output, true);
        return $res;

    }


    /** 获取冷钱包地址 */
    public function getAddress()
    {
        $data['appKey'] = $this->AppKey;
        $data['coinType'] = 'ETH';
        $sign = $this->bulidSign($data);
        $data['sign'] = $sign;
//        print_r($data);die();
//        $jsonStr = json_encode($data, 320);
        $apiUrl = 'http://token.xcoinpay.io/Api/Api/user_addr';
        $result = $this->http_post($apiUrl, $data);
        return $result;
    }

    /** 提现 */
    public function withdraw($withdraw = array())
    {
        $coinType = $withdraw['currency'];
        $data['appKey'] = $this->AppKey;
        $data['coinType'] = $coinType;
        $data['price'] = $withdraw['price'];
        $data['to'] = $withdraw['to'];
        $data['from'] = $withdraw['from'];
        $data['order_on'] = $withdraw['order_on'];
        $sign = $this->bulidSign($data);
        $data['sign'] = $sign;
        $apiUrl = 'http://token.xcoinpay.io/Api/Api/remote_withraw';
        $result = $this->http_post($apiUrl, $data);
        return $result;
    }


}








