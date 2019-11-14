<?php
class IndexController extends AppController {
    public function indexAction() {//默认Action
//        echo "<pre>";
//        ReflectionClass::export($this);
//        exit;

        //$this->getView()->assign('content',"Inde Hello3 World");
        //$this->getView()->display("index/index.phtml", ['content'=>"Inde Hello3 World"]);
        //echo $this->getView()->render("index/index.phtml", ['content'=>"Inde Hello3 World"]);

//        $ext = new \Helper\Ext();
//        setcookie("coo","coo",time()+60);
//        $_SESSION['abc'] = "abc";
//        p($this->session->abc);
//        p($this->getRequest()->get("abc"));

         //获取请求类型 $this->getRequest()->getMethod()
        //获取PATH_INFO参数与QUERY_STRING/支持GET/POST/COOKIE/SERVER参数  $this->getRequest()->get("参数")
        //获取PATH_INFO参数 $this->getRequest()->getParams()
        //获取文件信息 $this->getRequest()->getFiles()
        //获取session扩展AppController类实现  $this->session
        //获取cookie信息 $this->getRequest()->getCookie()
        //跳转 $this->forward("login");
        //响应 $this->getResponse()

        $method = $this->getRequest()->isGet();
        if(!$method){
            echo "post";
            exit;
        }else{
           // p($this->getRequest()->getRequestUri("index2"));
            //exit;
            $this->getView()->assign("");
        }
    }
    public function index2Action(){
        echo "123";
        return false;
    }
    public function testAction() {//默认Action
        $this->getView()->assign("content", "Inde Hello2 World");
    }
}
?>