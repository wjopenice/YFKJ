<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

use think\Db;

class BackupController extends Controller
{

    protected function init()
    {
        Yaf_Dispatcher::getInstance()->disableView();
    }

    public function indexAction()
    {

       $this->mysqldump();
       die();
    }

    //数据库备份
    function mysqldump($tableName = ''){

        $tableName = $tableName ? $tableName : 'user';
        $db_info = Config::get('db');
       // print_r($db_info);die();
        $username = $db_info['username'];//你的MYSQL用户名
        $password = $db_info['password'];;//密码
        $hostname = $db_info['hostname'];;//MYSQL服务器地址
        $dbname   = $db_info['database'];;//数据库名
        $port   = $db_info['hostport'];//数据库端口
        $dumpfname = $tableName . "_" . date("YmdHi").".sql";
        $filepath = APP_PATH."/public/database/";
        mkdirs($filepath);
        $path = $filepath.$dumpfname;
       // $command = "mysqldump -P{$port} -h{$hostname} -u{$username} -p{$password} {$dbname} > {$path}";
        $command = "mysqldump -u root -h {$hostname} -p{$password} {$dbname}  > {$path}";
        system($command,$retval);
        exit($retval);
    }

    //数据库备份
    function mysqldumpall($tableName){
        $username = Yii::$app->params['user'];//你的MYSQL用户名
        $password = Yii::$app->params['pass'];;//密码
        $hostname = Yii::$app->params['host'];;//MYSQL服务器地址
        $dbname   = Yii::$app->params['dbname'];;//数据库名
        $port   = Yii::$app->params['port'];;//数据库端口
        $dumpfname =  "localhost_" . date("YmdHi").".sql";
        $path = dirname(dirname(__FILE__))."/data/".$dumpfname;
        $command = "mysqldump -P{$port} -h{$hostname} -u{$username} -p{$password} {$dbname} {$tableName} > {$path}";
        system($command,$retval);
        $zipfname = "localhost_" . date("YmdHi").".zip";
        $zippath = dirname(dirname(__FILE__))."/data/".$zipfname;
        $zip = new \ZipArchive();
        if($zip->open($zippath,ZIPARCHIVE::CREATE))
        {
            $zip->addFile($path,$path);
            $zip->close();
        }
        if (file_exists($zippath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($zippath));
            flush();
            readfile($zippath);
            exit;
        }
    }



}
