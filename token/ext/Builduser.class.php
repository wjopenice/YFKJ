<?php
namespace ext;
class Builduser{
    public function user_add(){
//生成用户App key
//30062115-eb1c-4dcc-8e01-e19bd8c29101
//生成用户Secret key
//61f23786-3aca-4a60-a84e-9cf8867700c4
        $arr1 = range("a","z");
        shuffle($arr1);
        $arr2 = rand(0000,9999);
        $arr3 = range("A","Z");
        shuffle($arr3);
        $newArr = array($arr1[0],$arr2,$arr3[0]);
        shuffle($newArr);
        $strData1 = implode($newArr);
        $app_str = md5($strData1.uniqid());
        shuffle($newArr);
        $strData2 = implode($newArr);
        $secret_str = md5($strData2.uniqid());
        $data['app_key'] = substr($app_str,0,8)."-".substr($app_str,8,4)."-".substr($app_str,12,4)."-".substr($app_str,16,4)."-".substr($app_str,20);
        $data['secret_key'] = substr($secret_str,0,8)."-".substr($secret_str,8,4)."-".substr($secret_str,12,4)."-".substr($secret_str,16,4)."-".substr($secret_str,20);
        return $data;
    }
}





