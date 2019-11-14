<?php
const U = "utf-8";
const M = "Website";
const C = "Index";
const A = "login";
//控制编码
header("content-type:text/html;charset=".U);
//数据库配置
const TYPE = "mysql";
const Host = "localhost";
const User = "openice";
const Pass = "123Yunfan456";
const Dbname = "wallet";
const Port = "3306";
const Charset = "utf8";
const Prefix = "w_";
//获取站点根目录
$strurl = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
$url = strstr($strurl,"index.php",true);
//加载CSS文件
define("loadCSS",$url."public/css/");
//加载js文件
define("loadJS",$url."public/js/");
//加载img文件
define("loadIMG",$url."public/img/");
//框架根目录
define("ROOT",$url);
//V层跳C层的方法
define("BASE_URL",$strurl);

//钱包接口API地址
const API_URI1 = "http://39.100.241.187";
const API_URI2 = "http://token.huziru.com";



