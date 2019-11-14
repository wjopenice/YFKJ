<?php
/**
 * Created by PhpStorm.
 * User: lidongxu
 * Date: 2019/2/10
 * Time: 16:29
 */


// RSA加密方式(前端需要一样使用RSA加密)
class RSA
{
    /**
     * key值
     */
    public static $key = "3bba984c642761d9dc0cdef9664494d685244bcc";
    /**
     * @var string
     */
    private static $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCFs0bEmm1wYD6vxPSUKkY6GP+499cqAnKJk1r/VXTtPrS/OJhsDhzJGkYjQ9P0CSxD4nQy2QeK4qSoXGVEnoC9h/SqN1OmY1XRNnsgXgDC9prTpJsciSxGtIPI/LK/1SfXh1r+K9iWgLlrsqPmr/HAjjSfv0YvYTHBz/2n+fNjZwIDAQAB
-----END PUBLIC KEY-----
EOD;
    /**
     * @var string
     */
    private static $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAIWzRsSabXBgPq/E9JQqRjoY/7j31yoCcomTWv9VdO0+tL84mGwOHMkaRiND0/QJLEPidDLZB4ripKhcZUSegL2H9Ko3U6ZjVdE2eyBeAML2mtOkmxyJLEa0g8j8sr/VJ9eHWv4r2JaAuWuyo+av8cCONJ+/Ri9hMcHP/af582NnAgMBAAECgYBSRzh2xv4wWNpa+t70y2s6V6YPo13jOWxZI7itR17UnXFH+EE8xhaK38Nn8lbeaEK4aATQQeU1YDKqdWnQp6Zbk3ib/WKBT2KR2FSQvA3ofi1dvrSY9gx+FfWx0qQ4B3K/holGnEYh8+tiPT0FdsMC/4t1cRpu9kooN/l566zAKQJBAOeH5blt5jafmO18jNl7np6dsUgMnomZy/S05GubjViMDfBG5/WVIbfMKD8DuiPlxIybeG+TVA5kgb2nE6fW9hMCQQCT1I77jIay766dikpDjLLGLo5gRiiR5curmwXMzREwpLSxRPe9l+HsSvUh8DlRzkNZK+UCJWCg5nwPj1hzTdfdAkEAxOi+BnMiHIilbizEOV66a1nf4U/iMVKctDR4I9B6aLlMTXJwalt3/rHh9J293DPYcmDzD6l0Dn2KHfqPa+oVAwJAD6by2KmBXZLJHz8UK+DK0Pb+9iyXgRMepHXOgGe6CTd3NknCHV3metlY6RYBS6sWMGvYXIjOmVquCgu4ZsT4NQJBAMw5qT+5IU/EKpWXEDOnMneOfIF18MSpgjIGPafz3Fxt5MXbmHYBb5zd/yNj17QEI1sbRS+MZfrmkmEa2TTrOY0=
-----END RSA PRIVATE KEY-----
EOD;


    // 加密解密
    // E: 公钥加密
    // D: 私钥解密
    public static function authcode($string, $operation = 'E') {
        $ssl_public     = RSA::$publicKey;
        $ssl_private    = RSA::$privateKey;
        $pi_key         = openssl_pkey_get_private($ssl_private);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
        $pu_key         = openssl_pkey_get_public($ssl_public);//这个函数可用来判断公钥是否是可用的
        if( false == ($pi_key || $pu_key) ) return '证书错误';
        $data = "";
        if( $operation == 'D') {
            openssl_private_decrypt(base64_decode($string),$data,$pi_key);//私钥解密
        } else {
            openssl_public_encrypt($string, $data, $pu_key);//公钥加密
            $data = base64_encode($data);
        }
        return $data;
    }

    // 私钥加密
    public static function privateCode($string){
        $pi_key = openssl_pkey_get_private(RSA::$privateKey);
        openssl_private_encrypt($string, $data, $pi_key);
        $data = base64_encode($data);
        return $data;
    }

}
