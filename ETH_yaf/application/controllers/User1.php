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
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $usersys_discount = $activitymodel->get_sysuser_discount($user);
//            $oneData = $activitymodel->get_youinvitation_one($user,$usersys_discount[0]['discount_num']);
//            $twoData = $activitymodel->get_youinvitation_two($oneData['user'],$usersys_discount[0]['discount_num']);
//            $threeData = $activitymodel->get_youinvitation_two($twoData['user'],$usersys_discount[0]['discount_num']);
            $oneData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],1);
            $twoData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],2);
            $threeData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],3);
            $userdata = $activitymodel->get_user_data($user);
            $this->getView()->assign([
                "user" => $user,
                "oneData" => $oneData,
                "twoData" => $twoData,
                "threeData" => $threeData,
                "userdata" => $userdata
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
            $this->getView()->assign([
                "user"=>$user,
                "userdata"=>$userdata,
                "qrcode"=>$qrcode,
                "offering_data"=>$offering_data,
                "uselog_data"=>$uselog_data
            ]);
        }
    }
    //交易记录
    public function transactionAction(){
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $this->getView()->assign(["user" => $user]);
        }
    }
    //老用户钱包与理财信息
    public function olduserAction(){
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
                $this->getView()->assign(["user" => $user,"userinfo"=>$userinfo,"shop"=>$shop]);
            }else{
                $this->getView()->assign(["user" => $user,"userinfo"=>$userinfo,"shop"=>[]]);
            }
        }
    }
    //用户管理
    public function useradAction(){
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
                $this->getView()->assign(["user" => $user, "userdata" => $userdata]);
            }
        }
    }
    //霸屏行动
    public function bapingAction(){
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            $user_info = $this->db->field("*")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            $screen = $this->db->field("*")
                ->table("y_pa_screen")
                ->where("user_id = '{$user_info['id']}'")
                ->find();
            $time = date("Y-m-d",time());
            $result = $this->db->field("*")
                ->table("y_dyx_write")
                ->where("create_time = '{$time}'")
                ->find();
            $user_result = $this->db->field("*")
                ->table("y_dyx_user_write")
                ->where("create_time = '{$time}' AND u_id = {$user_info['id']}")
                ->find();
            if(!empty($screen)){
                $this->getView()->assign(["user" => $user,"status"=>$screen['status'],"result"=>$result,"user_result"=>$user_result]);
            }else{
                $this->getView()->assign(["user" => $user,"status"=>-1,"result"=>$result,"user_result"=>$user_result]);
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
        $data['u_id'] = $user['id'];
        $data['token_num'] = $price;
        $data['currency'] = 1;
        $data['create_time'] = time();
        $data['order_no'] = uniqid();
        $data['address'] = post("d");
        $data['status'] = 0;
        if($price > 100){
            $data['auto_address'] = 1;
            $data['build_status'] = 1;
        }else{
            $stat = $this->dyx($price,$data['order_no'],$data['address']);
            $data['build_status'] = $stat;
            $data['auto_address'] = 0;
        }
        $bool = $this->db->action($this->db->insertSql("withdraw",$data));
        if($bool){
            $message = ["message"=>"申请成功"];
        }else{
            $message = ["message"=>"申请失败"];
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
        $data['u_id'] = $user['id'];
        $data['token_num'] = $price;
        $data['currency'] = 2;
        $data['create_time'] = time();
        $data['order_no'] = uniqid();
        $data['address'] = post("d");
        $data['status'] = 0;
        if($price > 100){
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
        echo json_encode($message);
        exit;
    }
    //任务记录
    public function taskrecordAction(){
        $user = $this->sessions->user;
        $u_id = $this->get_user_id($user);
        $time = date("Y-m-d",time());
        $resultwrite = $this->db->field("*")
            ->table("y_dyx_user_write")
            ->where("create_time = '{$time}' AND u_id = {$u_id}")
            ->find();
        $resultwrite['create_time'] = empty($resultwrite['create_time'])?"暂无数据":$resultwrite['create_time'];
        $this->getView()->assign(["user" => $user,"resultwrite"=>$resultwrite]);
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