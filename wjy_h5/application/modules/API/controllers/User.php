<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/API/models/User.php';
include APP_MODULES.'/API/models/Wallet.php';

class UserController extends Rest
{

    /**
     * 用户 注册
     */
    public function Post_registerAction()
    {

        $invite_code = input('inviteCode');
        if (empty($invite_code)) {

            /* 没有invite_code错误 */
            return $this->error('please input a inite code');
        }
        /* 获取对应用户 */
        $user_model = new UserModel();
        $user =  $user_model->getUserByInviteCode($invite_code);
        if (empty($user)){

            /** 未找到用户信息错误 */
            return $this->error('cant not find userinfo');
        }

        /* 创建新用户  */
        $res = $user_model->addUser($user);
        if (is_error($res))
        {
            return $this->error();
        }

        return $this->success($res);

    }

    /**
     * 用户 登录
     */
    public function Post_loginAction()
    {
        $where['username'] = input('username');
        $where['password'] = input('password');
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('add')->check($where)) {

            $this->error($user_validate->getError());
        }

        $user_model = new UserModel();
        $res = $user_model->login($where);

        if (is_error($res))
        {
            /** 登录失败 */
            $this->error('login fail');
        }

        $this->success($res);


    }

    /**
     * 用户 退出
     */
    public function Post_logoutAction()
    {
        $this->checkLogin();
        $user_model = new UserModel();
        $res = $user_model->logout($this->token);
        $this->success();
    }


    /** 用户 个人资金 */
    public function Get_walletAction()
    {

        $this->checkLogin();
        $list = WalletModel::getUserWallet($this->uid);
        $this->success($list);

    }

    /** 用户 某个资金详情 */
    public function Get_currencyInfoAction()
    {
        $curreny_id = input('currency_id');
        if (empty($curreny_id))
        {
           $this->error('currency can not be empty');
        }
        $currency_info = WalletModel::getCurrencyInfo($this->uid, $curreny_id);
        $this->success($currency_info);
    }


    /** 用户 设置个人信息 */
    public function Post_setUserInfoAction()
    {
        $this->checkLogin();
        $where['nickname'] = input('nickname');
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('edit')->check($where)) {

            $this->error($user_validate->getError());
        }

        $user_model = new UserModel();
        $res = $user_model->setUserInfo($this->uid, $where);
        if ($res)
        {
            $this->success();
        }

        $this->error();
    }
}