<?php
use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
/**
 * 基于thinkphp 5.1版本的封装抽取
 * Date: 2018\2\19 0019 15:42
 *
 */

class Controller extends Controller_Abstract
{
    /**
     * @var Request Request 实例
     */
    public $request;

    protected function init()
    {
        $this->request = Request::instance();
    }
}