<?php
use Yaf\Application;
class activityModel{
    public $db;

    public function __construct()
    {
        $this->db = new dbModel();
    }

    public function get_sysuser_discount($username){
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $discount = $this->db->field("id,discount_num,lock_exchange_price,lock85_price,unlock_price,success_price,system_price,income,status")
            ->table("y_sys_discount")
            ->order("id desc")
            ->select();
        foreach($discount as $k=>$v){
            $res = $this->db->field("dis_price,link_price")
                ->table("y_user_discount")
                ->where("dis_id = {$discount[$k]['id']} and u_id = {$user['id']}")
                ->order("id desc")
                ->find();
            $discount[$k]['dis_price'] = empty($res['dis_price'])?0:$res['dis_price'];
            $discount[$k]['dis_amount'] = 0;
           // $discount[$k]['dis_system_price'] = 0;
           // $discount[$k]['dis_income'] = "7.06%";
            $discount[$k]['link_price'] = empty($res['link_price'])?0:$res['link_price'];
        }
        return $discount;
    }

    public function unlogin_sysuser_discount(){
        $discount = $this->db->field("id,discount_num,lock_exchange_price,lock85_price,unlock_price,success_price,status")
            ->table("y_sys_discount")
            ->order("id desc")
            ->select();
        foreach($discount as $k=>$v){
            $discount[$k]['dis_price'] = "暂无数据";
            $discount[$k]['dis_amount'] = "暂无数据";
            $discount[$k]['dis_system_price'] = "暂无数据";
            $discount[$k]['dis_income'] = "0%";
        }
        return $discount;
    }

    public function get_discount_id(){
        $discount = $this->db->field("id")
            ->table("y_sys_discount")
            ->where("status = 0")
            ->order()
            ->find();
        return $discount['id'];
    }

    public function get_discount_data(){
        $result = $this->db->field("*")
            ->table("y_sys_discount")
            ->where("status = 1")
            ->limit(0,3)
            ->select();
        return $result;
    }

    public function get_user_discount_total_price($id){
        $price = $this->db->field("(sum(dis_price) +sum(link_price)) as total")
            ->table("y_user_discount")
            ->where("dis_id = {$id}")
            ->select();
        if(!empty($price)){
            return $price[0]['total'];
        }else{
            return 0;
        }
    }

    public function get_user_discount($id,$username){
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $result = $this->db->field("*")
            ->table("y_user_discount")
            ->where("dis_id = {$id} and u_id ={$user}")
            ->find();
        return $result;
    }

