<?php
use \Yaf\Bootstrap_Abstract;
use \Yaf\Application;
use \Yaf\Dispatcher;
use \Yaf\Loader;
use \Yaf\Registry;
class Bootstrap extends Bootstrap_Abstract
{

    /**
     * 加载公共函数库
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initFunction(Dispatcher $dispatcher)
    {
        Loader::import('function/helper.php');
        import('common');

    }

    /**
     * 加载数据库
     */

    public function _initDatabase(Dispatcher $dispatcher)
    {
        \think\Db::setConfig();

    }

    /**
     * 设置缓存配置
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initCache(Dispatcher $dispatcher)
    {

        $cache = \think\Cache::init();
        Registry::set('cache', $cache);

    }
}