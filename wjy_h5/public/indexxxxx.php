<?php
use Yaf\Application;
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials : true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie");

// [ 应用入口文件 ]
date_default_timezone_set("Asia/Shanghai");
if (!defined('__ROOT__')) {
    $_root = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    define('__ROOT__', (('/' == $_root || '\\' == $_root) ? '' : $_root));
}

// 定义应用目录
define("APP_PATH", dirname(dirname(__FILE__)));

// 定义模块目录
// 定义应用目录
define("APP_MODULES", APP_PATH.'/application/modules');
//include APP_PATH. "/library/function/helper.php";

//开发环境 product：线上环境；develop：线下开发环境
define('APP_ENV', 'develop');

//加载配置
$app = new Application(APP_PATH . "/conf/app.ini", APP_ENV);
//$app = new Yaf_Application(APP_PATH . "/conf/app.ini", APP_ENV);
//启动应用
$app->bootstrap()->run();

