<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class AdminController extends Controller_Abstract{
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
        $this->db = new dbModel();
        if(!empty($this->sessions->username)){
            $this->user = $this->sessions->username;
        }else{
            success("请先登陆!","/login/index");
            exit;
        }
    }
    //首页
    public function indexAction(){
        $this->getView()->assign("username",$this->user);
    }
    //统计
    public function statAction(){
        //平台充值DYX
        $hotsql = "SELECT SUM(token_num) as total FROM `y_recharge` WHERE address = '0x036c470cf3494d61a60ded5c84a0a791e112401f' AND currency = 'HOT'";
        $datahost =  $this->db->action($hotsql);
        //平台提现DYX
        $txsql = "SELECT SUM(token_num) as total FROM `y_withdraw` WHERE currency = '1' AND status = 1 AND u_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $datatx = $this->db->action($txsql);
        //0.0018数据
        $sql1 = "SELECT SUM(usdt_num) as usdt,SUM(dyx_num) as total,SUM(unlock_dyx_num) as dunlock,SUM(lock_dyx_num) as dlock FROM y_offering WHERE id <= 110 AND user_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $data18 =  $this->db->action($sql1);
        //0.002数据
        $sql2 = "SELECT SUM(usdt_num) as usdt,SUM(dyx_num) as total,SUM(unlock_dyx_num) as dunlock,SUM(lock_dyx_num) as dlock FROM y_offering WHERE id > 110 AND user_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $data20 =  $this->db->action($sql2);
        //计算余额
        $wsql = "SELECT u_id,token_num FROM y_withdraw WHERE currency = '1' AND status = 1 AND u_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $data =  $this->db->action($wsql);
        $newArr = array();  // 这里是相加后处理过的数组，以单号为key
        foreach($data as $k=>$v){
            if (array_key_exists($v['u_id'], $newArr)) {
                $newArr[$v['u_id']]['token_num'] += $v['token_num'];
            }else{
                $newArr[$v['u_id']] = $v;
            }
        }
        $arrData = array_values($newArr);
        //20
        $wsql1 = "SELECT user_id,dyx_num FROM y_offering WHERE id > 110 AND user_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $data2 =  $this->db->action($wsql1);
        $arr20 = [];
        foreach ($data2 as $k1=>$v1){
            $data2[$k1]['dyx_num'] = $v1['dyx_num'] * 2;
            $arr20[] = $v1['user_id'];
        }
        //18
        $wsql2 = "SELECT user_id,dyx_num FROM y_offering WHERE id <= 110 AND user_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)";
        $data3 =  $this->db->action($wsql2);
        $arr18 = [];
        foreach ($data3 as $k2=>$v2){
            $arr18[] = $v2['user_id'];
        }
        $new18 = array_values(array_unique($arr18));
        //计算
        $total20 = 0;
        $total18 = 0;
        foreach($arrData as $x=>$y){
            if(in_array($y['u_id'],$arr20)){
                $total20 += $y['token_num'];
            }
            if(in_array($y['u_id'],$new18)){
                $total18 += $y['token_num'];
            }
        }
        $data18[0]['wx'] = $total18;
        $data18[0]['ywx'] = $data18[0]['total'] - $total18;
        $data20[0]['wx'] = $total20;
        $data20[0]['ywx'] = $data20[0]['total']*2 - $total20;
        $this->getView()->assign(["username"=>$this->user,"data18"=>$data18,"data20"=>$data20,"datahost"=>$datahost,"datatx"=>$datatx]);
    }
    //欢迎页
    public function welcomeAction(){
        $week_start = strtotime('monday', time());
        $week_end = strtotime('+2 monday', time())-1;
        $time_start = strtotime(date("Y-m-d 0:0:0"));
        $time_end = strtotime(date("Y-m-d 24:59:59"));
        $day_map = " between {$time_start} and {$time_end}";
        $week_map = " between {$week_start} and {$week_end}";
        $new_time = strtotime(date("2019-08-20 0:0:0"));
        //新用户数
        $data['user_new_total'] = $this->db->zscount("user","*","total","new_status = 1 AND create_time > '{$new_time}'");
        //总用户数
        $data['user_total'] = $this->db->zscount("user");
        //每日新增用户数
        $data['user_day_total'] = $this->db->zscount("user","*","total","create_time ".$day_map);
        //每周新增用户数
        $data['user_week_total'] = $this->db->zscount("user","*","total","create_time ".$week_map);
        //总DYX充值
        $data['dyx_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'DYX'");
        //每日DYX充值
        $data['dyx_day_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'DYX' and create_time ".$day_map);
        //总USDT充值
        $data['usdt_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'USDT' AND u_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)");
        //每日USDT充值
        $data['usdt_day_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'USDT' and create_time ".$day_map." AND u_id NOT IN(8,9,10,11,12,4036,4860,5135,600000,600006)");
        //总XYT充值
        $data['xyt_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'XYT'");
        //每日XYT充值
        $data['xyt_day_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'XYT' and create_time ".$day_map);
        //提现手续费充值
        $data['tx_recharge_total'] = $this->db->zssum("recharge","token_num","num","currency = 'HOT'");
        //总DYX提现
        $data['dyx_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '1' AND status = 1");
        //每日DYX提现
        $data['dyx_day_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '1' AND status = 1 and create_time ".$day_map);
        //总USDT提现
        $data['usdt_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '2' AND status = 1");
        //每日USDT提现
        $data['usdt_day_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '2' AND status = 1 and create_time ".$day_map);
        //总XYT提现
        $data['xyt_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '3' AND status = 1");
        //每日XYT提现
        $data['xyt_day_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '3' AND status = 1 and create_time ".$day_map);
        //总BTC提现
        $data['btc_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '4' AND status = 1");
        //每日BTC提现
        $data['btc_day_tx_total'] = $this->db->zssum("withdraw","token_num","num","currency = '4' AND status = 1 and create_time ".$day_map);
        //总预约
        $data['reserve_total'] = $this->db->field("(sum(dis_price)+sum(link_price)) as price")->table("y_user_discount")->find();
        //总手动预约
        $data['reserve_dis_total'] = $this->db->field("sum(dis_price) as price")->table("y_user_discount")->find();
        //总连续预约
        $data['reserve_link_total'] = $this->db->field("sum(link_price) as price")->table("y_user_discount")->find();
        $data['dis_id'] = (new activityModel())->get_discount_id();
        //x期总预约
        $data['x_reserve_total'] = $this->db->field("(sum(dis_price)+sum(link_price)) as price")->table("y_user_discount")->where("dis_id = {$data['dis_id']}")->find();
        //x期手动预约
        $data['x_reserve_dis_total'] = $this->db->field("sum(dis_price) as price")->table("y_user_discount")->where("dis_id = {$data['dis_id']}")->find();
        //x期连续预约
        $data['x_reserve_link_total'] = $this->db->field("sum(link_price) as price")->table("y_user_discount")->where("dis_id = {$data['dis_id']}")->find();
        $this->getView()->assign(["data"=>$data,"username"=>$this->user]);
    }
    //管理员
    public function systemAction(){
        $result = $this->db->field("*")->table("y_system")->select();
        $this->getView()->assign("arrdata", $result);
    }
    //管理员日志
    public function systemlogAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("system_log");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_system_log ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //个人排名
    public function personalrankingAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("user");
        $page->init($len,10);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_user ORDER BY token_available_balance DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //团队排名
    public function teamrankingAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $sql = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
        $count = count($this->db->action($sql));
        $page->init($count,10);
        $showstr = $page->show();
        $sqlpage = $sql." ".$page->limit;
        $page = $this->db->action($sqlpage);
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //历史排名
    public function historicalrankingAction(){
        $this->getView()->assign("arrdata", []);
    }
    //用户列表
    public function userlistAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("user");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_user ORDER BY id DESC {$page->limit} ");
        foreach ($page as $k=>$v){
            $xyt = $this->db->zssum("pay_wallet_order","xyt_number","num","xcoinpay_userid = {$v['id']} and status = 3");
            $page[$k]['xyt_num'] = $xyt;
            $page[$k]['xyt_available_balance'] = $xyt;
        }
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //用户搜索
    public function usersearchAction(){
        $username = $_REQUEST['search'];
        $userinfo = $this->db->field("*")
            ->table("y_user")
            ->where("tel = '{$username}'")
            ->find();
        $xyt = $this->db->zssum("pay_wallet_order","xyt_number","num","xcoinpay_userid = {$userinfo['id']} and status = 3");
        $userinfo['xyt_num'] = $xyt;
        $userinfo['xyt_available_balance'] = $xyt;

        $this->getView()->assign(["userinfo"=>$userinfo]);
    }
    //修改用户身份
    public function ajaxuserAction(){
        $data = post("data");
        $user = post("user");
        $bool = $this->db->action($this->db->updateSql("user",['level'=>$data],"id = {$user}"));
        if($bool){
            echo json_encode(["code"=>1,"message"=>"修改成功"]);
        }else{
            echo json_encode(["code"=>0,"message"=>"修改失败"]);
        }
        exit;
    }
    //充值记录
    public function rechargeAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("recharge");
        $showpage = 100;
        $page->init($len,$showpage);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_recharge ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr,"showpage"=>$showpage]);
    }
    //提现记录
    public function withdrawAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("withdraw");
        $showpage = 100;
        $page->init($len,$showpage);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_withdraw ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr,"showpage"=>$showpage]);
    }
    //打折活动
    public function discountAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("sys_discount");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_sys_discount ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //打折活动
    public function discountaddAction(){
        if($this->getRequest()->isPost()) {
            Dispatcher::getInstance()->autoRender(false);
            $data['discount_num'] = post('discount_num');
            $data['lock_exchange_price'] = post('token')."代币 = ".post('usdt')."USDT ,(".date("Y年m月 H:i:s",strtotime(post('dtime'))).")数据取自".post('exchange')."交易所";
            $data['lock85_price'] = post('lock85_price');
            $data['unlock_price'] = post('unlock_price');
            $data['success_price'] = post('success_price');
            $data['system_price'] = 0;
            $data['system_price'] = post('user_minimum_amount');
            $data['income'] = "12.56%";
            $data['status'] = 0;
            $data['create_time'] = time();
//            $packges["discount_package_token"] = post('discount_package_token');
//            $packges["discount_package_usdt"] = post('discount_package_usdt');
//            $packges["discount_package_xyt"] = post('discount_package_xyt');
//            $packges["discount_package_btc"] = post('discount_package_btc');
//            $packges["discount_package_stock"] = post('discount_package_stock');
//            $data['discount_package'] = json_encode($packges);
            $data['discount_package'] = "";
            $bool = $this->db->action($this->db->insertSql("sys_discount",$data));
            statusUrl($bool,"ADD OK","/admin/discount","ADD ERROR");
        }else{
            $this->getView()->assign("xxx","yyy");
        }
    }
    //icon字体
    public function iconfontAction(){
        $this->getView()->assign("xxx","yyy");
    }
    //DXY群主额度福利
    public function dyxdiscountAction(){
        $dyxdiscount = $this->db->field("*")->table("y_dyxdiscount")->find();
        $this->getView()->assign("dyxdiscount",$dyxdiscount);
    }
    //修改DYX群主福利
    public function editdyxAction(){
        if($this->getRequest()->isPost()){
            Dispatcher::getInstance()->autoRender(false);
            $id = post("id");
            $data['dxy_total_group'] = post("dxy_total_group");
            $data['dxy_total_member'] = post("dxy_total_member");
            $data['dxy_toal_lord'] = post("dxy_toal_lord");
            $data['dyx_toal_new_lord'] = post("dyx_toal_new_lord");
            $bool = $this->db->action($this->db->updateSql('dyxdiscount',$data,"id = {$id}"));
            statusUrl($bool,"修改成功","/admin/dyxdiscount","修改失败");
        }else{
            $id = get("id");
            $dyxdiscount = $this->db->field("*")
                ->table("y_dyxdiscount")
                ->where("id = {$id}")
                ->find();
            $this->getView()->assign("dyxdiscount",$dyxdiscount);
        }
    }
    //用户日志
    public function userlogAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("user_num_log");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_user_num_log ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //查询用户日志
    public function usersearchlogAction(){
        $username = $_REQUEST['search'];
        $userinfo = $this->db->field("id")
            ->table("y_user")
            ->where("tel = '{$username}'")
            ->find();
        if(!empty($userinfo['id'])){
            include APP_PATH."/application/core/Page.php";
            $page = new \app\core\Page();
            $len = $this->db->zscount("user_num_log","*","total","user_id = {$userinfo['id']}");
            $page->init($len,100);
            $showstr = $page->show();
            $page = $this->db->action("SELECT * FROM y_user_num_log WHERE user_id = {$userinfo['id']} ORDER BY id DESC {$page->limit} ");
            $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
        }else{
            $this->getView()->assign(["arrdata"=>[],"showstr"=>""]);
        }
    }
    //霸屏用户
    public function dyxpauserlistAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("pa_screen","*","total","status = 1");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_pa_screen WHERE status = 1 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //审核霸屏用户
    public function dyxpauserAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("pa_screen","*","total","status = 0");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_pa_screen WHERE status = 0 ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
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
    //导出Execl
    public function excelAction(){
        $type = get("type");  //充值  提现
        $tag = get("tag"); //1全部导出 2分页导出
        $page = get("page"); //页码
        $showpage = get("showpage");
        $start = ($page-1)*$showpage;
        $sql = "";
        $title = "";
        $newArr = [];
        if($type == 'recharge'){
            $title = ["排序ID","账号","币种","地址","金额","支付状态","充值时间"];
            if($tag == 1){
                $sql = "SELECT * FROM y_recharge ORDER BY id DESC";
            }else{
                $sql = "SELECT * FROM y_recharge ORDER BY id DESC LIMIT {$start},{$showpage}";
            }
            $arrData = $this->db->action($sql);
            foreach($arrData as $k=>$v){
                $newArr[$k]['排序ID'] = $v['id'];
                $newArr[$k]['账号'] = user_id_name($v['u_id']);
                $newArr[$k]['币种'] = $v['currency'];
                $newArr[$k]['地址'] = $v['address'];
                $newArr[$k]['金额'] = $v['token_num'];
                $newArr[$k]['支付状态'] = ($v['status'] == 1)?"已支付":"未支付";
                $newArr[$k]['充值时间'] = date("Y-m-d H:i:s",$v['create_time']);
            }
        }else{
            $title = ["排序ID","账号","币种","提现地址","订单","金额","申请状态","处理方式","提现时间","提现状态"];
            if($tag == 1){
                $sql = "SELECT * FROM y_withdraw ORDER BY id DESC";
            }else{
                $sql = "SELECT * FROM y_withdraw ORDER BY id DESC LIMIT {$start},{$showpage}";
            }
            $arrData = $this->db->action($sql);
            foreach($arrData as $k=>$v){
                $newArr[$k]['排序ID'] = $v['id'];
                $newArr[$k]['账号'] = user_id_name($v['u_id']);
                $type1 = "";
                switch ($v['currency']){
                    case 1:$type1 = "DYX";break;
                    case 2:$type1 = "USDT";break;
                    case 3:$type1 = "XYT";break;
                    case 4:$type1 = "BTC";break;
                    case 5:$type1 = "分红股";break;
                    default:$type1 = "币种不存在";break;
                }
                $newArr[$k]['币种'] = $type1;
                $newArr[$k]['提现地址'] = $v['address'];
                $newArr[$k]['订单'] = $v['order_no'];
                $newArr[$k]['金额'] = $v['token_num'];
                $newArr[$k]['申请状态'] = ($v['build_status'] == 1)?"申请成功":"申请失败";
                $newArr[$k]['处理方式'] = ($v['auto_address'] == 1)?"手动处理":"自动处理";
                $newArr[$k]['提现时间'] = date("Y-m-d H:i:s",$v['create_time']);
                $newArr[$k]['提现状态'] = ($v['status'] == 1)?"已支付":"未支付";
            }
        }
        include APP_PATH."/application/core/Phpecel2.php";
        $ecel = new \app\core\Phpecel2();
        $ecelobj = $ecel->importDataForObj($newArr,$title);
        $ecel->download($ecelobj,$type);
        exit;
    }
    public function excel2Action(){
        $type = get("type");  //充值  提现
        $sql = "";
        $newArr = [];
        if($type == 'reglog'){
            $sql = "SELECT * FROM y_user_num_log WHERE type = '充值' ORDER BY id DESC";
        }else{
            $sql = "SELECT * FROM y_user_num_log WHERE type = '空投' ORDER BY id DESC";
        }
        $title = ["排序ID","账号","类型","描述信息","时间"];
        $arrData = $this->db->action($sql);
        foreach($arrData as $k=>$v){
            $newArr[$k]['排序ID'] = $v['id'];
            $newArr[$k]['账号'] = user_id_name($v['user_id']);
            $newArr[$k]['类型'] = $v['type'];
            $newArr[$k]['描述信息'] = $v['info'];
            $newArr[$k]['时间'] = date("Y-m-d H:i:s",$v['create_time']);
        }
        include APP_PATH."/application/core/Phpecel2.php";
        $ecel = new \app\core\Phpecel2();
        $ecelobj = $ecel->importDataForObj($newArr,$title);
        $ecel->download($ecelobj,$type);
        exit;
    }
    //排名额度分配
    public function bulidrankingAction(){
        if($this->getRequest()->isPost()) {
            Dispatcher::getInstance()->autoRender(false);
            $id = post("id");
            $data['personal'] = post("personal");
            $data['team'] = post("team");
            $bool = $this->db->action($this->db->updateSql('bulidranking',$data,"id = {$id}"));
            statusUrl($bool,"修改成功","/admin/bulidranking","修改成功");
        }else{
            $data = $this->db->field("*")
                ->table("y_bulidranking")
                ->find();
            $this->getView()->assign(["data"=>$data]);
        }
    }
    //dyx拼多多预约
    public function dyxdiscountlistAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("user_discount");
        $page->init($len,12);
        $showstr = $page->show();
        $page = $this->db->action("SELECT * FROM y_user_discount ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //dyx新群主额度审核
    public function dyxgroupAction(){
        include APP_PATH."/application/core/Page.php";
        $page = new \app\core\Page();
        $len = $this->db->zscount("dyx_group","*","total","status = 0");
        $page->init($len,12);
        $showstr = $page->show();
        $new_time = strtotime(date("2019-09-21 20:30:00"));
        $page = $this->db->action("SELECT * FROM y_dyx_group WHERE status = 0 AND create_time >= '{$new_time}' ORDER BY id DESC {$page->limit} ");
        $this->getView()->assign(["arrdata"=>$page,"showstr"=>$showstr]);
    }
    //PHPECEL
    public function phpecel($arr,$title){
        include APP_PATH."/application/core/Phpecel2.php";
        $ecel = new \app\core\Phpecel2();
//            $arr = [
//                ["id"=>1,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"],
//                ["id"=>2,"name"=>"笑什么"]
//            ];
//            $title = ["ID","name"];
        $ecelobj = $ecel->importDataForObj($arr,$title);
        $ecel->download($ecelobj);
    }

    public function emptyAction()
    {
        // TODO: Implement __call() method.
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

    //fomo拍卖活动
    public function auctionAction(){

    }

}