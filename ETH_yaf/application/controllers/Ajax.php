<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class AjaxController extends Controller_Abstract{
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }
    
    public function ajax_return($code,$msg){
        echo json_encode(["code"=>$code,"message"=>$msg]);
        exit;
    }
    
    public function dyxpauserAction(){
        $user_id = post("id");
        $type = post("t");
        if($type == "ok"){
            $status = 1;
        }else{
            $status = 2;
        }
        $bool = $this->db->action($this->db->updateSql('pa_screen',['status'=>$status],"user_id = {$user_id}"));
        if($bool){
            $this->ajax_return(1,"操作通过");
        }else{
            $this->ajax_return(0,"操作失败");
        }
    }

    public function ajaxsenddelAction(){
        $btype = post("t");
        $od = post("od");
        $data['build_status'] = 0;
        $data['status'] = 0;
        $orderinfo = $this->db->field("*")
            ->table("y_withdraw")
            ->where("order_no = '{$od}'")
            ->find();
        if(!empty($orderinfo)){
            $where = "";
            switch ($btype){
                case 1:$type = "DYX";$where = "token_freeze_balance = token_freeze_balance + {$orderinfo['token_num']},token_available_balance = token_available_balance - {$orderinfo['token_num']}";break;
                case 2:$type = "USDT";$where = "usdt_freeze_balance = usdt_freeze_balance + {$orderinfo['token_num']},usdt_available_balance = usdt_available_balance - {$orderinfo['token_num']}";break;
                case 3:$type = "XYT";$where = "xyt_freeze_balance = xyt_freeze_balance + {$orderinfo['token_num']},xyt_available_balance = xyt_available_balance - {$orderinfo['token_num']}";break;
                case 4:$type = "BTC";$where = "btc_num = btc_num + {$orderinfo['token_num']}";break;
                case 5:$type = "分红股";break;
                default:$type = "币种不存在";break;
            }
            $sql = "UPDATE y_user SET {$where} WHERE id = {$orderinfo['u_id']}";
            $this->db->beginTransaction();
            $bool1 = $this->db->action($this->db->updateSql("withdraw",$data,"order_no = '{$od}'"));
            $bool2 = $this->db->action($sql);
            if($bool1 && $bool2){
                $this->db->commit();
                $message = "操作成功";
            }else{
                $this->db->rollback();
                $message = "操作失败,系统繁忙";
            }
            echo json_encode(["message"=>$message]);
            exit;
        }else{
            echo json_encode(["message"=>"订单不存在"]);
            exit;
        }
    }

    public function ajaxodsendAction(){
        $type = post("t");
        $order_no = post("od");
        $orderinfo = $this->db->field("*")
            ->table("y_withdraw")
            ->where("order_no = '{$order_no}'")
            ->find();
        $this->db->action($this->db->updateSql("withdraw",['status'=>3],"order_no = '{$order_no}'"));
        if(!empty($orderinfo)){
            if($type == "USDT"){
                $res = $this->usdt($orderinfo['token_num'],$order_no,$orderinfo['address']);
                echo json_encode(["message"=>$res]);
            }else if($type == "DYX"){
                $res = $this->dyx($orderinfo['token_num'],$order_no,$orderinfo['address']);
                echo json_encode(["message"=>$res]);
            }else{
                echo json_encode(["message"=>"币种不存在"]);
            }
        }else{
            echo json_encode(["message"=>"订单不存在"]);
        };
        exit;
    }

    public function ajaxpascreenAction(){
        $user = post("user");
        $user_info = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        if(!empty($user_info)){
            $data['user_id'] = $user_info['id'];
            $data['status'] = 1;
            $data['create_time'] = time();
            $data['usdt_num'] = 0;
            $bool = $this->db->action($this->db->insertSql('pa_screen',$data));
            if($bool){
                echo json_encode(['code'=>1,"message"=>"申请成功"]);
            }else{
                echo json_encode(['code'=>0,"message"=>"申请失败"]);
            }
        }else{
            echo json_encode(['code'=>-1,"message"=>"请登录"]);
        }
        exit;
    }

    public function ajaxuserofferingAction(){

        $data['usdt'] = post("usdt");
        $data['input_usdt'] = post("input_usdt");
        $data['dyx'] = post("dyx");
        $user = post("user");
        $offering = new offeringModel();
        $bool = $offering->insert($user,$data);
        if($bool == 1){
            $message = ['code'=>1,"msg"=>"兑换成功！请到个人资金中查看"];

        }else{
            $message = ['code'=>0,"msg"=>"兑换失败"];
        }
        echo json_encode($message);
        exit;
    }

    public function ajaxuseroffering2Action(){

        $data['usdt'] = post("usdt");
        $data['input_usdt'] = post("input_usdt");
        $data['dyx'] = post("dyx");
        $user = post("user");
        $offering = new offeringModel();
        if($user=='15338864563'){
            $bool = $offering->insert3($user,$data);
        }else{
            $bool = $offering->insert2($user,$data);
        }
        if($bool == 1){
            $message = ['code'=>1,"msg"=>"兑换成功！请到个人资金中查看"];

        }else{
            $message = ['code'=>0,"msg"=>"兑换失败"];
        }
        echo json_encode($message);
        exit;
    }

    public function dyx($price,$order,$address){
        include APP_PATH."/application/core/Pay.php";
        $pay = new \app\core\Pay();
        $res = $pay->withdraw(1,$order,$price,$address);
        if($res['code'] == 0){
            return "操作成功";
        }else{
            return "操作失败";
        }
    }

    public function usdt($price,$order,$address){
        include APP_PATH."/application/core/Pay.php";
        $pay = new \app\core\Pay();
        $res = $pay->withdraw(2,$order,$price,$address);
        if($res['code'] == 0){
            return "操作成功";
        }else{
            return "操作失败";
        }
    }
    //文件上传
    public function uploadAction(){
        Dispatcher::getInstance()->autoRender(false);
        if(!empty($_FILES['file'])){
            $time = time();
            $fileicon = files("file");
            $filename = strrchr($fileicon['name'],".");
            $dir = APP_PATH."/public/pascreen/".$time;
            if(!file_exists($dir)){
                mkdir($dir,0777,true);
            }
            $pathicon = $dir."/".$time.$filename;
            $bool = move_uploaded_file( $fileicon['tmp_name'],$pathicon);
            if($bool){
                $fileArr = "/pascreen/".$time."/".$time.$filename;
                echo json_encode(["msg"=>"ok","data"=>$fileArr]);
            }else{
                echo json_encode(["msg"=>"error"]);
            }
        }else{
            echo json_encode(["msg"=>"no"]);
        }
    }
    //结束DYX活动
    public function ajaxdiscountAction(){
        Dispatcher::getInstance()->autoRender(false);
        //得到活动id
        $dis_id = post("dis_id");
        //得到本期发行总额度
        $unlock_price = post("unlock_price");
        //得到交易所价格
        $exchange_price = post("exchange_price");
        //修改状态
        $this->db->action($this->db->updateSql("sys_discount",["status"=>1],"discount_num = {$dis_id}"));
        //发放奖品给所有用户
        $activitymodel = new activityModel();
        //求发放总额度
        $total_price_discount = ($unlock_price - $unlock_price*0.1)*0.0706;
        //求本期预约人员与本期个人总预约额度
        $user_discount = $this->db->field("u_id,(dis_price+link_price) as pirce")
            ->table("y_user_discount")
            ->where("dis_id = {$dis_id}")
            ->select();
        $this->sessions->pin_balance = 0;
        foreach($user_discount as $k => $v){
            //得到本期预约人员
            $user_id = $v['u_id'];
            //本期个人总预约额度
            $user_dis_price = $v['pirce'];
            //个人抢购额度比例
            $user_order =  $activitymodel->get_news_user_order($v['u_id']);
            $user_group = $activitymodel->get_news_user_group($v['u_id']);
            $total_level = get_user_scale($user_order['order'],$user_group['level']);
            //计算本期个人所得额度
            $unlock_price = $total_price_discount * $total_level;
            //对比个人预约额度与本期个人所得额度
            $z = bcsub($unlock_price,$user_dis_price);
            //情况1：额度相等
            if($z == 0){
                $this->dyxpinduoduoeq($unlock_price,$user_id,$dis_id,$exchange_price);
            }
            //情况2：额度不够
            if($z > 0){
                $this->dyxpinduoduogt($user_dis_price,$user_id,$dis_id,$z,$exchange_price);
            }
            //情况3：额度太多
            if($z < 0){
                $this->dyxpinduoduolt($unlock_price,$user_id,$dis_id,$z,$exchange_price);
            }
        }
    }
    //相等
    public function dyxpinduoduoeq($unlock_price,$user_id,$dis_id,$exchange_price){
        $int = $this->sessions->pin_balance;
        $this->sessions->pin_balance = $int + 0;
        $y = bcmul($unlock_price,0.1,3);   //10%解锁DYX
        $x = bcsub($unlock_price,$y,3)-bcsub($unlock_price,$y,3)*0.1; //0.1为官方手续费即10%   80%锁定U
        $z = bcdiv($y,$exchange_price,3);
        $s = bcmul($unlock_price,0.85,3);
        $sql = "UPDATE y_user SET token_num = token_num + {$z},token_available_balance = token_available_balance + {$z},usdt_num = usdt_num - {$s} + {$x},usdt_freeze_balance = usdt_freeze_balance - {$s} + {$x} WHERE id = {$user_id}";
        $this->db->beginTransaction();
        $bool1 = $this->db->action($sql);
        $log['user_id'] = $user_id;
        $log['type'] = "DYX拼多多模式";
        $log['info'] = "第{$dis_id}期，成功获得{$z}个DYX，{$x}USDT，解锁{$z}个DXY，未解锁{$x}个USDT";
        $log['create_time'] = time();
        $bool2 = $this->db->action($this->db->insertSql("user_num_log",$log));
        $bool3 = $this->db->action($this->db->updateSql("user_discount",["dis_amount"=>$x],"u_id = {$user_id} AND dis_id = {$dis_id}"));
        echo 1;
        //分配提成
        $this->updateGroup3($user_id,$unlock_price,$exchange_price);
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            $message = "{$user_id}操作成功";
        }else{
            $this->db->rollback();
            $message = "{$user_id}操作失败";
        }
        echo $message;
    }
    //大于
    public function dyxpinduoduogt($unlock_price,$user_id,$dis_id,$balance,$exchange_price){
        $int = $this->sessions->pin_balance;
        $this->sessions->pin_balance = $int + $balance;
        $y = bcmul($unlock_price,0.1,3);
        $x = bcsub($unlock_price,$y,3)-bcsub($unlock_price,$y,3)*0.1;
        $z = bcdiv($y,$exchange_price,3);
        $s = bcmul($unlock_price,0.85,3);
        $sql = "UPDATE y_user SET token_num = token_num + {$z},token_available_balance = token_available_balance + {$z},usdt_num = usdt_num - {$s} + {$x},usdt_freeze_balance = usdt_freeze_balance - {$s} + {$x} WHERE id = {$user_id}";
        $this->db->beginTransaction();
        $bool1 = $this->db->action($sql);
        $log['user_id'] = $user_id;
        $log['type'] = "DYX拼多多模式";
        $log['info'] = "第{$dis_id}期，成功获得{$z}个DYX，{$x}USDT，解锁{$z}个DXY，未解锁{$x}个USDT";
        $log['create_time'] = time();
        $bool2 = $this->db->action($this->db->insertSql("user_num_log",$log));
        $bool3 = $this->db->action($this->db->updateSql("user_discount",["dis_amount"=>$x],"u_id = {$user_id} AND dis_id = {$dis_id}"));
        echo 2;
        //分配提成
        $this->updateGroup3($user_id,$unlock_price,$exchange_price);
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            $message = "{$user_id}操作成功";
        }else{
            $this->db->rollback();
            $message = "{$user_id}操作失败";
        }
        echo $message;
    }
    //小于
    public function dyxpinduoduolt($unlock_price,$user_id,$dis_id,$refund2,$exchange_price){
        $int = $this->sessions->pin_balance;
        $refund = abs($refund2) - $int;
        $s = 0;
        if($refund<0){
            $this->sessions->pin_balance = abs($refund);
            $s = 0;
            $r = bcadd($unlock_price,$s,3);
            $y = bcmul($r,0.1,3);
            $z = bcdiv($y,$exchange_price,3);
            $x = bcsub($r,$y,3)-bcsub($r,$y,3)*0.1;
            $s2 = bcmul($unlock_price,0.85,3);
            $sql = "UPDATE y_user SET token_num = token_num + {$z},token_available_balance = token_available_balance + {$z},usdt_num = usdt_num - {$s2} + {$x},usdt_freeze_balance = usdt_freeze_balance - {$s} + {$x} WHERE id = {$user_id}";
            $this->db->beginTransaction();
            $bool1 = $this->db->action($sql);
            $log['user_id'] = $user_id;
            $log['type'] = "DYX拼多多模式";
            $log['info'] = "第{$dis_id}期，成功获得{$z}个DYX，{$x}个USDT，解锁{$z}个DXY，未解锁{$x}个USDT";
            $log['create_time'] = time();
            $bool2 = $this->db->action($this->db->insertSql("user_num_log",$log));
            $bool3 = $this->db->action($this->db->updateSql("user_discount",["dis_amount"=>$x],"u_id = {$user_id} AND dis_id = {$dis_id}"));
            echo 31;
            //分配提成
            $this->updateGroup3($user_id,$r,$exchange_price);
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                $message = "{$user_id}操作成功";
            }else{
                $this->db->rollback();
                $message = "{$user_id}操作失败";
            }
            echo $message;
        }else{
            $this->sessions->pin_balance = 0;
            $s = abs($refund);
            $r = bcadd($unlock_price,$s,3);
            $y = bcmul($r,0.1,3);
            $z = bcdiv($y,$exchange_price,3);
            $x = bcsub($r,$y,3)-bcsub($r,$y,3)*0.1;
            //押金
            $s2 = bcmul($unlock_price - $refund2,0.85,3);
            //退款
            $countstr = $s2 - $unlock_price - $int;
            $sql = "UPDATE y_user SET token_num = token_num + {$z},token_available_balance = token_available_balance + {$z},usdt_num = usdt_num - {$s2} + {$x},usdt_available_balance = usdt_available_balance + {$countstr},usdt_freeze_balance = usdt_freeze_balance - {$s2} + {$x} WHERE id = {$user_id}";
            $this->db->beginTransaction();
            $bool1 = $this->db->action($sql);
            $log['user_id'] = $user_id;
            $log['type'] = "DYX拼多多模式";
            $log['info'] = "第{$dis_id}期，成功获得{$z}个DYX，{$x}个USDT，退还{$countstr}个USDT，解锁{$z}个DXY，未解锁{$x}个USDT";
            $log['create_time'] = time();
            $bool2 = $this->db->action($this->db->insertSql("user_num_log",$log));
            $bool3 = $this->db->action($this->db->updateSql("user_discount",["dis_amount"=>$x],"u_id = {$user_id} AND dis_id = {$dis_id}"));
            echo 32;
            //分配提成
            $this->updateGroup3($user_id,$r,$exchange_price);
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                $message = "{$user_id}操作成功";
            }else{
                $this->db->rollback();
                $message = "{$user_id}操作失败";
            }
            echo $message;
        }
    }

    public function editdiscountAction(){
        $dis_id = post("dis_id");
        $price = post("price");
        $type = post("type");
        switch ($type){
            case "exchange":
                $bool = $this->db->action($this->db->updateSql("sys_discount",["exchange"=>$price],"discount_num = {$dis_id}"));
            break;
            case "exchange_price":
                $bool = $this->db->action($this->db->updateSql("sys_discount",["exchange_price"=>$price],"discount_num = {$dis_id}"));
                break;
            case "unlock_price":
                $bool = $this->db->action($this->db->updateSql("sys_discount",["unlock_price"=>$price,"success_price"=>$price*0.85],"discount_num = {$dis_id}"));
                break;
        }

        if($bool){
            echo json_encode(["msg"=>"操作成功"]);
        }else{
            echo json_encode(["msg"=>"操作失败"]);
        }
        exit;
    }

    public function dyxgroupdelAction(){
        $id = post("id");
        $uid = post("uid");
        $bool = $this->db->action($this->db->updateSql("dyx_group",["status"=>2],"id = {$id}"));
        $message = "";
        if($bool){
            $message = "驳回成功";
        }else{
            $message = "操作失败";
        }
        echo json_encode(['msg'=>$message]);
        exit;
    }

    public function dyxgroupAction(){
        $id = post("id");
        $uid = post("uid");
        //$this->db->beginTransaction();
        $bool1 = $this->db->action($this->db->updateSql("user",["level"=>3],"id = {$uid}"));
        $bool2 = $this->db->action($this->db->updateSql("dyx_group",["status"=>1],"id = {$id}"));
        $message = "操作成功";
        echo json_encode(['msg'=>$message]);
        exit;

//        $message = "";
//        if($bool1 && $bool2){
//            $this->db->commit();
//            $message = "操作成功";
//        }else{
//            $this->db->rollback();
//            $message = "操作失败";
//        }
//        echo json_encode(['msg'=>$message]);
//        exit;
    }

    public function dyxusergroupAction(){
        $username = post("username");
        $user = $this->db->field("id")
            ->table("y_user")
            ->where("username = '{$username}'")
            ->find();
        $data['u_id'] = $user['id'];
        $data['status'] = 0;
        $data['create_time'] = time();
        $bool = $this->db->action($this->db->insertSql("dyx_group",$data));
        if($bool){
            echo json_encode(["msg"=>"申请成功"]);
        }else{
            echo json_encode(["msg"=>"申请失败"]);
        }
        exit;
    }
    //霸屏用户上传图片
    public function dyxuploadAction(){
        Dispatcher::getInstance()->autoRender(false);
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
        $user = post("u");
        $type = post("t");
        $user_info = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $user_id = $user_info['id'];
        $user_result = $this->db->field("*")
            ->table("y_dyx_user_write")
            ->where("u_id = {$user_id} AND create_time BETWEEN '{$start}' AND '{$end}'")
            ->find();
        if(!empty($user_result)){
            if($type == 'pic1'){
                $data['pic1'] = post("p");
                $data['status1'] = 0;
            }else{
                $data['pic2'] = post("p");
                $data['status2'] = 0;
            }
            $bool = $this->db->action($this->db->updateSql("dyx_user_write",$data,"id = {$user_result['id']} AND u_id = {$user_id}"));
        }else{
            if($type == 'pic1'){
                $data['pic1'] = post("p");
                $data['status1'] = 0;
            }else{
                $data['pic2'] = post("p");
                $data['status2'] = 0;
            }
            $data['u_id'] = $user_id;
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['audit_ststus'] = 0;
            $data['income'] = 0;
            $data['auditors_note1'] = "";
            $data['auditors_note2'] = "";
            $this->db->insertSql("dyx_user_write",$data);
            $bool = $this->db->action($this->db->insertSql("dyx_user_write",$data));
        }

        if($bool){
            echo json_encode(['msg'=>"上传成功"]);
        }else{
            echo json_encode(['msg'=>"系统繁忙，稍后再试"]);
        }
    }

    public function dyxupload2Action(){
        Dispatcher::getInstance()->autoRender(false);
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
        $user = post("u");
        $pic = json_decode(post("p"),true);
        $data['pic1'] =$pic['pic1'];
        $data['pic2'] =$pic['pic2'];
        $data['pic3'] =$pic['pic3'];
        $data['pic4'] =$pic['pic4'];
        $data['pic5'] =$pic['pic5'];
        $data['pic6'] =$pic['pic6'];
        $data['status'] = 0;
        $user_info = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $user_id = $user_info['id'];
        $user_result = $this->db->field("*")
            ->table("y_dyx_user_write2")
            ->where("u_id = {$user_id} AND create_time BETWEEN '{$start}' AND '{$end}'")
            ->find();
        if(!empty($user_result)){
            $bool = $this->db->action($this->db->updateSql("dyx_user_write2",$data,"id = {$user_result['id']} AND u_id = {$user_id}"));
        }else{
            $data['u_id'] = $user_id;
            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['audit_ststus'] = 0;
            $data['income'] = 0;
            $data['auditors_note'] = "";
            $this->db->insertSql("dyx_user_write2",$data);
            $bool = $this->db->action($this->db->insertSql("dyx_user_write2",$data));
        }
        if($bool){
            echo json_encode(['msg'=>"上传成功"]);
        }else{
            echo json_encode(['msg'=>"系统繁忙，稍后再试"]);
        }
    }

    public function dyxcheckdelAction(){
        Dispatcher::getInstance()->autoRender(false);
        $type = post("t");
        $id = post("id");
        $text = post("text");
        $orderid = post("orderid");
        if($type == 'pic1'){
            $data['status1'] = 2;
            $data['auditors_note1'] = $text;
        }else{
            $data['status2'] = 2;
            $data['auditors_note2'] = $text;
        }
        $bool = $this->db->action($this->db->updateSql('dyx_user_write',$data,"u_id = {$id} AND id = {$orderid}"));
        if($bool){
            echo json_encode(['msg'=>"驳回成功"]);
        }else{
            echo json_encode(['msg'=>"系统繁忙,请稍后再试"]);
        }
    }

    public function dyxcheckdel2Action(){
        Dispatcher::getInstance()->autoRender(false);
        $id = post("id");
        $text = post("text");
        $orderid = post("orderid");
        $data['status'] = 2;
        $data['auditors_note'] = $text;
        $bool = $this->db->action($this->db->updateSql('dyx_user_write2',$data,"u_id = {$id} AND id = {$orderid}"));
        if($bool){
            echo json_encode(['msg'=>"驳回成功"]);
        }else{
            echo json_encode(['msg'=>"系统繁忙,请稍后再试"]);
        }
    }

    public function dyxchecksendAction(){
        Dispatcher::getInstance()->autoRender(false);
        $type = post("t");
        $id = post("id");
        $orderid = post("orderid");
        $dyxwrite_model = new dyxwriteModel();
        $bool = $dyxwrite_model->edituserdyx($id,$type,$orderid);
        if($bool == 1){
            echo json_encode(["msg"=>"审核成功"]);
        }else{
            echo json_encode(["msg"=>"系统繁忙，请稍后再试"]);
        }
    }

    public function dyxchecksend2Action(){
        Dispatcher::getInstance()->autoRender(false);
        $id = post("id");
        $orderid = post("orderid");
        $dyxwrite_model = new dyxwriteModel();
        $bool = $dyxwrite_model->edituserdyx2($id,$orderid);
        if($bool == 1){
            echo json_encode(["msg"=>"审核成功"]);
        }else{
            echo json_encode(["msg"=>"系统繁忙，请稍后再试"]);
        }
    }

    public function ajaxodsendoutAction(){
        Dispatcher::getInstance()->autoRender(false);
        $u_id = post("u");
        $order = post("od");
        $btype = post("b");
        $result = $this->db->field("*")
            ->table("y_withdraw")
            ->where("order_no = '{$order}'")
            ->find();
        if(!empty($result)){
            $where = "";
            switch ($btype){
                case 1:$type = "DYX";$where = "token_freeze_balance = token_freeze_balance - {$result['token_num']},token_available_balance = token_available_balance + {$result['token_num']}";break;
                case 2:$type = "USDT";$where = "usdt_freeze_balance = usdt_freeze_balance - {$result['token_num']},usdt_available_balance = usdt_available_balance + {$result['token_num']}";break;
                case 3:$type = "XYT";$where = "xyt_freeze_balance = xyt_freeze_balance - {$result['token_num']},xyt_available_balance = xyt_available_balance + {$result['token_num']}";break;
                case 4:$type = "BTC";$where = "btc_num = btc_num + {$result['token_num']}";break;
                case 5:$type = "分红股";break;
                default:$type = "币种不存在";break;
            }
            $sql = "UPDATE y_user SET {$where} WHERE id = {$u_id}";
            $this->db->beginTransaction();
            $bool1 = $this->db->action($sql);
            $bool2 = $this->db->action($this->db->updateSql('withdraw',['status'=>2],"order_no = '{$order}'"));
            if($bool1 && $bool2){
                $this->db->commit();
                $message = "退款成功";
            }else{
                $this->db->rollback();
                $message = "退款失败,系统繁忙";
            }
            echo json_encode(["message"=>$message]);
        }else{
            echo json_encode(["message"=>"退款失败，订单不存在"]);
        }
    }

    public function pinlogAction(){
        Dispatcher::getInstance()->autoRender(false);
        $user = post("u");
        $dis_id = post("dis");
        $mini_price = post("mini");
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $data['dis_id'] = $dis_id;
        $data['user_id'] = $userinfo['id'];
        $data['create_time'] = time();
        if(!empty($userinfo['id'])){
            $sql = "
            select id,tel,token_available_balance from y_user where floor_id={$userinfo['id']}
            UNION ALL
            select id,tel,token_available_balance from y_user where floor_id in (select id from y_user where floor_id = {$userinfo['id']})
            UNION ALL
            select id,tel,token_available_balance from y_user where floor_id in (select id from y_user where floor_id in (select id from y_user where floor_id = {$userinfo['id']}))";
            $result = $this->db->action($sql);
            if(!empty($result[0]['id'])){
                $arr = [];
                foreach($result as $k=>$v){
                    if($mini_price <= $v['token_available_balance']){
                        $arr[] = $v['tel'];
                    }
                }
                $str = implode(",",$arr);
                $data['user_sub'] = $str;
            }else{
                $data['user_sub'] = "";
            }
            $loginfo = $this->db->field("*")
                ->table("y_pin_log")
                ->where("dis_id = {$dis_id} AND user_id = {$userinfo['id']}")
                ->find();
            if(!empty($loginfo['id'])){
                $bool = $this->db->action($this->db->updateSql("pin_log",$data,"dis_id = {$dis_id} AND user_id = {$userinfo['id']}"));
            }else{
                $bool = $this->db->action($this->db->insertSql("pin_log",$data));
            }
            if($bool){
                echo json_encode(["code"=>1]);
            }else{
                echo json_encode(["code"=>0]);
            }
        }
    }

    public function fomoAction(){
        Dispatcher::getInstance()->autoRender(false);
        $user = post("u");
        $price = post('p');
        $f_id = post('f');
        $time = time();
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$user}'")
            ->find();
        $result = $this->db->field("*")
            ->table("y_fomo_user")
            ->where("f_id = {$f_id} AND u_id = {$userinfo['id']}")
            ->find();
        $fomoresult = $this->db->field("*")
            ->table("y_fomo")
            ->where("f_id = {$f_id}")
            ->find();
        $end_time =  $fomoresult['end_time'];
        $create_time =  $end_time - $time;
        $this->db->beginTransaction();
        if($create_time > 600){
            $sql = "UPDATE y_fomo SET fomo_price = {$price} WHERE f_id = {$f_id}";
        }else{
            $sql = "UPDATE y_fomo SET fomo_price = {$price},end_time = end_time + 600 WHERE f_id = {$f_id}";
        }
        $bool1 = $this->db->action($sql);
        $data['price'] = $price;
        $data['create_time'] = $time;
        if(!empty($result['id'])){
            $bool2 = $this->db->action($this->db->updateSql('fomo_user',$data,"f_id = {$f_id} AND u_id = {$userinfo['id']}"));
        }else{
            $data['f_id'] = $f_id;
            $data['u_id'] = $userinfo['id'];
            $data['address'] = $userinfo['xyt_address'];
            $bool2 = $this->db->action($this->db->insertSql('fomo_user',$data));
        }
        $usersql = "UPDATE y_user SET xyt_available_balance = xyt_available_balance - {$price},xyt_freeze_balance = xyt_freeze_balance + {$price} WHERE id = {$userinfo['id']}";
        $bool3 = $this->db->action($usersql);
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            $this->ajax_return(1,"竞品成功");
        }else{
            $this->db->rollback();
            $this->ajax_return(2,"竞品失败,系统繁忙");
        }
    }

    //用户生成地址
