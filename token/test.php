<?php

class Secret{
    private static $_privkey = '';
    private static $_isbase64 = true;
    /**
     * 初始化key值
     * @param  string  $privkey  私钥
     * @param  string  $pubkey   公钥
     * @param  boolean $isbase64 是否base64编码
     * @return null
     */
    public  function __construct(){
        self::$_privkey = "-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDFNLfsPy0K9FxT
NOR0A2d2Z62aS+8WXyni/yhnYDazK7hE35royjleAiQ4xBKhD6fSLsVSD13ge25v
1+Y5NgOk8ZDl7vpYPUuvlAQWUc/RJuFow9DrVbtJQm7BZcUg704f+9UW9okF3xha
d3z3VKo/F6aBZUzbRd8IPLhORZkihjJlxR3D43Njxba/Zl8dkkTe+LLBLl2Lz9oK
ea5eY1JDqrT4mumxe/x7mefEDZMKbtjaf9S4dfJV0OiVKQcOAHKV700df4wbiPS7
o7/+6FnO2zhr1mK7cd7F2Lx8lTS2fSEm5Riti36bXU7r9ERPNWKkRTbeWv+cOMxR
T5EHynYBAgMBAAECggEBAKzfG+PDNc8GivB9y05PtaC8HhjGO95WqbdNibOlSg4i
YzQs9/TdYRChu/LvHo2F1c2QJnVa9yZTzWnJzw2T5fnvsN9/cIFtqi1OCY8vRIfK
F4rpLPk/fCAqVkC+3+GRJeEvt4qbr14wRX74g9mkpGEOcG25yXfmdi8YW1Bx/l2d
zut4DQ+DOXcfOP02dfZIWMUuJJSD6VtGrq1HRymwmxh+oc1uELj17uLfQ1ivzf+q
D+KO4lDDOyKSbr/JwvnRrzHBI1UWFqA6PY9x9SZKonsFSHP3WHOhli/yzzj9tLSU
UPaay4prtNAMAylXQaZEEpdGB4Zt+vWDoKlbCZ/vY6kCgYEA8uWKlA2F7yBlJPjv
eCvTky8KtP+K3tc+ffqHNPV/nHf6uWeHFlzhSqtzK/u+wQC2gzVNhhzp19+yAL+a
nYFP76PzQMKiOOinm9wIjy5nS3onJ88geokhZCqn0XRGP28jCLxGJApkJCEwYaz+
zpgp1YaxLj/DK8kHzkSFEVS7TY8CgYEAz9gthh9rekDcZwx4sC9hLL0ZAIi3A1Rz
XuDx6SwJ6a1Oq5ACMlsJL/yqo/g0ja8+ghiYiKmfGrrKVTPVyS/pYAVwLU1NjCMB
A0YvxUDVSijJHhvA4D4a2wvMxsv4KiBNsRgtGlkP2MNE0tefBqC6//f5sL6CbqXu
4bSCVUzeW28CgYEAmSwHZ7XfcByNq/MPkEiS844Gwn0jpcM2tVr3SH5IKvO+OI0A
syl/KQdVabcnY65/ad6DNL+m92spZS9u4URalFRfYcdbZWfRyofHHgO1P/OYHZKQ
eLVhSTlc+sjIQ9hhz3BrCu9Cl0YWrIHbbYynVO/La87p1QLA2WJ8R6GXPCECgYBs
1hjQmPdg50ICvCceq1DJaqQDAZRELB7V2hkTLrpqIRSHBjwAPmeLN1Xr0vdCEjg7
S8HkHl5wUsvM3f3fAjXE0FQzhL1M2q+XFVPCiPanhL/8AjB3vE230nAC0aA7/vpv
9+b2WxjPO0F43uwpPlVt4F5hzQDkDAQRMMLnA/+UBQKBgHA1onRJX9pWTe3AUCbZ
i4mfLfFYyrinGNHE/oyx8qEW18LGXAo8oztZB4CawshjAtiSPNOhdPdPqY8LRq5r
oNDai+u9GohNHBWAkTKJG0RYxsBiR73qChTzg6qmeXxXXxqtb5+U2mP3uJxdqhOH
upkSJa4XhPXAoo4hFDuh5l7D
-----END PRIVATE KEY-----";
        self::$_isbase64 = true;
    }
    /**
     * 私钥加密
     * @param  string $data 原文
     * @return string       密文
     */
    private static function priv_encode($data){
        $outval = '';
        $res = openssl_pkey_get_private(self::$_privkey);
        openssl_private_encrypt($data, $outval, $res);
        if(self::$_isbase64){
            $outval = base64_encode($outval);
        }
        return $outval;
    }

    /**
     * 公钥解密
     * @param  string $data 密文
     * @return string       原文
     */
    private static function pub_decode($data,$_pubkey){
        $outval = '';
        if(self::$_isbase64){
            $data = base64_decode($data);
        }
        $res = openssl_pkey_get_public($_pubkey);
        openssl_public_decrypt($data, $outval, $res);
        return $outval;
    }

    /**
     * 公钥加密
     * @param  array $data 原文
     * @return string       密文
     */
    private  function pub_encode(array $data,$_pubkey){
        $outval = '';
        $data = json_encode($data);
        $res = openssl_pkey_get_public($_pubkey);
        openssl_public_encrypt($data, $outval, $res);
        if(self::$_isbase64){
            $outval = base64_encode($outval);
        }
        return $outval;
    }

    /**
     * 私钥解密
     * @param  string $data 密文
     * @return string       原文
     */
    private  function priv_decode(string $data){
        $outval = '';
        if(self::$_isbase64){
            $data = base64_decode($data);
        }
        $res = openssl_pkey_get_private(self::$_privkey);
        openssl_private_decrypt($data, $outval, $res);
        return $outval;
    }

