<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/10/10
 * Time: 17:28
 */


//跨域
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin: *");
//CORS
header("Access-Control-Request-Methods:GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Headers:x-requested-with,content-type,test-token,test-sessid');

$keyword = $_GET['keyword'];

if (empty($keyword))
{
    $data['code'] = 0;
    $data['msg'] = '请输入查询关键词';
    $data['data'] = '';
    echo json_decode($data);die();
}
/**
 *  GET
 */
/*
 *发送CURL get请求
 */
function _request($curl,$method='get', $data=null,$https=false)
{


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $curl);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($https){

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    if($method == 'post'){

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    $str = curl_exec($ch);
    curl_close($ch);
    return $str;

}

$url = 'https://service.xiaoyuan.net.cn/garbage/index/search?kw='.$keyword;


$res = _request($url);
echo $res;die();







