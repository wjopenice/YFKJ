<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class LoginController extends Controller_Abstract{
    const Login_Key = "BplVQpWfm5cjuKmbiKUvQqdErqhAp8vG";
    public $db;
    public $sessions;
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }
    public function indexAction(){
        if($this->getRequest()->isPost()){
            Dispatcher::getInstance()->autoRender(false);
            $data['user'] = post("user");
            $pass = $this->hashkey(post("pass"),self::Login_Key);
            $result = $this->db->field("*")
                ->table("y_system")
                ->where("user = '{$data['user']}' and pass = '{$pass}' and status = 1")
                ->find();
            if($result){
                $data['create_time'] = time();
                $data['ipaddress'] = server("REMOTE_ADDR");
                $this->db->action($this->db->insertSql("system_log",$data));
                $this->sessions->username = $data['user'];
                success("LOGIN OK","/admin/index");
            }else{
                error("LOGIN ERROR");
            }
        }else{
            $this->getView()->assign("xxxx", "yyyy");
        }
    }

    public function logoutAction(){
        $this->sessions->del('username');
        success("LOGIN OUT OK","/login/index");
    }

    public function testAction(){

        echo $this->hashkey("Yunfan123456",self::Login_Key);
        exit;
    }

    public function logAction(){
//        $key = $this->hashkey("123",self::Login_Key);
//        $strpath = $this->qrcode($key);
//        echo "<img src='".$strpath."'>";

        $url = "https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=0xe4412afb082b51b185acf2b421842465eac96103&address=0xc8ee031cc8a0aaba269fc8d24e08f7577cd968b0&tag=latest&apikey=XYT";
        $str = curl_get($url);
        print_r($str);

        exit;
        //$this->getView()->assign("xxxx", "yyyy");
    }

    public function qrcode($eth_addr){
        include APP_PATH."/vendor/phpqrcode/phpqrcode.php";
        $url = "/public/eth_addr/".$eth_addr.".png";
        $path = APP_PATH.$url;
        @chmod($path,0777);
        QRcode::png($eth_addr,$path,"L",6,1);
        return $url;
    }

    public function hashkey($data,$key){
        return hash_hmac("sha256",$data,$key);
    }

    public function user_sign($user_id){
        $uid = sprintf("%05d",$user_id);
        $iqidstr = substr(uniqid(), 7, 13);
        return substr($iqidstr, 0, 3).$uid.substr($iqidstr, 3, 3);
    }

    public function emptyAction()
    {
        // TODO: Implement __call() method.
    }
}