    public function get_user($username){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($user)){
            return $user;
        }else{
            return [];
        }
    }

    public function get_floor_id($username){
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($user)){
            return $user['floor_id'];
        }else{
            return NULL;
        }
    }

    public function get_user_id($username){
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($user)){
            return $user['id'];
        }else{
            return NULL;
        }
    }

    public function get_user_usdt($username){
        $token = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($token)){
            return $token['usdt_num'];
        }else{
            return 0;
        }
    }

    public function get_team_usdt($username){
        $floor = $this->db->field("id,usdt_num")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $total = 0;
        if(!empty($floor)){
            $one = $this->db->field("id,sum(usdt_num) as one_total")
                ->table("y_user")
                ->where("floor_id = {$floor['id']}")
                ->select();
            if(!empty($one[0]['id'])){
               $two_res = 0;
               foreach($one as $k=>$v){
                   $two = $this->db->field("id,sum(usdt_num) as two_total")
                       ->table("y_user")
                       ->where("floor_id = {$one[$k]['id']}")
                       ->select();
                   $two_res += $two[0]['two_total'];
               }
               $total = $one[0]['one_total'] + $floor['usdt_num'] + $two_res;
            }else{
               $total = $one[0]['one_total'] + $floor['usdt_num'];
            }
        }else{
            $total = 0;
        }
        return $total;
    }

    public function get_invite_num($dis_id,$username){
        $floor = $this->db->field("id,usdt_num")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user = $this->db->field("id,usdt_num")
            ->table("y_user")
            ->where("floor_id = {$floor['id']}")
            ->select();
        $num = 0;
        if(!empty($user)){
            $res_num = 0;
            foreach($user as $k=>$v){
                $res = $this->db->field("id")
                    ->table("y_user_discount")
                    ->where("u_id = {$user[$k]['id']} and dis_id = {$dis_id}")
                    ->find();
                if(!empty($res)){
                    $res_num += 1;
                }
            }
            $num = $res_num;
        }else{
            $num = 0;
        }
        return $num;
    }

    public function get_news_user_order($username){
        $result = $this->db->field("*")
            ->table("y_user")
            ->order("token_available_balance desc")
            ->select();
        $data = [];
        foreach($result as $k=>$v){
            if($v['username'] == $username){
                $data['order'] = $k+1;
                $data['dyx'] = $v['token_available_balance'];
                break;
            }
        }
        if(!empty($data)){
            return $data;
        }else{
            $data['order'] = "暂无数据";
            $data['dyx'] = "暂无数据";
            return $data;
        }
    }

    public function get_news_user_group($username){
        $result = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $result['id'];
        $sql = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
        $topRes = $this->db->action($sql);
        $data = [];
        foreach($topRes as $k1=>$v1){
            if($user_id == $v1['guid']){
                $data['level'] = $k1 + 1;
                $data['money_total'] = $v1['money_total'];
                break;
            }
        }
        if(!empty($data)){
            return $data;
        }else{
            $data['level'] = "暂无数据";
            $data['money_total'] = "暂无数据";
            return $data;
        }
    }

    public function get_user_order($username){
        $result = $this->db->field("*")
            ->table("y_user")
            ->order("usdt_num desc")
            ->select();
        $order = 0;
        foreach($result as $k=>$v){
           if($v['username'] == $username){
               $order = $k;
               break;
           }
        }
        return $order+1;
    }

    public function get_user_data($username){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $xyt = $this->db->zssum("pay_wallet_order","xyt_number","num","xcoinpay_userid = {$user['id']} and status = 3");
        $user['xyt_num'] = $xyt;
        $user['xyt_available_balance'] = $xyt;
        return $user;
    }

    public function get_curl_usdt($username){
        $token = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        if(!empty($token)){
            set_time_limit(0);
            $exchange_addr = "0xe4412afb082b51b185acf2b421842465eac96103";
            $token_addr = $token['token_address'];
            $url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=$exchange_addr&address=$token_addr&tag=latest&apikey=XYT";
            $res = curl_get($url);
            if($res->message == 'OK'){
                $this->db->action($this->db->updateSql("user",['usdt_num'=>$res->result],"username = '{$username}'"));
                return $res->result;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    public function get_youinvutation($username,$disid,$leavel){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $user['id'];
        $sql = "";
        if($leavel == 1){
            $sql = "select id,tel,create_time from y_user where floor_id={$user_id}";
        }else if($leavel == 2){
            $sql = "select id,tel,create_time from y_user where floor_id in (select id from y_user where floor_id = {$user_id})";
        }else if($leavel == 3){
            $sql = "select id,tel,create_time from y_user where floor_id in (select id from y_user where floor_id in (select id from y_user where floor_id = {$user_id}))";
        }
        $userone = $this->db->action($sql);
        if(!empty($userone)){
            $newData = [];
            foreach($userone as $key=>$value){
                $newData[$key]["tel"] = $value['tel'];
                $newData[$key]["create_time"] = date("Y-m-d H:i:s",$value['create_time']);
                $discount = $this->db->field("dis_price,link_price")
                    ->table("y_user_discount")
                    ->where("u_id = {$value['id']} and dis_id = {$disid}")
                    ->find();
                $newData[$key]["contribution_income"] = $discount['dis_price'] + $discount['link_price'];
            }
            $oneData['user'] = $userone;
            $oneData['data'] = $newData;
            $oneData['num'] = count($userone);
        }else{
            $oneData['user'] = [];
            $oneData['data'] = [];
            $oneData['num'] = 0;
        }
        return $oneData;
    }

    public function get_youinvitation_one($username,$disid){
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $user['id'];
        //查询一级邀请好友
        $userone = $this->db->field("id,tel,create_time")
            ->table("y_user")
            ->where("floor_id = {$user_id}")
            ->select();
        if(!empty($userone)){
            $newData = [];
            foreach($userone as $key=>$value){
                $newData[$key]["tel"] = $value['tel'];
                $newData[$key]["create_time"] = date("Y-m-d H:i:s",$value['create_time']);
                $discount = $this->db->field("dis_price,link_price")
                    ->table("y_user_discount")
                    ->where("u_id = {$value['id']} and dis_id = {$disid}")
                    ->find();
                $newData[$key]["contribution_income"] = $discount['dis_price'] + $discount['link_price'];
            }
            $oneData['user'] = $userone;
            $oneData['data'] = $newData;
            $oneData['num'] = count($userone);
        }else{
            $oneData['user'] = [];
            $oneData['data'] = [];
            $oneData['num'] = 0;
        }
        return $oneData;
    }

    public function get_youinvitation_two($usernamearr,$disid){
        $newuser = [];
        foreach($usernamearr as $k1=>$v1){
            //查询二级邀请好友
            $usertwo = $this->db->field("id,tel,create_time")
                ->table("y_user")
                ->where("floor_id = {$v1['id']}")
                ->select();
            if(!empty($usertwo)){
                $newuser[] = $usertwo;
            }
        }
        $arr = [];
        foreach($newuser as $k=>$v){
            foreach($v as $k1=>$v1){
                $arr[] = $v1;
            }
        }
        if(!empty($arr)){
            $newData2 = [];
            foreach($arr as $k3=>$v3){
                $newData2[$k3]["tel"] = $v3['tel'];
                $newData2[$k3]["create_time"] = date("Y-m-d H:i:s",$v3['create_time']);
                $discount = $this->db->field("dis_price,link_price")
                    ->table("y_user_discount")
                    ->where("u_id = {$v3['id']} and dis_id = {$disid}")
                    ->find();
                $newData[$k3]["contribution_income"] = $discount['dis_price'] + $discount['link_price'];
            }
            $oneData['user'] = $arr;
            $oneData['data'] = $newData2;
            $oneData['num'] = count($arr);
        }else{
            $oneData['user'] = [];
            $oneData['data'] = [];
            $oneData['num'] = 0;
        }
        return $oneData;
    }

    public function get_group_num($username)
    {
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $user['id'];
        $sql = "SELECT guid, sum(usdt_num) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
        $topRes = $this->db->action($sql);
        $num = 0;
        foreach ($topRes as $k=>$v){
             if($v['guid'] == $user_id){
                 $num = $k+1;
             }
        }
        return $num;
    }

    public function get_inviteid_num($username)
    {
        $user = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $user_id = $user['id'];
        $sql = "
        SELECT inviteid, sum(dis_price+link_price) as money_total
        FROM `y_inviteid` AS a 
        LEFT JOIN `y_user` AS b
        ON  FIND_IN_SET(b.id, a.users) 
        LEFT JOIN `y_user_discount` AS d
        ON  FIND_IN_SET(b.id, d.u_id) 
        GROUP BY inviteid 
        ORDER BY money_total DESC";
        $topRes = $this->db->action($sql);
        $num = 0;
        foreach ($topRes as $k=>$v){
            if($v['guid'] == $user_id){
                $num = $k+1;
            }
        }
        return $num;
    }

    public function get_user_total_level($u_l,$g_l){
        $bulidranking = $this->db->field("*")
            ->table("y_bulidranking")
            ->find();
        $user_num = $this->db->zscount("user","id","total","new_status = 1");
        //$group_num = $this->db->zscount("group","id");
        $group_num = $user_num;
        $user_leval = ($u_l/sums($user_num))*$bulidranking['personal'];
        $group_leval = ($g_l/sums($group_num))*$bulidranking['team'];
        $total = ($user_leval+$group_leval)*100;
        if($total == 0){
            $total = "暂无数据";
        }else{
            $total = round($total,2)."%";
        }
        return $total;
    }
}