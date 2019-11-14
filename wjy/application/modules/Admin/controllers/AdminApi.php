<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/9/7
 * Time: 14:22
 */

include "Base.php";

class AdminApi extends Base
{
    /**
     * 初始化 REST 路由
     * 修改操作 和 绑定参数
     *
     * @access protected
     */
    protected function init()
    {
        parent::init();

        $request = $this->_request;



    }
}
