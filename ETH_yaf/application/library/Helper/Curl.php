<?php
namespace Helper;
use Yaf\Application;
error_reporting(0);
class Curl
{
    /**
     * @var string / | http://xxx.com/
     */
    private static $baseUrl = '/index.php/';
    /**
     * @var string .html
     */
    private static $urlSuffix = '';


    /**
     * Create URL
     * 当控制器存在 createUrl() 方法时则优先调用控制器内部的，如果没有则调用默认的
     * @param string $route
     * home  (HomeController, Index Action)
     * home/index  (HomeController, Index Action)
     * m/home/index  (M Module, HomeController, Index Action)
     * @param array $params
     * @param string $ampersand
     * @return string
     */
    public static function createUrl($route, $params=array(), $ampersand='&')
    {
        $controller = '';
        if(strpos($route, '/')){
            $routeArr = explode('/', $route);
            $controller = count($routeArr) > 2 ? ucfirst($routeArr[1]) .'Controller' : ucfirst($routeArr[0]) .'Controller';
        }else{
            $controller = ucfirst($route) .'Controller';
        }

        if(class_exists($controller) && method_exists($controller, 'createUrl'))
            return $controller::createUrl($route, $params, $ampersand); //也可配置规则路由实现，避免在多个控制器内添加静态方法
        else
            return self::createUrlDefault($route, $params, $ampersand);
    }


    /**
     * URL
     * @param string $route
     * @param array $params
     * @param string $ampersand
     * @return string
     */
    public static function createUrlDefault($route, $params=array(), $ampersand='&')
    {
        $request = Application::app()->getDispatcher()->getRequest();
        $route = trim($route,'/');
        if($route==='')
            $route = $request->controller . '/' . $request->action;
        //elseif(strpos($route, '/')===false)
        //    $route = $request->controller . '/' . $route;

        /*
        //下面 createPathInfo 中过滤 param=null 的参数,保留参数可用 param=''
        foreach($params as $i=>$param)
            if($param===null)
                $params[$i]='';
        */
        if(isset($params['#']))
        {
            $anchor='#'.$params['#'];
            unset($params['#']);
        }
        else
            $anchor='';

        $url = self::$baseUrl . $route . self::$urlSuffix;
        $pstr = self::createPathInfo($params, '=', $ampersand);
        if($pstr || $anchor)
            $url .= '?' . $pstr . $anchor;

        return $url;
    }


    /**
     * Params
     * @return string  param1=val1¶m2=val2
     */
    private static function createPathInfo($params, $equal, $ampersand)
    {
        $pairs = array();
        foreach($params as $k => $v)
        {
            if (is_array($v))
                $pairs[]=self::createPathInfo($v,$equal,$ampersand);
            elseif ($v !== null) //else
                $pairs[]=urlencode($k).$equal.urlencode($v);
        }
        return implode($ampersand,$pairs);
    }
}