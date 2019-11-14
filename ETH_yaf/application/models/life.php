<?php
use Yaf\Application;
class lifeModel{
    public $db;

    public function __construct()
    {
        $this->db = new dbModel();
    }

    public function get_life_user($user)
    {
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        if(!empty($userinfo['id'])){
            $userid = $userinfo['id'];
            $dyx_total = $this->get_life_total($userid);
            $tw_total = $this->get_life_tw_dyx($userid);
            $yl_total = $this->get_life_tw_yl($userid);
            $arr['total'] = $dyx_total + $yl_total;
            $arr['tx_dyx'] = $tw_total + $yl_total;
            $arr['yl_dyx'] =  $yl_total;
        }else{
            $arr['total'] = 0;
            $arr['tx_dyx'] = 0;
            $arr['yl_dyx'] =  0;
        }
        return $arr;
    }
    
    public function get_life_total($userid)
    {
        $monthinfo = $this->db->field("*")
            ->table("y_life_dyx")
            ->where("user_id = {$userid} AND status = 0")
            ->select();
        if($monthinfo){
            $total = 0;
            foreach($monthinfo as $k=>$v){
                $total = $v['start_dyx'] + $total;
            }
            return $total;
        }else{
            return  0;
        }
    }

    public function get_life_tw_dyx($userid)
    {
        $monthinfo = $this->db->field("*")
            ->table("y_life_dyx")
            ->where("user_id = {$userid} AND status = 0")
            ->select();
        if($monthinfo){
            $dyx = 0;
            foreach($monthinfo as $k=>$v){
                $dyx = $v['start_dyx'] + $dyx;
            }
            return $dyx;
        }else{
            return 0;
        }
    }

    public function get_life_tw_yl($userid)
    {
        $timex = time();
        $monthinfo = $this->db->field("*")
            ->table("y_life_dyx")
            ->where("user_id = {$userid} AND status = 0 AND end_time <= {$timex} ")
            ->select();
        if($monthinfo){
            $yl_dyx = 0;
            foreach($monthinfo as $k=>$v){
                $yl_dyx = $v['income_dyx'] + $yl_dyx;
            }
            return $yl_dyx;
        }else{
            return 0;
        }
    }

    public function add_life_user($user,$dyx)
    {
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $userid = $userinfo['id'];
        $data['life_id'] = NULL;
        $data['user_id'] = $userid;
        $data['income_dyx'] = 0;
        $data['start_time'] = time();
        $data['end_time'] = time()+3600*24;
        $data['start_dyx'] = $dyx;
        $data['status'] = 0;
        $this->db->beginTransaction();
        $bool1 = $this->db->action($this->db->insertSql("life_dyx",$data));
        $sql = "UPDATE y_user SET token_num = token_num - {$dyx},token_available_balance = token_available_balance - {$dyx} WHERE id = {$userid}";
        $bool2 = $this->db->action($sql);
        if($bool1 && $bool2){
            $this->db->commit();
            return "存入成功";
        }else{
            $this->db->rollback();
            return "存入失败";
        }
    }

