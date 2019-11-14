<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/10/14
 * Time: 11:33
 */


$config = array(
    "digest_alg" => "sha512",
    "private_key_bits" => 1024,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,   //加密类型
);
$res = openssl_pkey_new($config);
//while($message = openssl_error_string()){
//    echo $message.'<br />'.PHP_EOL;
//}
openssl_pkey_export($res, $privKey);

//将公共密钥从$ res提取到$ pubKey
$pubKey = openssl_pkey_get_details($res);
$pubKey = $pubKey['key'];
echo "公:".$pubKey;
echo "<br>";
echo "私:".$privKey;
