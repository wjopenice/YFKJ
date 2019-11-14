<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/API/models/Currency.php';

class IndexController extends Rest
{

    /**
     * 首页 获取所有币种
     */
    public function GET_currencyListAction()
    {
        $name = input('keyword');
        $currency_model = new CurrencyModel();
        $list = $currency_model->getList($name);
        return $this->success($list);
    }



}