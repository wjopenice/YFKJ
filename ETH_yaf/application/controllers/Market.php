<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Session;
class MarketController extends Controller_Abstract{
    public $db;
    public $sessions;
    const Login_Key = "BplVQpWfm5cjuKmbiKUvQqdErqhAp8vG";
    public function init(){
        $this->db = new dbModel();
        $this->sessions= Session::getInstance();
    }
    //登录
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
                success("登录成功","/operate/index");
            }else{
                error("登录失败");
            }
        }else{
            $this->getView()->assign("xxxx", "yyyy");
        }
    }
    //退出
    public function logoutAction(){
        $this->sessions->del('username');
        success("退出成功","/market/index");
    }
    public function emptyAction()
    {
        // TODO: Implement __call() method.
    }
    public function hashkey($data,$key){
        return hash_hmac("sha256",$data,$key);
    }

}