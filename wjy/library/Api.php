<?php

/**
 * 基于thinkphp 5.1版本的封装抽取
 * Date: 2018\2\19 0019 15:42
 *
 */
class Api extends Controller
{

    /**
     * @var param 参数
     */
    protected $param;

    /**
     * 默认响应输出类型,支持json/xml
     * @var string
     */
    protected $responseType = 'json';

    protected function init()
    {
        parent::init();
        $request = $this->getRequest();
        //$this->request->filter('trim,strip_tags,htmlspecialchars');
        $param =  $request->getParams();
        $this->param = $param;
    }


    public function object_array($array)
    {
        if (is_object($array)) {
            $array = (array)$array;
        }
        if (is_array($array)) {
            foreach ($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $this->request->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 操作成功返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为1
     * @param string $type   输出类型
     */
    protected function success($msg = '', $data = null, $code = 1, $type = null)
    {
        $this->result($msg, $data, $code, $type);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null)
    {
        $this->result($msg, $data, $code, $type);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @return json
     */
    protected function result($msg, $data = null, $code = 0, $type = 'json')
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        if ($type == 'json')
        {
              $result = json_encode($result);
        }

        exit($result);
    }
}