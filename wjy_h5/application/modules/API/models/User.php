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
        if (cache($ip.$username) > 5)
        {
            return error('frequent operation please try again later');
        }
        $user = $this->where(['username' => $username, 'password' => $password])->field('uid')->find();

        /** 未找到用户 */
        if (empty($user)) {

            $error_count = cache($ip.$username);
            if ($error_count)
            {
                cache($ip.$username, $error_count+1, 600);

            } else
            {
                cache($ip.$username, 1, 600);
            }
            return error('cant not find user');
        }
        
        /** 生成token */
        $payload = array('iss'=>$user['uid'],'iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>'','jti'=>md5(uniqid('JWT').time()));
        $token = Jwt::getToken($payload);
        if (empty($token)) {

            return error('token fail');
        }
        /** 更新token */
        $res = $this->updateToken($user['uid'], $token);
        /** 增加缓存信息 */
        session_start();
        $session_id = session_id();
        if ($res) {

            cache('Auth_'.$token, null);
            cache('Auth_'.$token, $payload, Jwt::$keeptime);
            return ['token' => $token, 'sessionid' => $session_id];

        }

        return error('login fail');

    }

    /** 退出登录 */
    public function logout($token)
    {
        $this->where('token', $token)->update(['token' => '', 'update_time' => time()]);
        cache('Auth_'.$token, null);

        return true;
    }

    
    /**
     * 查询某个用户
     * return  array
     */
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

    /** 生成推荐码 */
    public function getUserInvitecode($i = 0)
    {
        $invite_code = base_convert(time() - 1420070400, 10, 36);
        /* 验证是否存在 */
        $check = $this->where('invite_code', $invite_code)->find();
        if (empty($check)) {

            return $invite_code;
        }
        if ($i > 50)
        {
            return error('get invite code fail');
        }

        return $this->getUserInvitecode($i + 1);
    }

    /** 添加用户 */
    public function addUser($user)
    {

        $user_name = $this->getUserName();
        if (is_error($user_name))
        {
            return  $user_name;
        }
        $invite_code = $this->getUserInvitecode();
        if (is_error($invite_code))
        {
            return $invite_code;
        }
        $user_add['username'] = $user_name;
        $password_random = Random::word($n=8);
        $user_add['password'] = secret($password_random, $user_add['username']);
        $user_add['invite_code'] = $invite_code;
        $user_add['puid'] = $user['uid'];
        $user_add['create_time'] = time();
        $user_add['update_time'] = time();


        /** 获取所有币种 */
        $currency = Db::name('currency')->where([])->field('id')->select();
        Db::startTrans();
        try {

            $uid = $this->insertGetId($user_add);
            /** 更新钱包 */
            $wallet_add['uid'] = $uid;
            $wallet_add['total'] = 0;
            $wallet_add['free'] = 0;
            $wallet_add['lock'] = 0;
            $wallet_add['consume'] = 0;
            $wallet_add['create_time'] = time();
            $wallet_add['update_time'] = time();
            foreach ($currency as $key => $item)
            {
                $wallet_add['currency_id'] = $item['id'];
                Db::name('wallet')->insert($wallet_add);
            }
            Db::commit();
            $user_info['username'] = $user_add['username'];
            $user_info['password'] =  $password_random;
            return $user_info;

        } catch (\Exception $e) {
            Db::rollback();
            return error('create user fail');
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

    /** 设置用户信息 */
    public function setUserInfo($uid, $userInfo)
    {
        /** 获取用户信息 */
        $userInfo['update_time'] = time();
        $res = $this->where('uid', $uid)->update($userInfo);
        if ($res)
        {
            return true;
        }

        return false;
    }

    /** 获取用户uid */
    public function getUidByToken($token)
    {
        $uid = $this->where('token', $token)->value('uid');
        return $uid;
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
            $userInfo = $this->where(['uid' => $item['uid']])->field('wechat_code,create_time')->find();
            $data[$key]['wechat_code'] = $userInfo['wechat_code'];
            $data[$key]['create_time'] = $userInfo['create_time'];
        }

        return $data;

    }

    /** 三级分销奖励 */
    public function sendDisReward($uid, $info, $level = 1)
    {

        /** 获取上级 */
        $p_uid = $this->where(['uid' => $uid])->value('puid');

        if ($p_uid == 0) {
            return;
        }

        $radio_key = 1;
        $finish = false;
        /** 奖励比例 */
        $reward_radio[0] = [15, 9, 4];
        $reward_radio[1] = [6, 5, 4];
        if ($p_uid == 1) {
            /** 一次性奖励 */
            $finish = true;
            $radio_key = 0;
        }


        $radio = $reward_radio[$radio_key][$level - 1];
        $money = (float)$info['money'] * $radio / 100;
        $log_add['currency_id'] = $info['currency_id'];

        $log_add['from_uid'] = $uid;
        $log_add['uid'] = $p_uid;
        $log_add['order_no'] = $info['order_no'];
        $log_add['scene'] = $info['scene'] ? $info['scene'] : 1;
        if ($log_add['scene'] == 1)
        {
            $log_add['tree_id'] = $info['tree_id'];

        } else
        {
            $log_add['redpacket_id'] = $info['redpacket_id'];
        }

        $log_add['level'] = $level;
        $log_add['money'] = $money;
        $log_add['remark'] = $info['remark'] . $level.'级奖励';
        $log_add['create_time'] = time();
        $wallet_model = new WalletModel();
        $dis_model = new DisLogModel();
        
        Db::startTrans();
        try {

            $dis_model->insert($log_add);
            $wallet_model->addMoney($log_add['uid'], $info['currency_id'], $log_add['money']);
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'].'分销奖励发送失败, 级别:'.$level, '', 'disreward');
        }

        if (!$finish && $level < 3) {
            $this->sendDisReward($log_add['uid'], $info, $level+1);
        }
    }

    /** 获取用户下的某条数据 */
    public function getUserColumn($uid, $column)
    {
        $user = $this->where(['uid' => $uid])->field($column)->find()->toArray();
        if (count($user) == 1)
        {
           return $user[$column];
        }

        return $user;
    }



}