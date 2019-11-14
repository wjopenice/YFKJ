<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class UserModel extends Model
{
    /** 登录 */
    public function login($user_info)
    {

        $ip = getip();
        $username = $user_info['username'];
        $password = $user_info['password'];
        $password = secret($password, $username);


        $user = $this->where(['username' => $username, 'password' => $password])->field('uid,username,password')->find();

        /** 未找到用户 */
        if (empty($user)) {

            return error('未获取到用户信息');
        }

        $session_id = session_id();
        $token = md5(sha1($user['username'].$user['password'].$session_id));
        /** 更新token */
        $res = $this->updateToken($user['uid'], $token);
        /** 增加缓存信息 */
        if ($res) {

            cache('Auth_wtree'.$token, null);
            cache('Auth_wtree'.$token, $user);
            return ['token' => $token, 'sessionid' => $session_id];

        }

        return error('登录失败');

    }

    /** 退出登录 */
    public function logout($token)
    {
        $this->where('token', $token)->update(['token' => '', 'update_time' => time()]);
        cache('Auth_wtree'.$token, null);

        return true;
    }

    /** 通过邀请码获取用户信息 */
    public function getUserByInviteCode($code)
    {
        if (empty($code)) {

            return [];
        }
        return $this->where(['invite_code' => $code])->field('uid')->find();
    }

    /** 生成用户名 */
    public function getUserName($i = 0)
    {
        $username = getUserName();
        /* 验证是否存在 */
        $check = $this->where('username', $username)->find();
        if (empty($check)) {

            return $username;
        }
        if ($i > 50)
        {
            return error('get user name fail');
        }

        return $this->getUserName($i + 1);
    }

    /** 添加用户 */
    public function addUser($user, $where)
    {
        $user_add['username'] = $where['username'];
        $user_add['nickname'] = $where['username'];
        $user_add['avatar'] = '';
        $user_add['password'] = secret($where['password'], $user_add['username']);
        $user_add['invite_code'] = 0;
        $user_add['puid'] = $user['uid'];
        $user_add['create_time'] = time();
        $user_add['update_time'] = time();

        /** 获取eth地址 */
        $eth_res = Db::name('address')->where(['uid' => 0])->find();
        if (empty($eth_res))
        {
            $this->error('获取地址失败');
        }
        $eth_address = $eth_res['address'];
        /** 获取所有币种 */
        $currency = Db::name('currency')->where([])->field('id')->select();
        Db::startTrans();
        try {

            $uid = $this->insertGetId($user_add);
            $invite_code = 100000 + $uid;
            $this->where('uid', $uid)->setField('invite_code', $invite_code);
            /** 更新钱包 */
            $wallet_add['uid'] = $uid;
            $wallet_add['total'] = 0;
            $wallet_add['free'] = 0;
            $wallet_add['lock'] = 0;
            $wallet_add['consume'] = 0;
            $wallet_add['address'] = $eth_address;
            Db::name('address')->where(['id' => $eth_res['id']])->update(['uid' => $uid]);
            $wallet_add['create_time'] = time();
            $wallet_add['update_time'] = time();
            foreach ($currency as $key => $item)
            {
                $wallet_add['currency_id'] = $item['id'];
                Db::name('wallet')->insert($wallet_add);
            }
            Db::commit();
            return ['result' => 'success'];

        } catch (\Exception $e) {
            Db::rollback();
            return error('创建用户失败');
        }
    }

    /** 更新token */
    public function updateToken($uid, $token)
    {

        $res = $this->where('uid', $uid)->update(['token' => $token, 'update_time' => time()]);
        if ($res) {

            return true;
        }

        return false;
    }

    /** 获取用户uid 根据不同条件 */
    public function getUidByInviteCode($code)
    {
        return $this->where('invite_code', $code)->value('uid');

    }

    /** 获取财富树用户下的注册信息 */
    public function getTreeUserInfo($data = [])
    {
        foreach ($data as $key => $item)
        {
            foreach ($item as $key2 => $item2)
            {
                $userInfo = $this->where(['uid' => $item2['uid']])->field('username,wechat_code,create_time')->find();
                $data[$key][$key2]['wechat_code'] = tomedia($userInfo['wechat_code']);
                $data[$key][$key2]['username'] = $userInfo['username'];
                $data[$key][$key2]['register_time'] = date('Y-m-d', strtotime($userInfo['create_time']));
            }

        }

        return $data;

    }

    /** 账号信息 */
    public function getAccountInfo($uid)
    {
        $account = $this->where('uid', $uid)->field('username,password')->find();
        $account['password'] = secret($account['password'], $account['username'], true);
        return $account;
    }

}
