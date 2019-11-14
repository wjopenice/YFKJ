<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

use think\Db;

include "Base.php";

class AuthController extends Base
{


    public function loginAction()
    {

        $username = input('username');
        $password = input('password');
        if (empty($username))
        {
            $this->error('请输入姓名');
        }

        if (empty($password))
        {
            $this->error('请输入密码');
        }

        $admin = Db::name('admin_user')->where(['username' => $username])->find();
        if (empty($admin))
        {
            $this->error('当前用户不存在');
        }

        if (md5($password) != $admin['password'])
        {
            $this->error('密码错误');
        }
        if ($admin['status'] != 1)
        {
            $this->error('当前用户不可用');
        }

        $token = md5(sha1($admin['username'].$admin['password'].session_id()));
        cache('Auth_'.$token, null);
        cache('Auth_'.$token, $admin, \beans\CacheTime::LOGIN_TIME);
        Db::name('admin_user')->where(['id' => $admin['id']])->update(['token' => $token]);
        $this->success(['token' => $token]);


    }


    public function logoutAction()
    {
         $token = $this->header['token'];
         if (empty($token))
         {
            $this->response(5008, '非法令牌');
         }
        Db::name('admin_user')->where(['token' => $token])->update(['token' => '']);

         cache('Auth_'.$token, null);
        $this->success();
    }


    public function infoAction()
    {
        $token = input('token');

        if (empty($token))
        {
            $this->response(5008, '非法令牌');
        }
        $cash = cache('Auth_'.$token);
//        if (empty($cash))
//        {
//            $this->response(50014, '令牌已过期');
//        }
        $admin = Db::name('admin_user')->where(['token' => $token])->field('id,username,avatar')->find();
        if (empty($admin))
        {
            $this->response(50012, '其他客户端已登录');
        }

        if (empty($admin['avatar']))
        {
            $admin['avatar'] = tomedia('static/avatar.gif');
        }
        $this->success($admin);
    }

}
