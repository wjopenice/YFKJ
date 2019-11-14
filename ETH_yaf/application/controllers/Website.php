<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;

class WebsiteController extends Controller_Abstract{
    const Login_Key = "BplVQpWfm5cjuKmbiKUvQqdErqhAp8vG";
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }
    //首页
    public function indexAction() {
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        //默认Action
        if(empty($this->sessions->user)) {
            $this->getView()->assign(["user" => "","discount_id"=>$discount_id]);
        }else {
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $usersys_discount = $activitymodel->get_sysuser_discount($user);
            $this->getView()->assign(["user" => $user,"usersys_discount"=>$usersys_discount,"discount_id"=>$discount_id]);
        }
    }
    //DYX公募
//    public function offeringAction() {//默认Action
//        $activitymodel = new activityModel();
//        $discount_id = $activitymodel->get_discount_id();
//        if(empty($this->sessions->user)) {
//            $this->getView()->assign(["user" => "","discount_id"=>$discount_id]);
//        }else {
//            $user = $this->sessions->user;
//            $this->getView()->assign(["user" => $user,"discount_id"=>$discount_id]);
//        }
//    }
    //DYX新群主公募
//    public function xinuserofferingAction() {//默认Action
//        if(empty($this->sessions->user)) {
//            success("请先登录", "/index/login");
//        }else{
//            $user = $this->sessions->user;
//            $activitymodel = new activityModel();
//            $user_usdt = $activitymodel->get_user_usdt($user);
//            /** 2019/9/7 gly修改*/
//            $user_info = $this->db->field("*")
//                ->table("y_user")
//                ->where("username = '{$user}'")
//                ->find();
//            $dyxdata = $this->db->field("*")
//                ->table("y_dyxdiscount")
//                ->find();
//            //$new_time = strtotime(date("2019-09-18 12:12:12"));
//            $new_time = strtotime(date("2019-09-21 20:30:00"));
//            $sql = "select sum(usdt_num) as num from y_offering WHERE user_id = {$user_info['id']} AND create_time > {$new_time}";
//            $result = $this->db->action($sql);
//            //获得个人固定额度
//            //获得个人固定额度
//            if($user=='15338864563'){
//                $user_fixed_amount = 2000;
//            }else{
//                $user_fixed_amount = 0;
//                switch($user_info['level']){
//                    case 1: $user_fixed_amount = $dyxdata['dxy_total_member'];break;
//                    case 2: $user_fixed_amount = $dyxdata['dxy_toal_lord'];break;
//                    case 3: $user_fixed_amount = $dyxdata['dyx_toal_new_lord'];break;
//                }
//            }
//            //获得个人已用额度
//            $user_used_amount = $result[0]['num'];
//            //获得个人可用额度
//            $user_available_amount = $user_fixed_amount - $user_used_amount;
//            if($user_available_amount <= 0){
//                $user_available_amount = 0;
//            }
//            //团队活动剩余总额度
//            $group_balance = $dyxdata['group_balance'];
//            //查看用户状态
//            $dyxgroupdata = $this->db->field("*")
//                ->table("y_dyx_group")
//                ->where("u_id = {$user_info['id']} AND create_time > {$new_time}")
//                ->find();
//            if(!empty($dyxgroupdata)){
//                $status = $dyxgroupdata['status'];
//            }else{
//                $status = -1;
//            }
//            $this->getView()->assign([
//                "user" => $user,
//                "user_usdt"=>$user_usdt,
//                "user_available_amount" => $user_available_amount,
//                "group_balance" => $group_balance,
//                "user_fixed_amount"=>$user_fixed_amount,
//                "status"=>$status
//            ]);
//        }
//    }
    //DYX公募
