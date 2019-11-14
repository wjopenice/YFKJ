<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class IndexController extends Controller_Abstract {
    const Login_Key = "BplVQpWfm5cjuKmbiKUvQqdErqhAp8vG";
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }

    public function indexAction() {//默认Action
        $user = $this->sessions->user;
        $this->getView()->assign("user", $user);
    }
    //抢购活动/开始预约
    public function activityAction() {//默认Action
        if(empty($this->sessions->user)){
            success("请先登录","/index/login");
        }else{
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $discount_id = $activitymodel->get_discount_id();
            $result = $activitymodel->get_discount_data();
            $total_price = $activitymodel->get_user_discount_total_price($discount_id);
            $usersys_discount = $activitymodel->get_sysuser_discount($user);
            //$user_usdt = $activitymodel->get_user_usdt($user);
            //$user_team_usdt = $activitymodel->get_team_usdt($user);
            //$user_invite_num = $activitymodel->get_invite_num($discount_id,$user);
            //$user_order = $activitymodel->get_user_order($user);
            //$group_num = $activitymodel->get_group_num($user);
            //$inviteid_num = $activitymodel->get_inviteid_num($user);
            //$inviteid = $activitymodel->get_news_inviteid($discount_id,$user);
//            $oneData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],1);
//            $twoData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],2);
//            $threeData = $activitymodel->get_youinvutation($user,$usersys_discount[0]['discount_num'],3);
            $userdata = $activitymodel->get_user_data($user);
            $user_order =  $activitymodel->get_news_user_order($user);
            $user_group = $activitymodel->get_news_user_group($user);
            $total_level =  $activitymodel->get_user_total_level($user_order['order'],$user_group['level']);
            $this->getView()->assign([
                "result"=>$result,
                "user"=>$user,
                "total_price"=>$total_price,
                "usersys_discount"=>$usersys_discount,
                "user_order"=>$user_order,
                "userdata"=>$userdata,
                "user_group"=>$user_group,
                "total_level"=>$total_level
            ]);
        };
    }

    public function unloginAction(){
        if(empty($this->sessions->user)) {
            $activitymodel = new activityModel();
            $result = $activitymodel->get_discount_data();
            $usersys_discount = $activitymodel->unlogin_sysuser_discount();
            $this->getView()->assign(["user" => "","result" => $result,"usersys_discount"=>$usersys_discount]);
        }else {
            $user = $this->sessions->user;
            $activitymodel = new activityModel();
            $result = $activitymodel->get_discount_data();
            $usersys_discount = $activitymodel->unlogin_sysuser_discount();
            $this->getView()->assign(["user" => $user,"result" => $result,"usersys_discount"=>$usersys_discount]);
            //$user = $this->sessions->user;
            //$activitymodel = new activityModel();
            $userdata = $activitymodel->get_user_data($user);
            $this->getView()->assign(["user" => $user, "userdata" => $userdata]);
        }


    }
    //登录
    public function loginAction() {//默认Action
        if($this->getRequest()->isPost()) {
            Dispatcher::getInstance()->autoRender(false);
            $username = addslashes(post('username'));
            $userpass = md5(addslashes(post('userpass')));
            $result = $this->db->field("*")
                ->table("y_user")
                ->where("username = '{$username}' and userpass = '{$userpass}'")
                ->find();
            if(!empty($result)){
                $data['login_time'] = time();
                $data['ip'] = $_SERVER['REMOTE_ADDR'];
                $this->db->action($this->db->updateSql("user",$data,"username = '{$username}'"));
                $this->sessions->user = $username;
                success("登录成功","/website/index");
            }else{
                error("登录失败");
            }
        }else{
            $this->getView()->assign("xxx", "yyy");
        }
    }
    //注册
    public function registerAction() {//默认Action
        if($this->getRequest()->isPost()){
            Dispatcher::getInstance()->autoRender(false);
            $code = $_SESSION['code'];
            if($code == 1){
                $usersigin = sprintf("%06d",addslashes(post("sign")));
                if(!empty($usersigin)){
                    $usersiginres = $this->db->field("id")
                        ->table("y_user")
                        ->where("sign = '{$usersigin}'")
                        ->find();
                    if(!empty($usersiginres)){
                        $data['username'] = addslashes(post('username'));
                        $data['userpass'] = md5(addslashes(post('userpass')));
                        $data['tel'] = post('username');
                        $sign = $usersiginres['id'];
                        $data['floor_id'] =  $sign; //$this->get_user_floor_id($sign);
                        $data['create_time'] = time();
                        $data['ip'] = $_SERVER["REMOTE_ADDR"];
                        $bool = $this->db->action($this->db->insertSql("user",$data));
                        if($bool){
                            $user_id =  $this->db->getInsertId();
                            $result = $this->db->field("*")
                                ->table("y_token")
                                ->where("u_id = 0")
                                ->find();
                            $user['token_address'] = $result['token_addr'];
                            $user['usdt_address'] = $result['usdt_addr'];
                            $user['xyt_address'] = $result['xyt_addr'];
                            $user['btc_address'] = $result['btc_addr'];
                            $user['sign'] = sprintf("%06d",$user_id);//rand("000000","999999");
                            $token['u_id'] = $user_id;
                            $this->db->action($this->db->updateSql("user",$user,"id = {$user_id}"));
                            $this->db->action($this->db->updateSql("token",$token,"token_id = {$result['token_id']}"));
                            /**则创建 */
                            $add_group['guid'] = $user_id;
                            $add_group['users'] = "{$user_id}";
                            $add_group['create_time'] = time();
                            $this->db->action($this->db->insertSql("group",$add_group));
                            /** 更新团队 */
                            $this->updateGroup($usersiginres['id'], $user_id);
                            statusUrl($bool,"注册成功","/index/login","注册失败,账户异常");
                        }else{
                            error("注册失败,用户已经存在");
                        }
                    }else{
                        error("邀请码错误");
                    }
                }else{
                    error("缺少邀请码");
                }
            }else{
                error("短信验证失败");
            }
        }else{
            $code = isset($_GET['invitation_code'])?$_GET['invitation_code']:"";
            $this->getView()->assign("code", $code);
        }
    }

    public function updateGroup($uid, $add_user_id, $level = 1)
    {
        /** 超过两级不做处理 */
        if ($level > 3){
            return true;
        }
        /** 获取上级团队 */;
        $group = $this->db->field("id,guid,users")
            ->table("y_group")
            ->where("guid = {$uid}")
            ->find();
        if (empty($group) && $level == 1){
            /** 没有则创建 */
            $add_group['guid'] = $uid;
            $add_group['users'] = "{$uid},{$add_user_id}";
            $add_group['create_time'] = time();
            $this->db->action($this->db->insertSql("group",$add_group));
        }else{
            /** 有则更新 */
            $group_user = explode(",", $group['users']);
            if (!in_array($add_user_id, $group_user)){
                $group_up['users'] = "{$group['users']},{$add_user_id}";
                $this->db->action($this->db->updateSql("group",$group_up,"id = {$group['id']}"));
            }
        }
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $puid = $user['floor_id'];
        if (empty($puid) || $puid == 0){
            return true;
        }
        $this->updateGroup($puid, $add_user_id, $level+1);
    }

    public function groupTop()
    {
        $sql = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
        $topRes = $this->db->action($sql);
        return $topRes;
    }

    public function inviteidGroup($uid, $add_user_id, $level = 1){
        /** 超过两级不做处理 */
        if ($level > 2){
            return true;
        }
        /** 获取上级团队 */;
        $group = $this->db->field("id,guid,users")
            ->table("y_inviteid")
            ->where("inviteid = {$uid}")
            ->find();
        if (empty($group) && $level == 1){
            /** 没有则创建 */
            $add_group['inviteid'] = $uid;
            $add_group['users'] = "{$uid},{$add_user_id}";
            $add_group['create_time'] = time();
            $this->db->action($this->db->insertSql("inviteid",$add_group));
        }else{
            /** 有则更新 */
            $group_user = explode(",", $group['users']);
            if (!in_array($add_user_id, $group_user)){
                $group_up['users'] = "{$group['users']},{$add_user_id}";
                $this->db->action($this->db->updateSql("inviteid",$group_up,"id = {$group['id']}"));
            }
        }
        $user = $this->db->field("floor_id")
            ->table("y_user")
            ->where("id = {$uid}")
            ->find();
        $puid = $user['floor_id'];
        if (empty($puid) || $puid == 0){
            return true;
        }
        $this->inviteidGroup($puid, $add_user_id, $level+1);
    }

    public function activityajaxAction(){
        $usdt = post('usdt');
        $data = post('data');
        $type = post('type');
        $dis = post('dis');
        $num = post('num');
        $user = $this->sessions->user;
        $this->db->beginTransaction();
        //写入活动表
        $activitymodel = new activityModel();
        $get_user = $activitymodel->get_user($user);
        $result = $this->db->field("*")
            ->table("y_user_discount")
            ->where("dis_id = {$num} and u_id = {$get_user['id']}")
            ->find();
        if(empty($result)){
            $data_dis['u_id'] = $get_user['id'];
            $data_dis['dis_id'] = $num;
            $data_dis['dis_price'] = ($type == 'sys')?$usdt:0;
            $data_dis['dis_amount'] = 0;
            $data_dis['dis_system_price'] = 0;
            $data_dis['dis_income'] = "7.06%";
            $data_dis['link_price'] = ($type == 'open')?$usdt:0;
            $bool1 = $this->db->action($this->db->insertSql("user_discount",$data_dis));
        }else{
            $where = ($type == 'sys')?['dis_price'=>$usdt]:['link_price'=>$usdt];
            $bool1 =$this->db->action($this->db->updateSql("user_discount",$where,"u_id = {$get_user['id']} and dis_id = {$num}"));
        }
        //同步用户数据
        $C = $get_user['usdt_num'] - $data;
        $userArr['usdt_available_balance'] = $data;  
        $userArr['usdt_freeze_balance'] = $C;
        $bool2 = $this->db->action($this->db->updateSql("user",$userArr,"username = '{$user}'")); //出仓
        //日志记录
        $log['user_id'] = $get_user['id'];
        $log['type'] = ($type=='sys')?"DYX拼多多手动预约":"DYX拼多多连续预约";
        $log['info'] = ($usdt==0)?"撤销".$dis."个代币":"成功".($usdt-$dis)."个代币";
        $log['create_time'] = time();
        $bool3 = $this->db->action($this->db->insertSql("user_num_log",$log));
        //进仓
        $message = 0;
        if($bool1 && $bool2 && $bool3){
            $this->db->commit();
            $message = 1;
        }else{
            $this->db->rollback();
            $message = 0;
        }
        echo json_encode(['code'=>$message]);
        exit;
    }

    //退出
    public function logoutAction(){
        $this->sessions->del("user");
        success("退出成功","/Website/index");
    }

    public function logAction(){
        $url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=0xe4412afb082b51b185acf2b421842465eac96103&address=0xc8ee031cc8a0aaba269fc8d24e08f7577cd968b0&tag=latest&apikey=XYT";
        $str = curl_get($url);
        print_r($str);
        exit;
        //liunx curl json post data
        //curl -H "Content-Type:application/json" localhost:8545 -X POST --data '{"jsonrpc":"2.0","method":"eth_protocolVersion","params":[],"id":67}'
        //curl -H "Content-Type:application/json" localhost:8545  -X POST --data '{"jsonrpc":"2.0","method":"eth_protocolVersion","params":[],"id":1337}'
    }

    public function qrcode($eth_addr){
        include APP_PATH."/vendor/phpqrcode/phpqrcode.php";
        $url = "/public/eth_addr/".$eth_addr.".png";
        $path = APP_PATH.$url;
        @chmod($path,0777);
        QRcode::png($eth_addr,$path,"L",6,1);
        return $url;
    }

    public function codeAction(){
        Dispatcher::getInstance()->autoRender(false);
        include APP_PATH."/application/core/Image.php";
        header("content-type:image/png");
        \app\core\Image::code(160,45,20,15,35,30,APP_PATH."/public/fonts/msyhbd.ttc");
    }

    public function user_sign($user_id){
        $uid = sprintf("%06d",$user_id);
        $iqidstr = substr(uniqid(), 7, 13);
        return substr($iqidstr, 0, 3).$uid.substr($iqidstr, 3, 3);
    }

    public function get_user_floor_id($data){
        $userdata = addslashes($data);
        $result = $this->db->field("id")
            ->table("y_user")
            ->where("sign = '{$userdata}'")
            ->find();
        if(!empty($result)){
            return $result['id'];
        }else{
            return 0;
        }
    }
    //手机验证码验证
    public function ajaxsmsAction(){
        $phone = post('tel');
        $code = post('lockcode');
        $locktime = time();
        $telsvcode = $this->db->field("*")->table("y_short_message")->where(" phone = '{$phone}' and code = '{$code}' ")->find();
        if($telsvcode){
            $datatime = $telsvcode['create_time'];
            if( ($locktime - $datatime) < 6000){
                $datacode = $telsvcode['code'];
                if($datacode == $code){
                    $this->sessions->code = 1;
                    echo json_encode(["code"=>0,"message"=>"验证码成功"]);
                }else{
                    echo json_encode(["code"=>1002,"message"=>"验证码错误"]);
                }
            }else{
                echo json_encode(["code"=>1005,"message"=>"验证码过期"]);
            }
        }else{
            echo json_encode(["code"=>1002,"message"=>"验证码错误"]);
        }
        exit;
    }

    public function telcodeAction(){
        $tel = addslashes(post('tel'));
        $result = $this->db->field("*")
            ->table("y_user")
            ->where("username = {$tel}")
            ->find();
        if(!empty($result)){
            echo json_encode(['code'=>2,"message"=>"账户已经存在，请直接登录"]);
            exit;
        }else{
            include APP_PATH."/application/core/Sms.php";
            $sms = new \app\core\Sms();
            $rand = rand(100000,999999);
            $code = $sms->smscode($tel,$rand);
            if($code['Message']=='OK'){
                $data['phone'] = $tel;
                $data['code'] = $rand;
                $data['create_time'] = time();
                $data['status'] = ($code['Code'] == "OK ")?1:0;
                $this->db->action($this->db->insertSql('short_message',$data));
                echo json_encode(['code'=>1,"message"=>"发送成功"]);
                exit;
            }else{
                echo json_encode(['code'=>0,"message"=>"系统繁忙请稍后再试"]);
                exit;
            }
        }
    }

    public function pwdtelcodeAction(){
        $tel = addslashes(post('tel'));
        $result = $this->db->field("*")
            ->table("y_user")
            ->where("username = {$tel}")
            ->find();
        if(empty($result)){
            echo json_encode(['code'=>2,"message"=>"账户不存在，请注册"]);
            exit;
        }else{
            include APP_PATH."/application/core/Sms.php";
            $sms = new \app\core\Sms();
            $rand = rand(100000,999999);
            $code = $sms->smscode($tel,$rand);
            if($code['Message']=='OK'){
                $data['phone'] = $tel;
                $data['code'] = $rand;
                $data['create_time'] = time();
                $data['status'] = ($code['Code'] == "OK ")?1:0;
                $this->db->action($this->db->insertSql('short_message',$data));
                echo json_encode(['code'=>1,"message"=>"发送成功"]);
                exit;
            }else{
                echo json_encode(['code'=>0,"message"=>"系统繁忙请稍后再试"]);
                exit;
            }
        }
    }
    //忘记密码
    public function forgetpwdAction(){
        if($this->getRequest()->isPost()){
            Dispatcher::getInstance()->autoRender(false);
            $tel = addslashes(post("username"));
            $pass = md5(addslashes(post('userpass')));
            $bool = $this->db->action($this->db->updateSql('user',["userpass"=>$pass],"username = '{$tel}'"));
            statusUrl($bool,"重置密码成功","/index/login","重置密码失败");
        }else{
            $this->getView()->assign("content", "Hello2 World");
        }
    }
    //查看排行榜
    public function ranklistAction(){
        if(empty($this->sessions->user)) {
            success("请先登录", "/index/login");
        }else {
            $user = $this->sessions->user;
            include APP_PATH."/application/core/Page.php";
            $page = new \app\core\Page();
            $sql = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
            $len = count($this->db->action($sql));
            $page->init($len,10);
            $showstr = $page->mediashow();
            $sqlpage = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC {$page->limit}";
            $page = $this->db->action($sqlpage);
            $this->getView()->assign([
                "arrdata"=>$page,
                "showstr"=>$showstr,
                "user" => $user
            ]);
        }
    }

    public function ajaxpwdAction(){
        $tel = addslashes(post('tel'));
        $pass = md5(addslashes(post('locapass')));
        $result = $this->db->field("*")
            ->table("y_user")
            ->where("username = '{$tel}' and userpass = '{$pass}'")
            ->find();
        if(!empty($result)){
            echo json_encode(["code"=>1,"message"=>"原密码通过"]);
        }else{
            echo json_encode(["code"=>2,"message"=>"原密码错误"]);
        }
        exit;
    }

    public function probAction($top){
        $num = $this->db->zscount("user");
        $sum=0;
        for($i=1;$i<=$num;$i++)
        {
            $sum+=$i;
        }
        echo $sum;
    }

    public function emptyAction()
    {
        // TODO: Implement __call() method.
    }



}
?>