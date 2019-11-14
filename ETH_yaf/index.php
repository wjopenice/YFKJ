<?php
use Yaf\Application;
use Yaf\Exception;
//http 301 https
if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on' ){
    header('Location: https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);exit();
}
error_reporting(0);
// Autoload 自动载入
define("APP_PATH",  realpath(dirname(__FILE__) . '/')); /* 指向public的上一级 */
// Autoload 自动载入
$app  = new Application(APP_PATH . "/conf/application.ini",ini_get('yaf.environ'));
$app->bootstrap()->run();






