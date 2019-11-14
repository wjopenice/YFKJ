<?php
use Yaf\Application;
class xcpModel{
    public $db;

    public function __construct()
    {
        $config = [
            "driver"=>"mysql",
            "hostname"=>"192.168.24.188",
            "port"=>3306,
            "database"=>"xcp",
            "username"=>"openice",
            "password"=>"123Yunfan456",
            "charset"=>"utf8",
            "prefix"=>""
        ];
        $this->db = new dbModel($config);
    }

    public function get_xcoinpay_user(){
        $user = $this->db->field("id,access,password,ether_address,unix_timestamp(ctime) as loca_time")
            ->table("xcoinpay_user")
            ->select();
        $newArr = [];

        foreach ($user as $k=>$v){
            $newArr[$k]['id'] = $v['id'];
            $newArr[$k]['username'] = $v['access'];
            $newArr[$k]['userpass'] = $v['password'];
            $newArr[$k]['tel'] = $v['access'];
            $newArr[$k]['token_address'] = $v['ether_address'];
            $newArr[$k]['usdt_address'] = $v['ether_address'];
            $newArr[$k]['xyt_address'] = $v['ether_address'];
            $newArr[$k]['btc_address'] = $v['ether_address'];
            $newArr[$k]['floor_id'] = 0;
            $newArr[$k]['create_time'] = (int)$v['loca_time'];
            $newArr[$k]['ip'] = "";
            $newArr[$k]['sign'] = $this->user_sign($v['id']);
            $newArr[$k]['new_status'] = 0;
        }

//        for ($i=0;$i<count($newArr);$i++){
//            $this->db->action($this->db->insertSql("y_user",$newArr[$i]));
//            echo $i."insert ok<hr>";
//        }
        echo "再见";
        //echo "<pre>";
        //print_r($newArr);
        exit;

        return $user;
    }


    public function user_sign($user_id){
        $uid = sprintf("%05d",$user_id);
        $iqidstr = substr(uniqid(), 7, 13);
        return substr($iqidstr, 0, 3).$uid.substr($iqidstr, 3, 3);
    }
}