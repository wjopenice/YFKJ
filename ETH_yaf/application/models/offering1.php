<?php
use Yaf\Application;
class offeringModel{
    public $db;

    public function __construct()
    {
        $this->db = new dbModel();
    }

    public function insert($username,$data){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $user['id'];
        $banlance = $this->is_user_dyx($user,$data['input_usdt']);
        $this->db->beginTransaction();
        $offer['user_id'] = $user_id;
        $offer['usdt_num'] = $data['input_usdt'];
        $offer['dyx_num'] = $data['dyx'];
        //$offer['unlock_dyx_num'] = $data['dyx'] * 0.1;
        // 改成全部没有解锁
        $offer['unlock_dyx_num'] = 0;
        $offer['lock_dyx_num'] = bcsub($data['dyx'], $offer['unlock_dyx_num'], 3);
        $offer['create_time'] = time();
        $offer['unlock_time'] = 20;
        $bool1 = $this->db->action($this->db->insertSql("offering",$offer));
        $datanum['usdt_num'] = $user['usdt_num'] - $data['input_usdt'];
        $datanum['usdt_available_balance'] = $user['usdt_num'] - $data['input_usdt'];
        $datanum['token_num'] = $user['token_num'] + $offer['dyx_num'];
        $datanum['token_available_balance'] =  $user['token_available_balance'] + $offer['unlock_dyx_num'];
        $datanum['token_freeze_balance'] = $user['token_freeze_balance'] + $offer['lock_dyx_num'] ;
        $bool2 = $this->db->action($this->db->updateSql("user",$datanum,"username = '{$username}'"));
        file_put_contents(APP_PATH."/log/text.txt",$this->db->updateSql("user",$datanum,"username = '{$username}'"),FILE_APPEND);
        $log['user_id'] = $user_id;
        $log['type'] = "群主额度";
        $log['info'] = "成功兑换{$data['input_usdt']}个USDT，获得{$data['dyx']}个DXY";
        $log['create_time'] = time();
        $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
        $dyx['group_balance'] = $banlance;
        $bool4 = $this->db->action($this->db->updateSql("dyxdiscount",$dyx,"id = 1"));
        file_put_contents(APP_PATH."/log/text.txt",$this->db->updateSql("dyxdiscount",$dyx,"id = 1"),FILE_APPEND);
        if($bool1 && $bool2 && $bool3 && $bool4){
            $this->db->commit();
            return 1;
        }else{
            $this->db->rollback();
            return 0;
        }
    }

    public function get_user_offering($usernmae){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$usernmae}'")
            ->find();
        $user_id = $user['id'];
        $user_offering = $this->db->field("*")
            ->table("y_offering")
            ->where("user_id = '{$user_id}'")
            ->order("id desc")
            ->select();
        if(!empty($user_offering[0]['user_id'])){
            return $user_offering;
        }else{
            return [];
        }
    }

    public function is_user_dyx($user,$usdt){
        $dyxdata = $this->db->field("*")
            ->table("y_dyxdiscount")
            ->find();
//        $offering = $this->db->field("*")
//            ->table("y_offering")
//            ->where("user_id = {$user['id']}")
//            ->find();
        $new_time = strtotime(date("2019-09-18 12:12:12"));
        //$sql = "select sum(usdt_num) as num from y_offering WHERE user_id = {$user['id']}";
        $sql = "select sum(usdt_num) as num from y_offering WHERE user_id = {$user['id']} AND create_time > {$new_time}";
        $result = $this->db->action($sql);
        //获得个人固定额度
//        if($user['level'] == 1){
//            $user_fixed_amount = $dyxdata['dxy_total_member'];
//        }else{
//            $user_fixed_amount = $dyxdata['dxy_toal_lord'];
//        }
        switch($user['level']){
            case 1: $user_fixed_amount = $dyxdata['dxy_total_member'];break;
            case 2: $user_fixed_amount = $dyxdata['dxy_toal_lord'];break;
            case 3: $user_fixed_amount = $dyxdata['dyx_toal_new_lord'];break;
        }
        //获得个人已用额度
        $user_used_amount = $result[0]['num'];
        //获得个人可用额度
        $user_available_amount = $user_fixed_amount - $user_used_amount;
        //获得团体剩余额度
        $group_balance = $dyxdata['group_balance'];

        $groupbanlance = $group_balance - $usdt;
        if($groupbanlance <0 ){
            $message = ['code'=>2,"msg"=>"兑换失败,团体剩余额度不足"];
            echo json_encode($message);
            exit;
        }else{
            if($user_available_amount - $usdt < 0){
                $message = ['code'=>3,"msg"=>"兑换失败,个人可用额度不足"];
                echo json_encode($message);
                exit;
            }else{

                return $groupbanlance;
            }
        }
    }
    public function get_user_log($usernmae){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$usernmae}'")
            ->find();
        $user_id = $user['id'];
        $user_log = $this->db->field("*")
            ->table("y_user_num_log")
            ->where("user_id = '{$user_id}'")
			->order("id desc")
			->limit(0,10)
            ->select();
        if(!empty($user_log[0]['user_id'])){
            return $user_log;
        }else{
            return [];
        }
    }
}