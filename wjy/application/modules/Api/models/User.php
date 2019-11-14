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

//        if (cache($ip.$username) > 5)
//        {
//            echo '123';die();
//            return error('frequent operation please try again later');
//        }

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

//        $invite_code = $this->getUserInvitecode();
//        if (is_error($invite_code))
//        {
//            return $invite_code;
//        }
        $user_add['username'] = $user_name;
        $user_add['nickname'] = $user_name;
        $user_add['avatar'] = '/avatar/'.rand(1, 20).'.jpg';
        $password_random = Random::word($n=8);
        $user_add['password'] = secret($password_random, $user_add['username']);
        $user_add['invite_code'] = 0;
        $user_add['puid'] = $user['uid'];
        $user_add['create_time'] = time();
        $user_add['update_time'] = time();

        /** 获取eth地址 */
        $eth_res = Tool::getAddress();

        if ($eth_res['msg'] == 'success' && $eth_res['code'] == 0 && $eth_res['data']['address'])
        {
            $eth_address = $eth_res['data']['address'];
            logs($eth_res, '', 'eth_address');

        } else
        {
            return error('未获取到地址信息');
        }

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
            $user_info['uid'] = $uid;
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

    /** 获取用户信息 */
    static function getUserInfo($uid)
    {
       $userInfo = self::where(['uid' => $uid])->field('nickname,avatar,wechat_code')->find();
       $userInfo['avatar'] = tomedia($userInfo['avatar']).'?time='.time();
       $userInfo['wechat_code'] = $userInfo['wechat_code'] ? tomedia($userInfo['wechat_code']).'?time='.time() : '';
       return $userInfo;
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
            foreach ($item as $key2 => $item2)
            {
                $userInfo = $this->where(['uid' => $item2['uid']])->field('wechat_code,create_time')->find();
                $data[$key][$key2]['wechat_code'] = tomedia($userInfo['wechat_code']);
                $data[$key][$key2]['create_time'] = $userInfo['create_time'];
                $data[$key][$key2]['register_time'] = date('Y-m-d', strtotime($userInfo['create_time']));
            }

        }

        return $data;

    }

    /** 获取账号密码信息 */
    static function getAccountInfo($uid)
    {
        $account = self::where('uid', $uid)->field('username,password')->find();
        $account['password'] = secret($account['password'], $account['username'], true);
        return $account;
    }

    /** 获取邀请信息 */
    static function getInviteInfo($uid)
    {

        $invite_code = self::where('uid', $uid)->value('invite_code');
        return ['invite_code' => $invite_code];
    }

    /** 三级分销奖励 */
    public function sendDisReward($uid, $info, $level = 1, $fuid = false)
    {
        /** 奖励比例按照 6 5 4依次奖励 如果未满三级就是平台则全部奖励 递归进行 **/
        /** 获取上级 */
        $p_uid = $this->where(['uid' => $uid])->value('puid');

        if ($p_uid == 0) {
            return true;
        }

        /** 源头id */
        if ($level == 1)
        {
           $fuid = $uid;
        }

        /** 剩余钱 */
        $sheng_money = $info['sheng_money'] ? $info['sheng_money'] :bcdiv(bcmul($info['money'], 15, 3), 100, 3);
        $finish = false;
        if ($p_uid == 1) {
            /** 一次性奖励 */
            $finish = true;
            $money = $sheng_money;

        } else
        {
            /** 计算奖励金额 */
            if ($level == 1)
            {
                $money = bcmul($sheng_money / 15, 6, 3);
                $info['sheng_money'] = bcsub($sheng_money, $money, 3);

            } else if ($level == 2)
            {
                $money = bcmul($sheng_money / 9, 5, 3);
                $info['sheng_money'] = bcsub($sheng_money, $money, 3);
            }
            else if ($level == 3)
            {
                $money = $sheng_money;

            }

        }


        $log_add['currency_id'] = $info['currency_id'];
        $log_add['from_uid'] = $fuid ? $fuid : $uid;
        $log_add['uid'] = $p_uid;
        $log_add['order_no'] = $info['order_no'];
        $log_add['scene'] = $info['scene'] ? $info['scene'] : 1;
        if ($log_add['scene'] == 1)
        {
            $log_add['tree_id'] = $info['tree_id'];

        } else
        {
            $log_add['redpacket_id'] = $info['redpacket_id'];
            $log_add['redgroup_id'] = $info['redgroup_id'];
        }

        $log_add['level'] = $level;
        $log_add['money'] = $money;
        $log_add['remark'] = $info['remark'] . $level.'级奖励';
        $log_add['create_time'] = time();
        $wallet_model = new WalletModel();
        $dis_model = new DisLogModel();
        logs(json_encode($log_add), '', 'disreward');
        Db::startTrans();
        try {

            $dis_id = $dis_model->insertGetId($log_add);
            $wallet_model->addMoney($log_add['uid'], $info['currency_id'], $log_add['money'], $info['order_no'], '分销收益');
            if ($log_add['uid'] == 1)
            {
                // 增加收益记录
                ProfitModel::addProfit($dis_id, $log_add['money'], $info['currency_id'], 5);
            }
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'].'分销奖励发送失败, 级别:'.$level, '', 'disreward');
        }

        if (!$finish && $level < 3) {
           return $this->sendDisReward($log_add['uid'], $info, $level+1, $fuid);
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

    /** 意见反馈 */
    static  function setOpinion($data)
    {
        $data['create_time'] = time();
        return Db::name('opinion')->insert($data);
    }


}
