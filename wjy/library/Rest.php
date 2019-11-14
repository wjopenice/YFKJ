<?php
/**
 * REST 控制器
 * Date: 2018\2\23 0023 14:10
 *
 */

use beans\ErrorCode;
use think\Db;

class Rest extends Controller
{

    /**
     * 完成响应数据
     *
     * @var array
     */
    protected $response = false; //自动返回数据

    /**
     * 响应状态码
     *
     * @var int
     */
    protected $code = 200;

    /**
     * 语言包
     *
     * @var array
     */
    protected $lang;

    /**
     * 配置信息
     *
     * @access private
     *
     * @var array
     */
    private $_config;


    public $token;

    public $sessionId;

    public $sign;

    public $user;

    public $uid;

    /**
     * 结束时自动输出信息
     */
    public function __destruct()
    {
        if ($this->response !== false) {
            $response = $this->_response;
            $response->setHeader('Content-Type', 'application/json;charset=utf-8', true, $this->code);
            $response->setBody(json_encode($this->response, $this->_config['json']));
            $response->response();
        }
    }

    /**
     * 初始化 REST 路由
     * 修改操作 和 绑定参数
     *
     * @access protected
     */
    protected function init()
    {
        parent::init();
        Yaf_Dispatcher::getInstance()->disableView(); //立即输出响应，并关闭视图模板

        $request = $this->_request;

        $REQUEST = new Request();

        session_start();
        if (!empty($REQUEST->header()['token']))
        {
            $this->token = $token = $REQUEST->header()['token'];
        }
        if (!empty($REQUEST->header()['sessionid']))
        {
            $this->sessionId = $sessionId = $REQUEST->header()['sessionid'];
        }
        if (!empty($REQUEST->header()['sign']))
        {
            $this->sign = $REQUEST->header()['sign'];
        }
        $cache = cache('Auth_'.$token);

        //echo $REQUEST->header()['sessionid'];die();
        /*请求来源，跨站cors响应*/
        $rest_info = Config::get('rest');
        $cors = isset($rest_info['cors']) ? $rest_info['cors'] : '';
        if ($cors) {
            $this->corsHeader($cors);
        }
        /*请求操作判断*/
        $method = $request->getMethod();
        $type = $request->getServer('CONTENT_TYPE');
        if ($method === 'OPTIONS') {
            /*cors 跨域header应答,只需响应头即可*/
            exit();
        } elseif (strpos($type, 'application/json') === 0) {


            /*json 数据格式*/
            if ($inputs = file_get_contents('php://input')) {
                $input_data = json_decode($inputs, true);
                if ($input_data) {
                    $GLOBALS['_' . $method] = $input_data;
                } else {
                    parse_str($inputs, $GLOBALS['_' . $method]);
                }
            }

        } elseif ($method === 'PUT' && ($inputs = file_get_contents('php://input'))) {
            /*直接解析*/
            parse_str($inputs, $GLOBALS['_PUT']);
        }

        /*Action路由*/
        $action = $request->getActionName();
        $this->_config = $rest_info['rest'];
        if (is_numeric($action)) {
            /*数字id绑定参数*/
            $request->setParam($this->_config['param'], intval($action));
            //提取请求路径
            $path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] :
                strstr($_SERVER['REQUEST_URI'] . '?', '?', true);
            $path = substr(strstr($path, $action), strlen($action) + 1);
            $action = $path ? strstr($path . '/', '/', true) : $this->_config['action'];
        }

        $rest_action = $method . '_' . $action; //对应REST_Action