//    public function userofferingAction() {//默认Action
//        if(empty($this->sessions->user)) {
//            success("请先登录", "/index/login");
//        }else{
//            $user = $this->sessions->user;
//            $activitymodel = new activityModel();
//            $user_usdt = $activitymodel->get_user_usdt($user);
//            /** 2019/9/7 gly修改*/
//            $user_info = $this->db->field("*")
//                ->table("y_user")
//                ->where("username = '{$user}'")
//                ->find();
//            $dyxdata = $this->db->field("*")
//                ->table("y_dyxdiscount")
//                ->find();
//            $sql = "select sum(usdt_num) as num from y_offering WHERE user_id = {$user_info['id']}";
//            $result = $this->db->action($sql);
//            //获得个人固定额度
//            if($user=='15338864563'){
//                $user_fixed_amount = 2000;
//            }else{
//                if($user_info['level'] == 1){
//                    $user_fixed_amount = $dyxdata['dxy_total_member'];
//                }else{
//                    $user_fixed_amount = $dyxdata['dxy_toal_lord'];
//                }
//            }
//            //获得个人已用额度
//            $user_used_amount = $result[0]['num'];
//            //获得个人可用额度
//            $user_available_amount = $user_fixed_amount - $user_used_amount;
//            if($user_available_amount <= 0){
//                $user_available_amount = 0;
//            }
//            //团队活动剩余总额度
//            $group_balance = $dyxdata['group_balance'];
//            $this->getView()->assign([
//                "user" => $user,
//                "user_usdt"=>$user_usdt,
//                "user_available_amount" => $user_available_amount,
//                "group_balance" => $group_balance,
//                "user_fixed_amount"=>$user_fixed_amount
//            ]);
//        }
//    }
    //FOMOXYT
    public function fomoxytAction(){
        if(empty($this->sessions->user)) {
            $this->getView()->assign([
                "user" =>"",
                "userdata"=>0,
                "fomodata"=>[],
                "userfomo"=>[],
                "userext"=>[]
            ]);
        }else{
            $user = $this->sessions->user;
            $fomo = new fomoModel();
            $userdata = $fomo->get_user_info($user);
            $fomodata = $fomo->fomo;
            if($fomodata){
                $fomodata['award'] = $fomodata['award_btc']."BTC + ".$fomodata['award_rntp']."RNTP";
                $userext = $fomo->get_user_fomo_find($userdata['id']);
                $userfomo = $fomo->get_user_fomo();
                $this->getView()->assign([
                    "user" =>$user,
                    "userdata"=>$userdata,
                    "fomodata"=>$fomodata,
                    "userfomo"=>$userfomo,
                    "userext"=>$userext
                ]);
            }else{
                $this->getView()->assign([
                    "user" =>$user,
                    "userdata"=>$userdata,
                    "fomodata"=>[],
                    "userfomo"=>[],
                    "userext"=>[]
                ]);
            }
        }
    }

    public function fomodyxAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            $this->getView()->assign([
                "user" =>"",
                "userdata"=>0,
                "fomodata"=>[],
                "userfomo"=>[],
                "userext"=>[],
                "discount_id"=>$discount_id
            ]);
        }else{
            $user = $this->sessions->user;
            $fomo = new fomoModel();
            $userdata = $fomo->get_user_info($user);
            $fomodata = $fomo->fomo;
            $this->getView()->assign([
                "user" =>$user,
                "userdata"=>$userdata,
                "fomodata"=>[],
                "userfomo"=>[],
                "userext"=>[],
                "discount_id"=>$discount_id
            ]);
        }
    }
    //霸屏行动
    public function bpactiveAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        if(empty($this->sessions->user)) {
            $this->getView()->assign(["user" => "","dyx"=>0,"status"=>-1]);
        }else {
            $user = $this->sessions->user;
            $user_info = $this->db->field("id,token_available_balance")
                ->table("y_user")
                ->where("username = '{$user}'")
                ->find();
            if(!empty($user_info)){
                $pa = $this->db->field("status")
                    ->table("y_pa_screen")
                    ->where("user_id = {$user_info['id']}")
                    ->find();
                if(!empty($pa)){
                    $this->getView()->assign(["user" => $user,"dyx"=>$user_info['token_available_balance'],"status"=>(int)$pa['status'],"discount_id"=>$discount_id]);
                }else{
                    $this->getView()->assign(["user" => $user,"dyx"=>$user_info['token_available_balance'],"status"=>-1,"discount_id"=>$discount_id]);
                }
            }else{
                $this->getView()->assign(["user" => $user,"dyx"=>0,"status"=>-1,"discount_id"=>$discount_id]);
            }
        }
    }

    public function testAction(){
        phpinfo();
        exit;
    }

    public function emptyAction()
    {
        // TODO: Implement __call() method.
    }

    //dyx理财
    public function managemoneyAction(){
        $activitymodel = new activityModel();
        $discount_id = $activitymodel->get_discount_id();
        $this->getView()->assign(["xxx"=>"yyyy","discount_id"=>$discount_id]);
    }
}
