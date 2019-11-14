<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class OperateController extends Controller_Abstract{
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
        $this->db = new dbModel();
        if(!empty($this->sessions->username)){
            $this->user = $this->sessions->username;
        }else{
            success("请先登陆!","/market/index");
            exit;
        }
    }
    //首页
    public function indexAction(){
        $this->getView()->assign("username",$this->user);
    }
    public function welcomeAction(){
        $this->getView()->assign("username",$this->user);
    }
    //dyx新群主额度审核
    public function dyxgroupAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $new_time = strtotime(date("2019-09-21 20:30:00"));
        $len = $this->db->zscount("dyx_group","*","total","status = 0 AND create_time >= '{$new_time}' AND u_id NOT IN(8,9,10,11,12,4036,5135,600000,600006)");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_group WHERE status = 0 AND create_time >= '{$new_time}' AND u_id NOT IN(8,9,10,11,12,4036,5135,600000,600006) ORDER BY id DESC {$page->limit} ");
        $num = $this->db->zscount("dyx_group","DISTINCT u_id","total","create_time >= '{$new_time}' AND u_id NOT IN(8,9,10,11,12,4036,5135,600000,600006)");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr,"num"=>$num,"len"=>$len]);
    }

    public function dyxgrouperrorAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $new_time = strtotime(date("2019-09-21 20:30:00"));
        $len = $this->db->zscount("dyx_group","*","total","status = 2 AND create_time >= '{$new_time}' AND u_id NOT IN(8,9,10,11,12,5135,600000,600006)");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_group WHERE status = 2 AND create_time >= '{$new_time}' AND u_id NOT IN(8,9,10,11,12,5135,600000,600006) ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //霸屏用户
    public function dyxpauserlistAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("pa_screen","*","total","status = 1 AND user_id NOT IN(8,9,10,11,12,5135,600000,600006)");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_pa_screen WHERE status = 1 AND user_id NOT IN(8,9,10,11,12,5135,600000,600006) ORDER BY id DESC {$page->limit} ");
        $num = $this->db->zscount("pa_screen","DISTINCT user_id","total","user_id NOT IN(8,9,10,11,12,5135,600000,600006)");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr,"num"=>$num,"len"=>$len]);
    }
    //审核霸屏用户
    public function dyxpauserAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("pa_screen","*","total","status = 0 AND user_id NOT IN(8,9,10,11,12,5135,600000,600006)");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_pa_screen WHERE status = 0 AND user_id NOT IN(8,9,10,11,12,5135,600000,600006) ORDER BY id DESC {$page->limit} ");
        $num = $this->db->zscount("pa_screen","DISTINCT user_id","total","user_id NOT IN(8,9,10,11,12,5135,600000,600006)");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr,"num"=>$num,"len"=>$len]);
    }
    //DXY霸屏任务发布
    public function dyxreleaseAction(){
        if($this->getRequest()->isPost()) {
            $type = post("type");
            $pic = post("p");
            $text = post("t");
            if($type == 'pic1'){
                $data['pic1']=$pic;
                $data['text1']=$text;
                $data['create_time'] = date("Y-m-d H:i:s",time());
            }else{
                $data['pic2']=$pic;
                $data['text2']=$text;
                $data['create_time2'] = date("Y-m-d H:i:s",time());
            }
            $result = $this->db->field("*")
                ->table("y_dyx_write")
                ->where("id = 1")
                ->find();
            if(!empty($result)){
                $bool = $this->db->action($this->db->updateSql("dyx_write",$data,"id = 1"));
            }else{
                $bool = $this->db->action($this->db->insertSql("dyx_write",$data));
            }
            if($bool){
                echo json_encode(['code'=>1]);
            }else{
                echo json_encode(['code'=>0]);
            }
            exit;
        }else{
            $result = $this->db->field("*")
                ->table("y_dyx_write")
                ->where("id = 1")
                ->find();
            if(!empty($result['pic1']) && !empty($result['text1'])){
                $a = 1;
            }else{
                $a = 0;
            }
            if(!empty($result['pic2']) && !empty($result['text2'])){
                $b = 1;
            }else{
                $b = 0;
            }
            $time1 =strtotime($result['create_time']);
            $time3 =strtotime($result['create_time2']);
            $time2 =strtotime(date("Y-m-d 00:00:00"));
            if($time1 > $time2){
                $c = 1;
            }else{
                $c = 0;
            }
            if($time3 > $time2){
                $d = 1;
            }else{
                $d = 0;
            }
            $this->getView()->assign(["result"=>$result,"a"=>$a,"b"=>$b,"c"=>$c,"d"=>$d]);
        }
    }
    //霸屏任务审核
    public function dyxcheckAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_user_write","*","total","status1 = 0 OR status2 = 0");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_user_write WHERE status1 = 0 OR status2 = 0 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //霸屏任务审核通过
    public function dyxsuccessAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_user_write","*","total","status1 = 1 AND status2 = 1");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_user_write WHERE status1 = 1 AND status2 = 1 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //DXY群主额度福利
    public function dyxdiscountAction(){
        $dyxdiscount = $this->db->field("*")->table("y_dyxdiscount")->find();
        $this->getView()->assign("dyxdiscount",$dyxdiscount);
    }
    //新霸屏任务审核
    public function dyxcheck2Action(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_user_write2","*","total","status = 0");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_user_write2 WHERE status = 0 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //霸屏任务审核通过
    public function dyxsuccess2Action(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_user_write2","*","total","status = 1");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_user_write2 WHERE status = 1 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }

    //霸屏任务未通过
    public function dyxerrorAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_user_write2","*","total","status = 2");
        $page->init($len,100);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_dyx_user_write2 WHERE status = 2 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }

    public function dyxkontouAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("admin_log");
        $page->init($len,10);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_admin_log ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //空投奖励
    public function dyxktAction(){
        $price = post("p");
        $tel = post('t');
        $result = $this->db->field("*")
            ->table("y_user")
            ->where("tel = {$tel}")
            ->find();
        if(!empty($result['id'])){
            $this->db->beginTransaction();
            $sql = "UPDATE y_user SET token_num = token_num + {$price},token_available_balance = token_available_balance + {$price} WHERE id = {$result['id']}";
            $bool1 = $this->db->action($sql);
            $log['user_id'] = $result['id'];
            $log['type'] = "空投";
            $log['info'] = "奖励{$price}个DYX";
            $log['create_time'] = time();
            $bool2 = $this->db->action($this->db->insertSql("user_num_log",$log));
            $alog['admin'] = $this->user;
            $alog['create_time'] = time();
            $alog['type'] = "空投DYX奖励";
            $alog['ip'] = $_SERVER['REMOTE_ADDR'];
            $alog['price'] = $price;
            $alog['tel'] = $tel;
            $bool3 = $this->db->action($this->db->insertSql("admin_log",$alog));
            if($bool1 && $bool2 && $bool3){
                $this->db->commit();
                echo json_encode(["code"=>1,"message"=>"操作成功"]);
                exit;
            }else{
                $this->db->rollback();
                echo json_encode(["code"=>0,"message"=>"操作失败,系统繁忙"]);
                exit;
            }
        }else{
            echo json_encode(["code"=>2,"message"=>"手机号不存在"]);
            exit;
        }
    }

}