    public function edit_life_user($user,$dyx,$yl)
    {
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $userid = $userinfo['id'];
        $lifeinfo = $this->db->field("*")
            ->table("y_life_dyx")
            ->where("user_id = {$userid} AND status = 0")
            ->order("start_time DESC")
            ->select();
        if(!empty($lifeinfo[0]['life_id'])){
            $this->db->beginTransaction();
            $over = $dyx;
            $bool1 = false;
            foreach($lifeinfo as $k=>$v){
                $num = $over - $v['start_dyx'];
                if($num > 0){
                    echo 1;
                    $x = $num - $v['income_dyx'];
                    if($x >= 0){
                        $data['income_dyx'] = 0;
                        $over = $num - $v['income_dyx'];
                        $data['status'] = 1;
                    }else{
                        $data['income_dyx'] = abs($x);
                        $over = 0;
                        $data['status'] = 0;
                    }
                    $data['start_dyx'] = 0;
                    $bool1 = $this->db->action($this->db->updateSql("life_dyx",$data,"life_id = {$v['life_id']}"));
                }else if($num == 0){
                    echo 2;
                    $data['start_dyx'] = 0;
                    $data['income_dyx'] = $v['income_dyx'];
                    $data['status'] = 0;
                    $bool1 = $this->db->action($this->db->updateSql("life_dyx",$data,"life_id = {$v['life_id']}"));
                    $over = 0;
                    break;
                }else{
                    echo 3;
                    $data['start_dyx'] = abs($num);
                    $data['income_dyx'] = $v['income_dyx'];
                    $data['status'] = 0;
                    $bool1 = $this->db->action($this->db->updateSql("life_dyx",$data,"life_id = {$v['life_id']}"));
                    $over = 0;
                    break;
                }
            }

            $sql = "UPDATE y_user SET token_num = token_num + {$dyx},token_available_balance = token_available_balance + {$dyx} WHERE id = {$userid}";
            $bool2 = $this->db->action($sql);
            $log['user_id'] = $userid;
            $log['type'] = "DYX理财";
            $log['info'] = "活期宝成功获得{$dyx}个DYX";
            $log['create_time'] = time();
            $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
            //分配提成
            $this->updateGroupx($userid,$yl);
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                return "取出成功";
            }else{
                $this->db->rollback();
                return "取出失败";
            }
        }else{
            return "余额不足";
        }
    }

    public function get_month_dyx($user)
    {
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $userid = $userinfo['id'];
        $monthinfo = $this->db->field("*")
            ->table("y_month_dyx")
            ->where("user_id = {$userid} AND status = 0")
            ->order("start_time DESC")
            ->select();
        if($monthinfo){
            return $monthinfo;
        }else{
            return [];
        }
    }

    public function get_month_user($user){
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        if(!empty($userinfo['id'])){
            $userid = $userinfo['id'];
            $dyx_total = $this->get_month_total($userid);
            $tw_total = $this->get_month_tw_dyx($userid);
            $arr['total'] = $dyx_total + $tw_total['yl_dyx'];
            $arr['tx_dyx'] = $tw_total['tx_dyx'] + $tw_total['yl_dyx'];
            $arr['yl_dyx'] =  $tw_total['yl_dyx'];
        }else{
            $arr['total'] = 0;
            $arr['tx_dyx'] = 0;
            $arr['yl_dyx'] =  0;
        }
        return $arr;
    }

    public function get_month_total($userid)
    {
        $monthinfo = $this->db->field("*")
            ->table("y_month_dyx")
            ->where("user_id = {$userid} AND status = 0")
            ->select();
        if($monthinfo){
            $total = 0;
            foreach($monthinfo as $k=>$v){
                $total = $v['start_dyx'] + $total;
            }
            return $total;
        }else{
            return  0;
        }
    }

    public function get_month_tw_dyx($userid)
    {
        $timex = time();
        $monthinfo = $this->db->field("*")
            ->table("y_month_dyx")
            ->where("user_id = {$userid} AND status = 0 AND unlock_time <= {$timex} ")
            ->select();
        $arrData = [];
        if($monthinfo){
            $dyx = 0;
            $yl_dyx = 0;
            foreach($monthinfo as $k=>$v){
                $dyx = $v['start_dyx'] + $dyx;
                $yl_dyx = $v['income_dyx'] + $yl_dyx;
            }
            $arrData['tx_dyx'] = $dyx;
            $arrData['yl_dyx'] = $yl_dyx;
            return $arrData;
        }else{
            $arrData['tx_dyx'] = 0;
            $arrData['yl_dyx'] = 0;
            return $arrData;
        }
    }
    
    public function add_month_user($user,$dyx)
    {
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $userid = $userinfo['id'];
        $data['month_id'] = NULL;
        $data['user_id'] = $userid;
        $data['income_dyx'] = 0;
        $data['start_time'] = time();
        $data['end_time'] = 0;
        $data['unlock_time'] = strtotime(date('Y-m-d H:i:s',strtotime('+1 month')));
        $data['income_dyx'] = 0;
        $data['start_dyx'] = $dyx;
        $data['end_dyx'] = 0;
        $data['status'] = 0;
        $this->db->beginTransaction();
        $bool1 = $this->db->action($this->db->insertSql("month_dyx",$data));
        $sql = "UPDATE y_user SET token_num = token_num - {$dyx},token_available_balance = token_available_balance - {$dyx} WHERE id = {$userid}";
        $bool2 = $this->db->action($sql);
        if($bool1 && $bool2){
            $this->db->commit();
            return "存入成功";
        }else{
            $this->db->rollback();
            return "存入失败";
        }
    }

    public function edit_month_user($user,$dyx,$yl)
    {
        $timex = time();
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $userid = $userinfo['id'];
        $monthinfo = $this->db->field("*")
            ->table("y_month_dyx")
            ->where("user_id = {$userid} AND status = 0 AND unlock_time <= {$timex}")
            ->select();
        if(!empty($monthinfo[0]['month_id'])){
            $this->db->beginTransaction();
            $over = $dyx;
            $bool1 = false;
            foreach($monthinfo as $k=>$v){
                $num = $over - $v['start_dyx'];
                if($num > 0){
                    $x = $num - $v['income_dyx'];
                    if($x >= 0){
                        $data['income_dyx'] = 0;
                        $data['status'] = 1;
                        $over = $num - $v['income_dyx'];
                    }else{
                        $data['income_dyx'] = abs($x);
                        $data['status'] = 0;
                        $over = 0;
                    }
                    $data['start_dyx'] = 0;
                    $data['end_dyx'] = $v['start_dyx'];
                    $data['end_time'] = time();
                    $bool1 = $this->db->action($this->db->updateSql("month_dyx",$data,"month_id = {$v['month_id']}"));
                }else if($num == 0){
                    $data['start_dyx'] = 0;
                    $data['end_dyx'] = $v['start_dyx'];
                    $data['end_time'] = time();
                    $data['income_dyx'] = $v['income_dyx'];
                    $data['status'] = 0;
                    $bool1 = $this->db->action($this->db->updateSql("month_dyx",$data,"month_id = {$v['month_id']}"));
                    $over = 0;
                    break;
                }else{
                    $data['start_dyx'] = abs($num);
                    $data['end_dyx'] = $v['start_dyx']-abs($num);
                    $data['end_time'] = time();
                    $data['income_dyx'] = $v['income_dyx'];
                    $data['status'] = 0;
                    $bool1 = $this->db->action($this->db->updateSql("month_dyx",$data,"month_id = {$v['month_id']}"));
                    $over = 0;
                    break;
                }
            }
            $sql = "UPDATE y_user SET token_num = token_num + {$dyx},token_available_balance = token_available_balance + {$dyx} WHERE id = {$userid}";
            $bool2 = $this->db->action($sql);
            $log['user_id'] = $userid;
            $log['type'] = "DYX理财";
            $log['info'] = "月理财成功获得{$dyx}个DYX";
            $log['create_time'] = time();
            $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
            //分配提成
            $this->updateGroupx($userid,$yl);
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                return "取出成功";
            }else{
                $this->db->rollback();
                return "取出失败";
            }
        }else{
            return "余额不足";
        }
    }

    public function updateGroupx($uid,$yl,$level = 1)
    {
        /** 超过三级不做处理 */
        if ($level > 3){
            return true;
        }
        /** 获取上级直推用户 */;
        $user = $this->db->field("id,tel,floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $price = 0;
        switch ($level){
            case 1:$price = $yl*0.3;break;
            case 2:$price = $yl*0.2;break;
            case 3:$price = $yl*0.1;break;
        }
        //修改用户dyx信息
        $sql = "UPDATE y_user SET token_num = token_num + {$price},token_available_balance = token_available_balance + {$price} WHERE id = {$user['floor_id']}";
        $this->db->action($sql);
        //记录日志
        $log['user_id'] = $user['floor_id'];
        $log['type'] = "DYX理财";
        $log['info'] = "邀请好友，获得{$price}个DXY";
        $log['create_time'] = time();
        $this->db->action($this->db->insertSql("user_num_log",$log));
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $puid = $user['floor_id'];
        if (empty($puid) || $puid == 0){
            return true;
        }
        $this->updateGroupx($puid,$yl,$level+1);
    }

    public function put_life_yl($user){
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();

        if($userinfo){
            $timex = time();
            $lifeinfo = $this->db->field("*")
                ->table("y_life_dyx")
                ->where("user_id = {$userinfo['id']} AND status = 0 AND start_dyx <> 0 AND end_time <= {$timex} ")
                ->select();
            if($lifeinfo[0]['life_id']){
                foreach($lifeinfo as $k=>$v){
                    $times = $v['end_time'];
                    $timey =  $timex - $times;
                    $second = floor($timey/(60*60*24))+1;
                    $count = $this->db->zscount("life_dyx_log","*","total","yl_id = {$userinfo['id']}");
                    $x = $second - $count;
                    if($x > 0){
                        $this->db->beginTransaction();
                        $y = ($v['start_dyx']*0.005)*$x;
                        $sql = "UPDATE y_life_dyx SET income_dyx = income_dyx + {$y} WHERE life_id = {$v['life_id']}";
                        $bool1 = $this->db->action($sql);
                        $bool2 = false;
                        for($i=0;$i<$x;$i++){
                            $data['id'] = NULL;
                            $data['yl_id'] = $userinfo['id'];
                            $data['yl_price'] = $v['start_dyx']*0.005;
                            $bool2 = $this->db->action($this->db->insertSql("life_dyx_log",$data));
                        }
                        if($bool1 && $bool2){
                            $this->db->commit();
                            return "利息更新成功";
                        }else{
                            $this->db->rollback();
                            return "利息更新失败";
                        }
                    }else{
                        return "无利息更新";
                    }
                }
            }else{
                //无可调节金额
                return "利息数据已是最新";
            }
        }else{
            //无账号
            return "无账号数据";
        }
    }
}