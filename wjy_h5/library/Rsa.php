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
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuTOrfbRt/vTmBJAAemTz
J3TluvNw9bxOn2uPO/KjDKjegdIVtRWDxO/U6OUqdT2nFW9RMmKjsjqVZ9XsaZcx
XigE0Bnf23n+xBsU5TKT8EXFSazVsja10CUvpzfem6jAfLzyW2SsNKtWvBlRJpoH
fOjspYDDQV4+6z3NrR4PRUUonWwFC0FA4WvGfKeDdnHnGLV/UhghmnyO41bnEd5P
eZAuUP3+02ELRJOZANap1gbQaddn+124qPjVosJih2CYG+Nftw6UEGHatD5VXR6u
Z4tMTo9Xk4Diqr8POTQdyI+ySgLoQIyQwPs+/i4a6sxj1aqhViUuI8tWCemOM1Yh
uwIDAQAB
-----END PUBLIC KEY-----
EOD;
    /**
     * @var string
     */
    private static $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQC5M6t9tG3+9OYE
kAB6ZPMndOW683D1vE6fa4878qMMqN6B0hW1FYPE79To5Sp1PacVb1EyYqOyOpVn
1explzFeKATQGd/bef7EGxTlMpPwRcVJrNWyNrXQJS+nN96bqMB8vPJbZKw0q1a8
GVEmmgd86OylgMNBXj7rPc2tHg9FRSidbAULQUDha8Z8p4N2cecYtX9SGCGafI7j
VucR3k95kC5Q/f7TYQtEk5kA1qnWBtBp12f7Xbio+NWiwmKHYJgb41+3DpQQYdq0
PlVdHq5ni0xOj1eTgOKqvw85NB3Ij7JKAuhAjJDA+z7+LhrqzGPVqqFWJS4jy1YJ
6Y4zViG7AgMBAAECggEAC5PoV55s9fBXZNefwAcJkGDlb6+CU0pnW938gVHk0YjJ
CDKa+swShcA8fia9ZcDp7hQcblruQtkYt/oOonc6Ndmom13SucTBoX7T6pQj3XRv
JfDxFwgGi7GXbgu3FeIRznp9aCs9/LjtXirzAMfGSg/Bo4MOMcrzgf9GGVl0uO7D
n6LyS1Qqb796TzazykVejYK63QaPoNauwYtz70AIbj8syKpOJomwDxojD97hFmVt
qM80fyn7ugfKbVxKSZD2qyMQJCDo0W/97FazJTUmrsQwfLHWdQNXSXmpk6YFSXmp
LmAIzaQSgPQCIeKl7sEvCXoauE4fvjXGO3O3UqhdyQKBgQDj090v8RbMOFQY1Jng
BVjw/JB9S57cnbLSQDDc4AvwMEt1cOi0HR3sN7IFGPQJv4/CnoT0V+iZNkmiUfmk
qFcHcLHJltBeiLFHphDcju7z9/uqKWu9fmShsdtziru/xaUTnvjctBltG+yzwXc2
8MX0Depy6mrsrBD4G8e8YVjljQKBgQDQGnCoGJMx3qWz3JoqmRPpPt5QdXuKYg5u
1F+e7UctF+4dzFUZq9ZcTGp4uXZ3hJfq2bO4qT0UsyLkw0Ybq3jF9kEws4QyAlvy
kus1M+Sg4np/aSonPqzjIbHd9Vpg6sz2BQD5Qsm76yH4p5O5FkLPVzTz+NPvF4gZ
/sGbb2heZwKBgQCSPQUmZ9ddYO7CPX1D6crMbSIF+wtOyz7kbGWw8XNYSne1O7Nz
IZl6bcmsMfc7ZqXNo9jtypu39LvRIFNhGNk8Il9Maaz4cT9yISuDl4/UNCJreEj7
Cm1nyPV3Nd8L2r76WDmf2U7U1W7bd1yhfG1kGaaa+tOFdXEzAQn1ZnscZQKBgQC7
nNqr8VviveONv/iNJuy4f6FV0FTMcZKpgHWkhi6BDO3zl1+xgSYEmC/1Ht8a6UM8
y4AnnwweOVHgA9yU3YZIkrY6/dzUaSQUEThUArDACu59J4aFUw/yxVHh1Wzhq02V
DvXNwtS0wksiLZIUb2lUHD71JQOMrh5ZsT9CrrHdSQKBgQDFliPjs7cd/ElJ+SB3
I6elvT+2WC5az9cQ6DP26ULpmilHPvbxBI+r+Faje8Yq3sChRzRr8WSX+PwxZiwz
23nUaztNn+RDgekja+YNMFyqLTjSOE1N+Ko+phFisT0keWQ2vhVaZkEEzAKQcq6N
ZETx2Utup/joog5/eMpvX5DHQQ==
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
    public static function privateCode(){
        $pi_key = openssl_pkey_get_private(RSA::$privateKey);
        openssl_private_encrypt(RSA::$key, $data, $pi_key);
        $data = base64_encode($data);
        return $data;
    }
    
}