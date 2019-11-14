<?php
// [ 应用入口文件 ]
date_default_timezone_set("Asia/Shanghai");
if (!defined('__ROOT__')) {
    $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
}
error_reporting(E_ALL);
// 定义应用目录
define("APP_PATH", dirname(dirname(__FILE__)));

define("APP_MODULES", APP_PATH.'/application/modules');
define("MODEL", APP_PATH.'/application/models');
//include APP_PATH. "/library/function/helper.php";
define("APP_ATTACHMENT", APP_PATH.'/public/attachment');

//开发环境 product：线上环境；develop：线下开发环境
$host = $_SERVER['HTTP_HOST'];
if ($host == 'shuidihuan.name')
{
    define('APP_ENV', 'product');
    error_reporting(0);
} else
{
    define('APP_ENV', 'develop');
}


//加载配置
$app = new Yaf_Application(APP_PATH . "/conf/app.ini", APP_ENV);
//启动应用
$app->bootstrap()->run();
