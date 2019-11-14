<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class UserController extends Controller_Abstract{
    const Login_Key = "BplVQpWfm5cjuKmbiKUvQqdErqhAp8vG";
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }
    //我的邀请
    public function youinvitationAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)){
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $usersys_discount = $activitymodel->get_sysuser_discount($user);
            $oneData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],$usersys_discount[0]['user_minimum_amount'],1);
            $twoData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],$usersys_discount[0]['user_minimum_amount'],2);
            $threeData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],$usersys_discount[0]['user_minimum_amount'],3);
            $userdata = $activitymodel->get_user_data($user);
            $this->getView()->assign([
                "user" => $user,
                "oneData" => $oneData,
                "twoData" => $twoData,
                "threeData" => $threeData,
                "userdata" => $userdata,
                "discount_id"=>$discount_id
            ]);
        }
    }
    //合作方
    public function partnersAction(){
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $this->getView()->assign(["user" => $user]);
        }
    }
    //合作方登录
    public function partnersloginAction(){
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else{
            $this->getView()->assign("content", "Hello2 World");
        }
    }
    //个人中心
    public function personalcenterAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)){
            success("请先登录","/index/login");
        }else {
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $userdata = $activitymodel->get_user_data($user);
            $offemodel = new offeringModel();
            $offering_data = $offemodel->get_user_offering($user);
            $uselog_data = $offemodel->get_user_log($user);
            $qrcode = $this->qrcode($userdata['token_address']);
            $lifemodel = new lifeModel();
            $life_dyx = $lifemodel->get_life_user($user);
            $month_dyx = $lifemodel->get_month_user($user);
            $month_dyx_log = $lifemodel->get_month_dyx($user);
            $this->getView()->assign([
                "user"=>$user,
                "userdata"=>$userdata,
                "qrcode"=>$qrcode,
                //"offering_data"=>$offering_data,
                "uselog_data"=>$uselog_data,
                "discount_id"=>$discount_id,
                "life_dyx"=>$life_dyx,
                "month_dyx"=>$month_dyx,
                "month_dyx_log"=>$month_dyx_log
            ]);
        }
    }
    //交易记录
    public function transactionAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $this->getView()->assign(["user" => $user,"discount_id"=>$discount_id]);
        }
    }
    //老用户钱包与理财信息
    public function olduserAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $userinfo = $this->db->field("*")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            $xyt = $this->db->zssum("pay_wallet_order","xyt_number","num","xcoinpay_userid = {$userinfo['id']} and status = 3");
            $userinfo['xyt_num'] = $xyt;
            $userinfo['xyt_available_balance'] = $xyt;
            $shop = $this->db->field("*")
                ->table("y_pay_wallet_order")
                ->where("xcoinpay_userid = '{$userinfo['id']}'")
                ->select();
            if(!empty($shop[0]['id'])){
                $this->getView()->assign(["user" => $user,"userinfo"=>$userinfo,"shop"=>$shop,"discount_id"=>$discount_id]);
            }else{
                $this->getView()->assign(["user" => $user,"userinfo"=>$userinfo,"shop"=>[],"discount_id"=>$discount_id]);
            }
        }
    }
    //用户管理
    public function useradAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            if($this->getRequest()->isPost()){
                Dispatcher::getInstance()->autoRender(false);
                $tel = post('tel');
                $pass = $this->hashkey(addslashes(post('pass')),self::Login_Key);
                $this->db->action($this->db->updateSql("user",["userpass"=>$pass],"username='{$tel}'"));
                success("密码修改成功","/index/personalcenter");
            }else{
                $user = $this->sessions->user;
                $activitymodel = new activityModel();
                $userdata = $activitymodel->get_user_data($user);
                $this->getView()->assign(["user" => $user, "userdata" => $userdata,"discount_id"=>$discount_id]);
            }
        }
    }
    //霸屏行动
    public function bapingAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $h = date("H",time());
            switch(true){
                case $h<=11 && $h>=0:;
                    $start = date("Y-m-d 00:00:00",strtotime("-1 day"));
                    $end =  date("Y-m-d 11:00:00",time());
                    break;
                case $h>11 && $h<=23:
                    $start = date("Y-m-d 00:00:00",time());
                    $end =  date("Y-m-d 11:00:00",strtotime("+1 day"));
                    break;
            }
            $user_info = $this->db->field("*")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            $screen = $this->db->field("*")
                ->table("y_pa_screen")
                ->where("user_id = '{$user_info['id']}'")
                ->find();
            $result = $this->db->field("*")
                ->table("y_dyx_write")
                ->where("id = 1")
                ->find();
            $user_result = $this->db->field("*")
                ->table("y_dyx_user_write")
                ->where("u_id = {$user_info['id']} AND create_time BETWEEN '{$start}' AND '{$end}'")
                ->find();
            if(!empty($screen)){
                $this->getView()->assign(["user" => $user,"status"=>$screen['status'],"result"=>$result,"user_result"=>$user_result,"discount_id"=>$discount_id]);
            }else{
                $this->getView()->assign(["user" => $user,"status"=>-1,"result"=>$result,"user_result"=>$user_result,"discount_id"=>$discount_id]);
            }
        }
    }
    //提现dyx
    public function ajaxdyxAction(){
        $username = post("u");
        $price = post("c");
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($user['id'])){
            $sql = "UPDATE y_user SET token_available_balance = token_available_balance - {$price},token_freeze_balance = token_freeze_balance + {$price} WHERE id = {$user['id']}";
            $bool = $this->db->action($sql);
            if($bool){
                $data['u_id'] = $user['id'];
                $data['token_num'] = $price;
                $data['currency'] = 1;
                $data['create_time'] = time();
                $data['order_no'] = uniqid();
                $data['address'] = post("d");
                $data['status'] = 0;

//                if($price > 100){
//                    $data['auto_address'] = 1;
//                    $data['build_status'] = 1;
//                }else{
//                    $stat = $this->dyx($price,$data['order_no'],$data['address']);
//                    $data['build_status'] = $stat;
//                    $data['auto_address'] = 0;
//                }

                $stat = $this->dyx($price,$data['order_no'],$data['address']);
                $data['build_status'] = $stat;
                $data['auto_address'] = 0;

                $bool = $this->db->action($this->db->insertSql("withdraw",$data));
                if($bool){
                    $message = ["message"=>"申请成功"];
                }else{
                    $message = ["message"=>"申请失败"];
                }
            }else{
                $message = ["message"=>"申请失败,金额修改失败"];
            }
        }else{
            $message = ["message"=>"申请失败,用户不存在"];
        }
        echo json_encode($message);
        exit;
    }
    //提现usdt
    public function ajaxusdtAction(){
        $username = post("u");
        $price = post("c");
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($user['id'])){
            $sql = "UPDATE y_user SET usdt_available_balance = usdt_available_balance - {$price},usdt_freeze_balance = usdt_freeze_balance + {$price} WHERE id = {$user['id']}";
            $bool = $this->db->action($sql);
            if($bool){
                $data['u_id'] = $user['id'];
                $data['token_num'] = $price;
                $data['currency'] = 2;
                $data['create_time'] = time();
                $data['order_no'] = uniqid();
                $data['address'] = post("d");
                $data['status'] = 0;
                if($price >= 100){
                    $data['auto_address'] = 1;
                    $data['build_status'] = 1;
                }else{
                    $stat = $this->usdt($price,$data['order_no'],$data['address']);
                    $data['build_status'] = $stat;
                    $data['auto_address'] = 0;
                }
                $bool = $this->db->action($this->db->insertSql("withdraw",$data));
                if($bool){
                    $message = ["message"=>"申请成功"];
                }else{
                    $message = ["message"=>"申请失败"];
                }
            }else{
                $message = ["message"=>"申请失败,金额修改失败"];
            }
        }else{
            $message = ["message"=>"申请失败,用户不存在"];
        }
        echo json_encode($message);
        exit;
    }
    //任务记录
    public function taskrecordAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        $user = $this->sessions->user;
        $h = date("H",time());
        switch(true){
            case $h<=11 && $h>=0:;
                $start = date("Y-m-d 11:00:00",strtotime("-1 day"));
                $end =  date("Y-m-d 11:00:00",time());
                break;
            case $h>11 && $h<=23:
                $start = date("Y-m-d 11:00:00",time());
                $end =  date("Y-m-d 11:00:00",strtotime("+1 day"));
                break;
        }
        $u_id = $this->get_user_id($user);
        $resultwrite = $this->db->field("*")
            ->table("y_dyx_user_write")
            ->where(" u_id = {$u_id} AND create_time BETWEEN '{$start}' AND '{$end}'")
            ->find();
        $resultwrite['create_time'] = empty($resultwrite['create_time'])?"暂无数据":$resultwrite['create_time'];
        $this->getView()->assign(["user" => $user,"resultwrite"=>$resultwrite,"discount_id"=>$discount_id]);
    }

    public function xinbapingAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $h = date("H",time());
            switch(true){
                case $h<=11 && $h>=0:;
                    $start = date("Y-m-d 00:00:00",strtotime("-1 day"));
                    $end =  date("Y-m-d 11:59:59",time());
                    break;
                case $h>11 && $h<=23:
                    $start = date("Y-m-d 00:00:00",time());
                    $end =  date("Y-m-d 11:59:59",strtotime("+1 day"));
                    break;
            }
            $user_info = $this->db->field("*")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            $screen = $this->db->field("*")
                ->table("y_pa_screen")
                ->where("user_id = '{$user_info['id']}'")
                ->find();
            $user_result = $this->db->field("*")
                ->table("y_dyx_user_write2")
                ->where("u_id = {$user_info['id']} AND create_time BETWEEN '{$start}' AND '{$end}'")
                ->find();
            $user_result['create_time'] = empty($user_result['create_time'])?"暂无数据":$user_result['create_time'];
            if(!empty($screen)){
                $this->getView()->assign(["user" => $user,"status"=>$screen['status'],"user_result"=>$user_result,"discount_id"=>$discount_id]);
            }else{
                $this->getView()->assign(["user" => $user,"status"=>-1,"user_result"=>$user_result,"discount_id"=>$discount_id]);
            }
        }
    }
    //活期宝
    public function lifeAction(){
        Dispatcher::getInstance()->autoRender(false);
        $type = post('t');
        $dyx_num = post('d');
        $user = post('u');
        $yl = post('yl');
        $lifemodel = new lifeModel();
        if($type == 1){
            //存
            $result = $lifemodel->add_life_user($user,$dyx_num);
            echo $result;
        }else{
            //取
            $result = $lifemodel->edit_life_user($user,$dyx_num,$yl);
            echo $result;
        }
    }
    //月理财
    public function monthAction(){
        Dispatcher::getInstance()->autoRender(false);
        $type = post('t');
        $dyx_num = post('d');
        $user = post('u');
        $yl = post('yl');
        $lifemodel = new lifeModel();
        if($type == 1){
            //存
            $result = $lifemodel->add_month_user($user,$dyx_num);
            echo $result;
        }else{
            //取
            $result = $lifemodel->edit_month_user($user,$dyx_num,$yl);
            echo $result;
        }
    }
    //币闪购
    public function flashbuyAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $user_info = $this->db->field("id,usdt_address,usdt_num,usdt_available_balance,usdt_freeze_balance")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            $this->getView()->assign(["user" => $user,"user_info"=>$user_info,"discount_id"=>$discount_id]);
        }
    }

    public function dyx($price,$order,$address){
        include APP_PATH."/application/core/Pay.php";
        $pay = new \app\core\Pay();
        $res = $pay->withdraw(1,$order,$price,$address);
        if($res['code'] == 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function usdt($price,$order,$address){
        include APP_PATH."/application/core/Pay.php";
        $pay = new \app\core\Pay();
        $res = $pay->withdraw(2,$order,$price,$address);
        if($res['code'] == 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function qrcode($eth_addr){
        include APP_PATH."/vendor/phpqrcode/phpqrcode.php";
        $url = "/public/eth_addr/".$eth_addr.".png";
        $path = APP_PATH.$url;
        @chmod($path,0777);
        QRcode::png($eth_addr,$path,"L",6,1);
        return $url;
    }

    public function get_user_id($username){
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        return $user['id'];
    }

    public function emptyAction(){}


}