//    public function useraddressAction(){
// 第二步        
//        $str = file_get_contents(APP_PATH."/log/address2.txt");
//        $json_arr = json_decode($str,true);
//        for($i=0;$i<count($json_arr);$i++){
//            $data['token_id'] = null;
//            $data['usdt_addr'] = $json_arr[$i];
//            $data['usdt_public'] = null;
//            $data['usdt_private'] = null;
//            $data['token_addr'] = $json_arr[$i];
//            $data['token_public'] = null;
//            $data['token_private'] = null;
//            $data['xyt_addr'] = $json_arr[$i];
//            $data['xyt_public'] = null;
//            $data['xyt_private'] = null;
//            $data['btc_addr'] = $json_arr[$i];
//            $data['u_id'] = 0;
//            $bool = $this->db->action($this->db->insertSql('token',$data));
//            var_dump($bool);
//        }
//
//        echo "ok";
//        exit;
        
    // 第一步
//        include APP_PATH."/application/core/Pay.php";
//        $pay = new \app\core\Pay();
//
//         $a= 0;
//         //file_put_contents(APP_PATH."/log/address.txt","{",FILE_APPEND);
//         for($i=0;$i<=300;$i++){
//             $httpdata = $pay->address();
//             $result = json_decode($httpdata,true);
//             file_put_contents(APP_PATH."/log/address.txt","\"{$a}\":\"{$result['data']['address']}\",",FILE_APPEND);
//             $a++;
//         }
//        //file_put_contents(APP_PATH."/log/address.txt","}",FILE_APPEND);
//         echo "ok";
//         exit;
//    }

    //解锁群主活动大洋线
    public function unlockgroupdyxAction(){
        $groupdyx = $this->db->field("*")
            ->table("y_offering")
            ->where("unlock_time = 33")
            ->select();
        foreach($groupdyx as $k=>$v){
            $this->db->beginTransaction();
            $data = bcdiv(($v['dyx_num']-$v['dyx_num']*0.1),40,3);
            $sql = "update y_offering set unlock_time = 32,lock_dyx_num = lock_dyx_num - {$data},unlock_dyx_num = unlock_dyx_num + {$data} where id = {$v['id']};";
            $bool1 = $this->db->action($sql);
            $sql2 = "update y_user set token_available_balance = token_available_balance + {$data},token_freeze_balance = token_freeze_balance - {$data} where id = {$v['user_id']};";
            $bool2 = $this->db->action($sql2);
            $log['user_id'] = $v['user_id'];
            $log['type'] = "群主额度解锁";
            $log['info'] = "成功{$data}个DYX";
            $log['create_time'] = time();
            $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
            var_dump($bool1);
            var_dump($bool2);
            var_dump($bool3);
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                echo "{$k}ok<hr>";
            }else{
                $this->db->rollback();
                echo "{$k}nosql：{$sql}===={$sql2}<hr>";
            }

        }
        exit;
    }
    //统计充值usdt未兑换活动，剩余的usdt
    public function usdtcountAction(){
        $userinfo = $this->db->field("id,u_id,token_num")
            ->table("y_recharge")
            ->where("currency = 'USDT' AND status = 1 AND u_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)")
            ->select();
        $arr = [];
        foreach ($userinfo as $k=>$v){
            if ($arr[$v['u_id']]){
                $arr[$v['u_id']]['token_num'] += $v['token_num'];
            }else{
                $arr[$v['u_id']] = $v;
            }
        }

        $total = "";
        $tota2 = "";

        foreach ($arr as $a=>$b){
            $user = $this->db->field("id,usdt_available_balance,usdt_freeze_balance")
                ->table("y_user")
                ->where("id = {$a}")
                ->find();
            $arr[$a]['usdt_available_balance']= $user['usdt_available_balance'];
            $arr[$a]['usdt_freeze_balance']= $user['usdt_freeze_balance'];
            $total += $user['usdt_available_balance']+$user['usdt_freeze_balance'];
            $tota2 += $arr[$a]['token_num'];
        }

        //echo "<pre>";
        //print_r($arr);
        echo "总共充值USDT:".$tota2."<hr>";
        echo "剩余USDT未兑换:".$total."<hr>";
        exit;
    }
    //统计群主审核通过
    public function groupnumAction(){
        $sql = 'SELECT o.id,o.user_id,u.username,o.usdt_num,o.dyx_num,o.unlock_dyx_num,o.lock_dyx_num,o.create_time FROM y_offering as o INNER JOIN y_user as u ON u.id = o.user_id WHERE o.user_id NOT IN (8,9,10,11,12,4036,4860,5135,600000,600006)';  //AND o.id < 116
        $result = $this->db->action($sql);
        $newArr = [];
        foreach($result as $k=>$v){
            $newArr[$k]['排序ID'] = $v['id'];
            $newArr[$k]['用户ID'] = $v['user_id'];
            $newArr[$k]['用户手机号'] = $v['username'];
            $newArr[$k]['兑换USDT数量'] = $v['usdt_num'];
            $newArr[$k]['本次得到DYX总数数量'] = $v['dyx_num'];
            $newArr[$k]['本次得到DYX解锁数量'] = $v['unlock_dyx_num'];
            $newArr[$k]['剩余待解锁数量'] = $v['lock_dyx_num'];
            $newArr[$k]['兑换时间'] = date("Y-m-d H:i:s",$v['create_time']);

//            $sql1 = "UPDATE y_user SET token_num = token_num + {$v['dyx_num']},token_available_balance = token_available_balance + {$v['dyx_num']} WHERE id={$v['user_id']}";
//            echo $sql1."<hr>";
//            $bool1 = $this->db->action($sql1);
//            var_dump($bool1);
//            echo "<hr>";
//            $sql2 = "INSERT INTO y_user_num_log VALUES(NULL,'DYX群主额度','1570244278','补贴0.004操作：空投奖励{$v['dyx_num']}DYX',{$v['user_id']})";
//            echo $sql2."<hr>";
//            $bool2 = $this->db->action($sql2);
//            var_dump($bool2);
//            echo "<hr>";
        }
        $title = ["排序","用户ID","用户手机号","兑换USDT数量","本次得到DYX总数数量","本次得到DYX解锁数量","剩余待解锁数量","兑换时间"];
        include APP_PATH."/application/core/Phpecel2.php";
        $ecel = new \app\core\Phpecel2();
        $ecelobj = $ecel->importDataForObj($newArr,$title);
        $ecel->download($ecelobj,"群主额度人员表");


        exit;
    }
    //更新活期宝
    public function editlifeAction(){
        Dispatcher::getInstance()->autoRender(false);
        $user = post("u");
        $lifemodel = new lifeModel();
        $res = $lifemodel->put_life_yl($user);
        echo $res;
    }

    public function updateGroup3($uid,$total_price,$exchange_price,$level = 1)
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
        if(empty($user) && $user['token_available_balance'] < 10000){
            return true;
        }else{
            $price = 0;
            switch ($level){
                case 1:$price = ($total_price*0.03)/$exchange_price;break;
                case 2:$price = ($total_price*0.02)/$exchange_price;break;
                case 3:$price = ($total_price*0.01)/$exchange_price;break;
            }
            //修改用户dyx信息
            $sql = "UPDATE y_user SET token_num = token_num + {$price},token_available_balance = token_available_balance + {$price} WHERE id = {$user['floor_id']}";
            $this->db->action($sql);
            //记录日志
            $log['user_id'] = $user['floor_id'];
            $log['type'] = "DYX拼多多模式";
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
            $this->updateGroup3($puid,$total_price,$exchange_price,$level+1);
        }
    }

}