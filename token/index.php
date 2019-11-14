<?php
session_start();
define("APP_PATH",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
define("TOKEN_URL",  'http://token.huziru.com'); /* 指向public的上一级 */
define("TOKEN_APP",  '1cff7c37-30c5-09ef-18c8-74d1df5de82c'); /* 指向public的上一级 */
define("TOKEN_KEY",  'a881a98-5ea8-4ee1-daee-a678ad79c795'); /* 指向public的上一级 */
set_time_limit(0);
include_once "load.php";
include_once "ini/config.php";
include_once "fun/functions.php";
if(!empty($_SERVER['PATH_INFO'])){
    $strData = $_SERVER['PATH_INFO'];
    $arrData = explode("/",$strData);
    $m = empty($arrData[1]) ? M :$arrData[1];
    $c = empty($arrData[2]) ? C :$arrData[2];
    $a = empty($arrData[3]) ? A :$arrData[3];
}else{
    $m =  M ;
    $c =  C ;
    $a =  A ;
}
$str = "\\$m\\controller\\$c";
$obj = new $str();
$obj->$a();
