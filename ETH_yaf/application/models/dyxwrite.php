<?php
use Yaf\Application;
class dyxwriteModel
{
    public $db;
    public function __construct()
    {
        $this->db = new dbModel();
    }

    public function edituserdyx($id,$type,$orderid){
        $str = "";$dyxtype = "";
        if($type == 'pic1'){
            $str = 'status1 = 1';
            $dyxtype = "一";
        }else{
            $str = 'status2 = 1';
            $dyxtype = "二";
        }
        $this->db->beginTransaction();
        //修改dyx表信息
        $sql = "UPDATE y_dyx_user_write SET income = income + 62.5,{$str} WHERE u_id = {$id} AND id = {$orderid}";
        $bool1 = $this->db->action($sql);
        //修改用户dyx信息
        $sql = "UPDATE y_user SET token_num = token_num + 62.5,token_available_balance = token_available_balance + 62.5 WHERE id = {$id}";
        $bool2 = $this->db->action($sql);
        //记录日志
        $log['user_id'] = $id;
        $log['type'] = "DYX霸屏行动";
        $log['info'] = "完成每日第{$dyxtype}次任务，获得62.5个DXY";
        $log['create_time'] = time();
        $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
        //统计上级用户
        $this->updateGroup($id);
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            return 1;
        }else{
            $this->db->rollback();
            return 0;
        }
    }

    public function updateGroup($uid, $level = 1)
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
        if (empty($user) && $level == 1){
            return true;
        }else{
            $price = 0;
            switch ($level){
                case 1:$price = 31.5;break;
                case 2:$price = 10.5;break;
                case 3:$price = 5.5;break;
            }
//            if($level == 1){
//               $price = 0.5;
//            }else{
//               $price = 0.25;
//            }
            //修改用户dyx信息
            $sql = "UPDATE y_user SET token_num = token_num + {$price},token_available_balance = token_available_balance + {$price} WHERE id = {$user['floor_id']}";
            $this->db->action($sql);
            //记录日志
            $log['user_id'] = $user['floor_id'];
            $log['type'] = "DYX霸屏行动";
            $log['info'] = "邀请好友完成每日任务1次，获得{$price}个DXY";
            $log['create_time'] = time();
            $this->db->action($this->db->insertSql("user_num_log",$log));
        }
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $puid = $user['floor_id'];
        if (empty($puid) || $puid == 0){
            return true;
        }
        $this->updateGroup($puid, $level+1);
    }

    public function edituserdyx2($id,$orderid){
        $this->db->beginTransaction();
        //修改dyx表信息
        $sql = "UPDATE y_dyx_user_write2 SET income = income + 125,status = 1 WHERE u_id = {$id} AND id = {$orderid}";
        $bool1 = $this->db->action($sql);
        //修改用户dyx信息
        $sql = "UPDATE y_user SET token_num = token_num + 125,token_available_balance = token_available_balance + 125 WHERE id = {$id}";
        $bool2 = $this->db->action($sql);
        //记录日志
        $log['user_id'] = $id;
        $log['type'] = "DYX霸屏行动";
        $log['info'] = "完成每日任务，获得125个DXY";
        $log['create_time'] = time();
        $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
        //统计上级用户
        $this->updateGroup2($id);
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            return 1;
        }else{
            $this->db->rollback();
            return 0;
        }
    }

    public function updateGroup2($uid, $level = 1)
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
        if (empty($user) && $level == 1){
            return true;
        }else{
            $price = 0;
            switch ($level){
                case 1:$price = 63;break;
                case 2:$price = 21;break;
                case 3:$price = 11;break;
            }
//            if($level == 1){
//               $price = 0.5;
//            }else{
//               $price = 0.25;
//            }
            //修改用户dyx信息
            $sql = "UPDATE y_user SET token_num = token_num + {$price},token_available_balance = token_available_balance + {$price} WHERE id = {$user['floor_id']}";
            $this->db->action($sql);
            //记录日志
            $log['user_id'] = $user['floor_id'];
            $log['type'] = "DYX霸屏行动";
            $log['info'] = "邀请好友完成每日任务，获得{$price}个DXY";
            $log['create_time'] = time();
            $this->db->action($this->db->insertSql("user_num_log",$log));
        }
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $puid = $user['floor_id'];
        if (empty($puid) || $puid == 0){
            return true;
        }
        $this->updateGroup2($puid, $level+1);
    }
}