        /*检查该action操作是否存在，存在则修改为REST接口*/
        if (method_exists($this, $rest_action . 'Action')) {
            /*存在对应的操作*/
            $request->setActionName($rest_action);
        } elseif (!method_exists($this, $action . 'Action')) {
            /*action和REST_action 都不存在*/
            if (method_exists($this, $this->_config['none'] . 'Action')) {
                $request->setActionName($this->_config['none']);
            } else {
                $info = array(
                    'error' => '未定义操作',
                );
                if (APP_ENV == 'develop') {
                    $dev_info = array(
                        'method' => $method,
                        'action' => $action,
                        'controller' => $request->getControllerName(),
                        'module' => $request->getmoduleName()
                    );
                    $info = array_merge($info, $dev_info);
                }
                $this->response(ErrorCode::ERROR_ACTION, $info, 404);
            }
        } elseif ($action !== $request->getActionName()) {
            /*修改后的$action存在而$rest_action不存在,绑定参数默认控制器*/
            $request->setActionName($action);
        }

        /** 如果存在刷新token过期时间 */
        if ($token && $sessionId && $cache)
        {
            cache('Auth_'.$token, $cache, Jwt::$keeptime);
        }

        // 验证签名
        $whiteList = ['avatar', 'wechat', 'cooperation'];
        if (!in_array($action, $whiteList))
        {
            $this->checkSign(input('param.'));
        }