    private function aes_crypt($data,$type = 0,$key="I8jOFVZSzEediYzQ",$iv="uycLyKE2hsLfB8Fg"){
        try{
            if( !preg_match("/^[\d\w]{16}$/i",$key) ){
                throw new \Exception("秘钥不规范");
            }
            if( !preg_match("/^[\d\w]{16}$/i",$iv) ){
                throw new \Exception("初始值不规范");
            }
                        if($type == 0){
                //加密
                //openssl_encrypt($str, 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
                //$crypted = openssl_encrypt($data, 'AES-128-CBC', $key,1, $iv);
                $crypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
                $data = base64_encode($crypted);
            }else{
                //解密
                //$crypted = openssl_decrypt($data, 'AES-128-CBC', $key,1, $iv);
                $crypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($data), MCRYPT_MODE_CBC, $iv);
                $data = rtrim($crypted, "\0");
            }
            return $data;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /*
    * 加密方法
    */
    public function encrypt(array $data,$_pubkey){
        try{
            $aes = $param = $return = [];
            $aes['key'] = self::get_random_string(16);
            $aes['iv'] = self::get_random_string(16,1);
            $return['token'] = $this->pub_encode($aes,$_pubkey);
            $return['data'] = $this->aes_crypt(json_encode($data),0,$aes['key'],$aes['iv']);
            return $return;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /*
    * 解密方法
    */
    public function decrypt(array $data){
        try{
            //使用私钥解密 token
            $aes = json_decode($this->priv_decode($data['token']),true);
            return json_decode($this->aes_crypt($data['data'],1,$aes['key'],$aes['iv']),true);            
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * 获取小于MD5 32位字符串
     * @param  string $len  字符串长度
     * @param  bool   $type = 0 从开头取 1 从末尾取 
     * @return string       随机字符串
     */
    public static function get_random_string($len=16,$type = 0){
        $uniqid = md5(microtime(true));
        if($type == 0){
            preg_match("/^[\w\d]{".$len."}/i",$uniqid,$match);
        }else{
            preg_match("/[\w\d]{".$len."}$/i",$uniqid,$match);
        }
        return $match[0];
    }

    //格式化字符串
    public static function get_bus_num(){
        $uniqid = md5(uniqid(microtime(true),true));
        preg_match('/^([\w\d]{6})([\w\d]{6})([\w\d]{6})([\w\d]{6})([\w\d]{8})$/i', $uniqid,$match);
        unset($match[0]);
        return implode('-', $match);
    }
    /**
     * 创建一组公钥私钥
     * @return array 公钥私钥数组
     */
    public static function new_rsa_key(){
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $privkey);
        $d= openssl_pkey_get_details($res);
        $pubkey = $d['key'];
        //$privkey = preg_replace("/[\s]+/",'',$privkey);
        //$pubkey = preg_replace("/[\s]+/",'',$pubkey);
        //去除秘钥空字符
        return array(
            'privkey' => $privkey,
            'pubkey'  => $pubkey
        );
    }
}

$str = "-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApDnJGAApI8jR7uVMu9tu
HqL9rSaAa6AYMzjfsZ8k5VYq7psG9Tli/RBgwNWWmCmMHAq/zz4JHMDpcePI2CIf
gBkWigmkrmpZ4SCpOqCh26vNjLhxPp66RjoInPL1ozZJ2zdiSIheiblX2CR4JLVI
XXJpxgmWl3trte84Zz6VBeScecfabde+g9NnyOC7KQn7FFiGM5MmFZ1japVf8ERP
/GwvfN4gVljSKRpbvTewZSG8v4RJY2V3uyx5rPXK3RIAlV3lxqsV/XT6d6Ljww8b
m0otTJsBE3soNZBeCMvElwTyT1NBWC6bYfDmVCsksTAbMcL9o3LHg7ZiHza3kzNd
iwIDAQAB
-----END PUBLIC KEY-----";
$Secret = new Secret();
$data = ["bus_num"=>"ee3a6cdd-7f42-6e94-4fc3-c2fd229a9c36","coin_tp"=>"eth"];
//$result = $Secret->encrypt($data,$str);



$arr = [
"token"=>"INtqcKDNHmgSd0iuG5uu4p\/rVEDZhptP622LqShSahNne6qyv6M4S1z5dG759yUlwZhnV86Q5YAh4NaTCppaptrD9Dx1VebPp6YTHlIavfkZBCxv+n6O8HErPRVH402zWFDMx5n8e0R93kb60f+4cBYMczMPfEKfaJdYep2tIo94H5DUhVy6l2xl1\/GJQhsvD81rLv+5cOMgjyAhrr5GarN0IJTf9+V6ORFIMKEzFMcF18h2ZetS1H1daFJnMYghmFgt5sdOfo3KJ+CGSwUPyahP7+gwOPnJjVNbUeDSfFWisXDGXwhVwUWHEqo89UbaYx6yLLzcVVL60uOI4eiBuQ==",
"data"=>"rC\/q63IFiPrv+OWiCk\/UTyYV7NzVzQ7TEm1nFHS1+Btm1gqXrRqWPsizr33HZUkj7JwK7xkVW7PAzUglI5rb9q0dAhOFOx1goGxLvFAR1ft7LdGvcpAmrLg+vnb0D6oHsK4w3udWqaubTc43ZrZ4t\/0mny+1KD1PULsoGQBJzmYML12i7MBju27lthe9cLl4Z6IZP9qYZEUAKQhhcPSecw=="
];


$result =  $Secret->decrypt($arr);

var_dump($result);

