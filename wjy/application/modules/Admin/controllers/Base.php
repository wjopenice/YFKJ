<?php
/**
 * REST 控制器
 * Date: 2018\2\23 0023 14:10
 *
 */

use beans\ErrorCode;
use think\Db;

class Base extends Controller
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
     * 配置信息
     *
     * @access private
     *
     * @var array
     */
    private $_config;

    public $token;

    public $admin;

    protected $header;

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
        $this->header = $REQUEST->header();
        /*请求来源，跨站cors响应*/
        $rest_info = Config::get('rest');
        $cors = isset($rest_info['cors']) ? $rest_info['cors'] : '';
        if ($cors) {
            $this->corsHeader($cors);
        }
        $this->_config = $rest_info['rest'];
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

    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
         $lng_common =  Lang::load(APP_MODULES .'/api/lang/zh-cn/common.php');
         $lng_model = Lang::load(APP_MODULES .'/api/lang/zh-cn/' . str_replace('.', '/', $name) . '.php');
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
            $this->_config['code'] => $status,
            $this->_config['msg'] => is_string($data) ? $data : $data['error'],
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

        $this->response = array(
            $this->_config['code'] => 20000,
            $this->_config['msg'] => $msg ? $msg : '操作成功',
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
        $this->response = array(
            $this->_config['code'] => 20001,
            $this->_config['msg'] => $msg ? $msg : '操作失败',
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



}