        /** 需要验证的路由 */
        $controller = $request->controller;
        $this->loadlang($controller);
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
         $lng_common =  Lang::load(APP_MODULES .'/Api/lang/zh-cn/common.php');
         $lng_model = Lang::load(APP_MODULES .'/Api/lang/zh-cn/' . str_replace('.', '/', $name) . '.php');
        $this->lang = array_merge($lng_common, $lng_model);

    }

    /**
     * 设置返回信息，立即返回
     *
     * @access protected
     *
     * @param int $status 返回状态
     * @param mixed $data 返回数据
     * @param int $code 可选参数，设置响应状态吗
     */
    protected function response($status, $data = null, $code = null)
    {
        $this->response = array(
            $this->_config['status'] => $status,
            $this->_config['msg'] => is_string($data) ? $this->lang[$data] : $this->lang[$data['error']],
            $this->_config['data'] => $data,
        );
        ($code > 0) && $this->code = $code;
        exit();
    }

    /**
     * 快速返回成功信息(status为1)
     *
     * @access protected
     *
     * @param mixed $data 返回数据内容
     * @param int $code 设置状态码[默认200]
     */
    protected function success($data = null, $msg = '', $code = 200)
    {

        $msg =  $msg ? $msg : $this->lang['operation completed'];
        $msg =  $this->lang[$msg] ? $this->lang[$msg] : $msg;
        $this->response = array(
            $this->_config['status'] => 1,
            $this->_config['msg'] => $msg,
            $this->_config['data'] => $data,
        );
        $this->code = $code;
        exit();
    }

    /**
 * 快速返回失败信息(status为0)
 *
 * @access protected
 *
 * @param mixed $data 返回数据内容
 * @param int $code 设置状态码[默认200]
 */
    protected function error($msg = '', $data = null, $code = 200)
    {
        $msg =  $msg ? $msg : $this->lang['operation failed'];
        $msg =  $this->lang[$msg] ? $this->lang[$msg] : $msg;
        $this->response = array(
            $this->_config['status'] => 0,
            $this->_config['msg'] => $msg,
            $this->_config['data'] => $data,
        );
        $this->code = $code;
        exit();
    }

    /**
     * 快速返回请提示信息(status为20001)
     *
     * @access protected
     *
     * @param mixed $data 返回数据内容
     * @param int $code 设置状态码[默认200]
     */
    protected function toast($msg = '', $data = null, $code = 200)
    {
        $msg =  $msg ? $msg : $this->lang['operation failed'];
        $msg =  $this->lang[$msg] ? $this->lang[$msg] : $msg;
        $this->response = array(
            $this->_config['status'] => 20001,
            $this->_config['msg'] => $msg,
            $this->_config['data'] => $data,
        );
        $this->code = $code;
        exit();
    }

    /**
     * 快速返回请提示信息(status为20001)
     *
     * @access protected
     *
     * @param mixed $data 返回数据内容
     * @param int $code 设置状态码[默认200]
     */
    protected function warning($msg = '', $data = null, $code = 200)
    {
        $msg =  $msg ? $msg : $this->lang['operation failed'];
        $msg =  $this->lang[$msg] ? $this->lang[$msg] : $msg;
        $this->response = array(
            $this->_config['status'] => 30001,
            $this->_config['msg'] => $msg,
            $this->_config['data'] => $data,
        );
        $this->code = $code;
        exit();
    }

    /**
     * CORS 跨域请求响应头处理
     *
     * @param array $cors CORS配置
     * @access private
     */
    private function corsHeader(array $cors)
    {

        //请求来源站点
        $from = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] :
            (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        if ($from) {
            $domains = $cors['Access-Control-Allow-Origin'];
            if ($domains !== '*') {//非通配
                $domain = strtok($domains, ',');
                while ($domain) {
                    if (strpos($from, rtrim($domain, '/')) === 0) {
                        $cors['Access-Control-Allow-Origin'] = $domain;
                        break;
                    }
                    $domain = strtok(',');
                }
                if (!$domain) {
                    /*非请指定的求来源,自动终止响应*/
                    header('Forbid-Origin: ' . $from);
                    return;
                }
            } elseif ($cors['Access-Control-Allow-Credentials'] === 'true') {
                /*支持多域名和cookie认证,此时修改源*/
                $cors['Access-Control-Allow-Origin'] = $from;
            }
            foreach ($cors as $key => $value) {
                header($key . ': ' . $value);
            }
        }
    }


    /** 验证签名 */
    public function checkSign($param = [])
     {
          foreach ($param as $key => $item)
          {
             if (strpos($key,'/api/') !==false)
             {
                 unset($param[$key]);
             }
          }
         $sign = Tool::bulidApiSign($param);
          //echo $sign;die();
         if ($sign != $this->sign)
         {
             $this->error('验签失败');
         }
     }

     public function getUid()
     {
         if (empty($this->token)) {

             return false;
         }
         $userInfo = Db::table('user')->where('token', $this->token)->field('uid,username,nickname')->find();

         if (!$userInfo)
         {
             return false;
         }

         $this->user = $userInfo;
         $this->uid = $userInfo['uid'];
     }
    /** 公用检查登录页面 */
    public function checkLogin()
    {

        $cache = cache('Auth_'.$this->token);
//        if (empty($this->token) || empty($this->sessionId) || empty($cache))
//        {
//            $this->response(ErrorCode::NOT_LOGIN, 'logon invalidation');
//        }

//        if (empty($this->token) || empty($cache)) {
//
//            $this->response(ErrorCode::NOT_LOGIN, 'logon invalidation');
//        }
//        if ($this->sessionId != session_id())
//        {
//            $this->response(ErrorCode::NOT_LOGIN,'logon illegal');
//        }

        if (empty($this->token)) {

            $this->response(ErrorCode::NOT_LOGIN, 'logon invalidation');
        }
        $userInfo = Db::table('user')->where('token', $this->token)->field('uid,username,nickname')->find();

        if (!$userInfo)
        {
            $this->response(ErrorCode::NOT_LOGIN,'logon error');
        }

        $this->user = $userInfo;
        $this->uid = $userInfo['uid'];
        $this->addActiveUser();
    }

    /** 增加日活跃用户 */
    public function addActiveUser()
    {
        $active = Db::name('active')->whereTime('create_time', 'today')->find();
        if (empty($active))
        {
            $active_add['users'] = $this->uid;
            $active_add['create_time'] = time();
            $active_add['update_time'] = time();
            Db::name('active')->insert($active_add);

        } else
        {
             /** 判断是否包含 */
            $user_arr = explode(',', $active['users']);
            if (!in_array($this->uid, $user_arr))
            {
                $active_up['users'] = $active['users'].','.$this->uid;
                $active_up['update_time'] = time();
                Db::name('active')->where('id', $active['id'])->update($active_up);
            }

        }
    }
}
