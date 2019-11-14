<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class RedgroupModel extends Model
{

    /** 获取红包列表 */
    static function getList($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $list = Db::name('red_collection')->where(['uid' => $where['uid']])->order('istop desc, id desc')->page($page, 10)->select();
        /** 获取群基本信息 */
        $result = [];
        foreach ($list as $key => $item)
        {

            $redgroup = self::where('id', $item['redgroup_id'])->field('id,currency_id,uid as create_uid,name,online_user,money,count,send_rule')->find()->toArray();;
            /** 获取币种 */
            $currency = CurrencyModel::where('id', $redgroup['currency_id'])->value('name');
            $redgroup['currency'] = $currency;

//            $redgroup['online_user'] = array_filter(explode(",", $redgroup['online_user']));
//            $redgroup['online_count'] = count($redgroup['online_user']);
            $users_count = Db::name('red_collection')->where(['redgroup_id' => $item['redgroup_id']])->count();
            $redgroup['user_count'] = $users_count;
            // 创建者

            $result[$key] = $redgroup;
            if ($redgroup['create_uid'] == $where['uid'])
            {
                $result[$key]['creater'] = true;

            } else
            {
                $result[$key]['creater'] = false;
            }


        }
        return $result;
    }


    static function getRedgroupByCurrency($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $condition = [];
        $condition[] = ['password', '=', 0];
        if ($where['currency_id'])
        {
            $condition[] = ['currency_id', '=', $where['currency_id']];
        }
        if ($where['room_number'])
        {
            $condition[] = ['room_number', 'like', $where['room_number'].'%'];
        }
        // 搜索房间号码
        $list = self::where($condition)->field('id,currency_id,name,money,count,send_rule,create_time')->order('create_time desc')->page($page, 20)->select();
        foreach ($list as $key => $item)
        {
            $currency = Db::name('currency')->where('id', $item['currency_id'])->field('id,name,tag,icon')->find();
            $currency['icon'] = tomedia($currency['icon']);
            $list[$key]['currency'] = $currency;
            $users_count =Db::name('red_collection')->where(['redgroup_id' => $item['id']])->count();
            $list[$key]['user_count'] = $users_count;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        return $list;
    }

    /** 获取下一个 */
    static function getNext($where)
    {

        /** 五种情况 一种首页全部 一种带币种类型 一种随机 一种他人 一种自己 */
        if ($where['type'] == 'all')
        {

            $whereP = [];
            // 首页的进来了 2种 全部和特定币种
            if (!empty($where['currency']))
            {
                $whereP['currency_id'] = $where['currency'];
            }
            $whereP['password'] = 0;
            $count = self::where($whereP)->count();

            if ($where['page'] >= $count)
            {
                $data['none'] = true;
                $data['redgroup_id'] = '';

            } else
            {

                $list = self::where($whereP)->order('create_time desc')->page($where['page'] + 1, 1)->select();
                $data['none'] = false;
                $data['redgroup_id'] = $list[0]['id'];

            }

        }  else if ($where['type'] == 'rand')
        {
            /** 随机 */
            $list = self::field("id")->where('password', 0)->limit(1)->order('rand()')->select();
            $data['redgroup_id'] = $list[0]['id'];
            $data['none'] = false;

        } else if (!empty($where['fuid']))
        {

            $page = $where['page'] ? $where['page'] : 1;
            $psize = 1;
            $start = ($page - 1) * $psize;

            if ($where['type'] == 'like')
            {
                // 喜欢的
                $listsql = "SELECT * FROM red_collection  WHERE (SELECT COUNT(1) AS num FROM redgroup  WHERE id = redgroup_id  AND password = 0 AND  uid = ?) = 0 AND uid = ? ORDER BY istop DESC, id DESC limit $start,$psize";


            } else
            {
                // 创建的
                $listsql = "SELECT * FROM red_collection  WHERE (SELECT COUNT(1) AS num FROM redgroup  WHERE id = redgroup_id AND password = 0 AND  uid = ?) > 0 AND uid = ? ORDER BY istop DESC, id DESC limit $start,$psize";

            }

            //echo $listsql;die();
            $list = Db::query($listsql, [$where['fuid'], $where['fuid']]);
            if (empty($list))
            {
                $data['none'] = true;
                $data['redgroup_id'] = '';

            } else
            {
                $data['none'] = false;
                $data['redgroup_id'] = $list[0]['redgroup_id'];
            }


        } else
        {
            /** 自己 */
            $uid = $where['uid'];
            $whereP['uid'] = $uid;
            $count = Db::name('red_collection')->where($whereP)->count();
            if ($where['page'] >= $count)
            {
                $data['none'] = true;
                $data['redgroup_id'] = '';

            } else
            {
                $list = Db::name('red_collection')->where($whereP)->order('istop desc, id desc')->page($where['page'] + 1, 1)->select();
                $data['none'] = false;
                $data['redgroup_id'] = $list[0]['redgroup_id'];

            }
        }

        return $data;
    }

    /** 获取某个红包信息 */
    static function getGroupInfo($uid, $redgroup_id)
    {

        //保存获取记录

        $groupInfo = self::where('id', $redgroup_id)->field('id,uid as create_uid,currency_id,online_user,name,send_rule,password,money,count')->find();

        if ($groupInfo['password'] != 0 && $groupInfo['create_uid'] != $uid)
        {
            $red_auth = Db::name('redgroup_auth')->where(['uid' => $uid, 'redgroup_id' => $redgroup_id])->find();
            if (empty($red_auth))
            {
                // 已经验证过直接保存一下记录
                $red_auth_add['uid'] = $uid;
                $red_auth_add['redgroup_id'] = $redgroup_id;
                $red_auth_add['create_time'] = time();
                Db::name('redgroup_auth')->insert($red_auth_add);
                unset($groupInfo['password']);
            }
        }

        /** 获取是否收藏与置顶 */
        $collection = Db::name('red_collection')->where(['uid' => $uid, 'redgroup_id' => $redgroup_id])->find();
        if ($collection)
        {
            $groupInfo['collection'] = true;
            $groupInfo['istop'] =  $collection['istop'] == 0 ? false : true;


        } else
        {
            $groupInfo['collection'] = false;
            $groupInfo['istop'] = false;
        }

        /** 币种信息 */
        $groupInfo['currency_name'] = Db::name('currency')->where('id', $groupInfo['currency_id'])->value('name');

//        $groupInfo['online_user'] = array_filter(explode(",", $groupInfo['online_user']));
//        $groupInfo['online_count'] = count($groupInfo['online_user']);
        $groupInfo['is_create'] = $uid == $groupInfo['create_uid'] ? true : false;

        /** 获取评论条数  */
        $comment_count = Db::name('red_comment')->where(['redgroup_id' => $redgroup_id])->count();
        $groupInfo['comment_count'] = $comment_count;

        /** 获取收藏条数 */
        $collection_count = Db::name('red_collection')->where('redgroup_id', $redgroup_id)->count();
        $groupInfo['collection_count'] = $collection_count;

        /** 获取用户数据*/
        /** 获取群成员信息 */
        $users_count = Db::name('red_collection')->where(['redgroup_id' => $redgroup_id])->count();
        $groupInfo['user_couont'] = $users_count;
//        $users = [];

//        foreach ($redgroup_user as $key => $item)
//        {
//            $user = Db::name('user')->where('uid', $item['uid'])->field('uid,nickname,avatar')->find();
//            $users[] = $user;
//        }
//        $groupInfo['users'] = $users;

        return $groupInfo;

    }

    /** 获取群信息 */
    static function getGroupDetail($uid, $redgroup_id)
    {

        $groupInfo = self::where('id', $redgroup_id)->field('id,uid as create_uid,name,send_rule,room_number')->find();


        /** 获取是否收藏与置顶 */
        $collection = Db::name('red_collection')->where(['uid' => $uid, 'redgroup_id' => $redgroup_id])->find();
        if ($collection)
        {
            $groupInfo['collection'] = true;
            $groupInfo['istop'] =  $collection['istop'] == 0 ? false : true;


        } else
        {
            $groupInfo['collection'] = false;
            $groupInfo['istop'] = false;
        }


        /** 获取用户数据*/
        /** 获取群成员信息 */
        $redgroup_user = Db::name('red_collection')->where(['redgroup_id' => $redgroup_id])->field('id,uid,create_time')->order('id asc')->select();
        $users = [];
        foreach ($redgroup_user as $key => $item)
        {
            $user = Db::name('user')->where('uid', $item['uid'])->field('uid,nickname,avatar')->find();
            $user['avatar'] = tomedia($user['avatar']);
            $users[] = $user;
        }
        $groupInfo['users'] = $users;

        return $groupInfo;

    }

    /** 生成红包群 */
    public function createGroup($group, $pay_res)
    {

//        $roomNumber = $this->getRoomNumber();
//        if (is_error($roomNumber))
//        {
//            return error('room number fail');;
//        }
         $group['order_no'] = $pay_res['order_no'];
         $group['room_number'] = 0;
         $group['create_time'] = time();

        /** 去除群主收益与平台收益 */
        $group_owner_money = $group['money'] * \beans\ProfitCode::RED_GROUP_OWNER / 100;
        $platform_money = $group['money'] * \beans\ProfitCode::RED_PLATFORM / 100;

        $red_money = bcsub($group['money'], $group_owner_money, 3);
        $red_money = bcsub($red_money, $platform_money, 3);

         /** 创建红包记录 */
         $redpacket_add['uid'] = $group['uid'];
         $redpacket_add['money'] = $red_money;
         $redpacket_add['realmoney'] = $group['money'];
         $redpacket_add['currency_id'] = $group['currency_id'];
         $redpacket_add['order_no'] = $pay_res['order_no'];
         $redpacket_add['count'] =  $group['count'];
         $redpacket_add['create_time'] = time();

         /** 创建自己的收藏记录 */
         $collection_add['uid'] = $group['uid'];
         $collection_add['create_time'] = time();

        $wallet_model = new WalletModel();
         /** 生成每条红包金额 */
        list($money_arr, $min_money, $max_money) = $this->getRedMoneyData($red_money, $group['count']);

        Db::startTrans();
        try {

            $group_id = $this->insertGetId($group);
            $room_number = 'R'. (100000 + $group_id);
            $this->where('id', $group_id)->setField('room_number', $room_number);
            $redpacket_add['redgroup_id'] = $group_id;
            $redpacket_id = Db::name('redpacket')->insertGetId($redpacket_add);
            $collection_add['redgroup_id'] = $group_id;
            Db::name('red_collection')->insert($collection_add);
            foreach ($money_arr as $item)
            {
                $redpacket_item['lucky'] = 0;
                $redpacket_item['currency_id'] = $group['currency_id'];
                $redpacket_item['money'] = $item;
                if ($min_money > 0 && $item == $min_money)
                {
                    $redpacket_item['lucky'] = 1;
                    $min_money = 0;
                }
                if ($max_money > 0 && $item == $max_money)
                {
                    $redpacket_item['lucky'] = 2;
                    $max_money = 0;
                }
                $redpacket_item['redpacket_id'] = $redpacket_id;
                $redpacket_item['create_time'] = time();
                Db::name('redpacket_log')->insert($redpacket_item);
            }

            // 增加压金收益
            $wallet_model->addMoney(1, $group['currency_id'], $group['money'], $pay_res['order_no'], '创建红包押金收益');
            // 增加收益记录
            ProfitModel::addProfit($group_id, $group['money'], $group['currency_id'], 3);

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            return error('create tree fail');
        }

        /** 创建红包成功红发送奖励 */
        $info['order_no'] = $group['order_no'];
        $info['redpacket_id'] = $redpacket_id;
        $info['redgroup_id'] = $group_id;
        $info['currency_id'] = $group['currency_id'];
        $info['money'] = $group['money'];
        $info['platform'] = $platform_money;
        $info['group_owner'] = $group_owner_money;
        $redlog_model = new RedLogModel();
        /** 1.平台收益 */
        $redlog_model->platformCommission($group['uid'], $info);
        /** 2.群主收益 */
        $redlog_model->sendRewardForGroupOwner($group['uid'], $info);

        return ['redgroup_id' => $group_id];


    }

    /** 生成红包 */
    static function createRedpicket($uid, $group)
    {
        /** 支付的金额 */
        $pay_money = $group['money'];

        /** 创建订单 */
        $payOrder_model = new  PayOrderModel();
        $order_res = $payOrder_model->createOrder($uid, $group['currency_id'], $pay_money, 5);
        if (is_error($order_res))
        {
            logs('抢红后发送红包订单失败', '', 'sendred');
            return  $order_res;
        }
        $wallet = new WalletModel();
        $pay_res = $wallet->payMoney($uid, $order_res['order_no']);

        if (is_error($pay_res))
        {
            logs('抢红后发送红包支付失败:订单:'.$order_res['order_no'].'群:'.$group['id'], '', 'sendred');
            return  $pay_res;
        }
        //$pay_res['order_no'] = '2019092137894320702397';
        /** 去除群主收益与平台收益 */
        $group_owner_money = $group['money'] * \beans\ProfitCode::RED_GROUP_OWNER / 100;
        $platform_money = $group['money'] * \beans\ProfitCode::RED_PLATFORM / 100;

        $red_money = bcsub($group['money'], $group_owner_money, 3);
        $red_money = bcsub($red_money, $platform_money, 3);

        /** 创建红包记录 */
        $redpacket_add['uid'] = $uid;
        $redpacket_add['currency_id'] = $group['currency_id'];
        $redpacket_add['redgroup_id'] = $group['id'];
        $redpacket_add['money'] = $red_money;
        $redpacket_add['realmoney'] = $group['money'];
        $redpacket_add['order_no'] = $pay_res['order_no'];
        $redpacket_add['count'] = $group['count'];
        $redpacket_add['create_time'] = time();

        /** 生成每条红包金额 */
        list($money_arr, $min_money, $max_money) = self::getRedMoney($red_money, $group['count']);
        Db::startTrans();
        try {

            $redpacket_id = Db::name('redpacket')->insertGetId($redpacket_add);
            foreach ($money_arr as $item)
            {
                $redpacket_item['lucky'] = 0;
                $redpacket_item['currency_id'] = $group['currency_id'];
                $redpacket_item['money'] = $item;
                if ($min_money > 0 && $item == $min_money)
                {
                    $redpacket_item['lucky'] = 1;
                    $min_money = 0;
                }
                if ($max_money > 0 && $item == $max_money)
                {
                    $redpacket_item['lucky'] = 2;
                    $max_money = 0;
                }
                $redpacket_item['redpacket_id'] = $redpacket_id;
                $redpacket_item['create_time'] = time();
                Db::name('redpacket_log')->insert($redpacket_item);
            }
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs('抢红后发送红包失败订单:'.$order_res['order_no'].'群:'.$group['id'], '', 'sendred');
            return error('create tree fail');
        }


        /** 创建红包成功红发送奖励 */
        $info['order_no'] = $pay_res['order_no'];
        $info['redpacket_id'] = $redpacket_id;
        $info['redgroup_id'] = $group['id'];
        $info['currency_id'] = $group['currency_id'];
        $info['money'] = $group['money'];
        $info['platform'] = $platform_money;
        $info['group_owner'] = $group_owner_money;
        $redlog_model = new RedLogModel();
        /** 1.平台收益 */
        $redlog_model->platformCommission($group['uid'], $info);
        /** 2.群主收益 */
        $redlog_model->sendRewardForGroupOwner($group['uid'], $info);

        return ['result' => 'success'];

    }

    /** 抢红包 */
    static function grabRedpicket($uid, $redpacket_id)
    {

        /** 获取记录 */
        $redpicket = Db::name('redpacket')->where('id', $redpacket_id)->find();
        if (empty($redpicket))
        {
            return error('can not find redpicket');
        }

        /** 获取群组信息 */
        $redgroup = self::where('id', $redpicket['redgroup_id'])->find();

        /** 判断是否还有未抢红包 */
        $redpick_log = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id, 'uid' => 0])->field('id,money,lucky')->order('id asc')->find();
        if (empty($redpick_log))
        {
            $join_log = \think\Db::name('redpacket_join')->where(['uid' => $uid, 'redpacket_id' => $redpacket_id])->find();
            if (empty($join_log))
            {
                $redlog_join['uid'] = $uid;
                $redlog_join['redpacket_id'] = $redpacket_id;
                $redlog_join['create_time'] = time();
                \think\Db::name('redpacket_join')->insert($redlog_join);
            }
            return ['has' => false];
        }

        /** 判断是否参与红包 */
        $log = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id, 'uid' => $uid])->value('id');
        if ($log)
        {
            return ['has' => true, 'islog' => true];
        }

        $wallet_model = new WalletModel();
        /** 判断余额是否充足 */
        $my_money = $wallet_model->getMoney($uid, $redgroup['currency_id']);
        if ($my_money < $redgroup['money'])
        {
            return ['has' => true, 'islog' => false, 'money' => false];
        }


        /** 红包流水 */
        $dis_money = bcdiv(bcmul($redpick_log['money'], 15, 3), 100, 3);
        $redlog['uid'] = $uid;
        $redlog['from_uid'] = $redpicket['uid'];
        $redlog['money'] = bcsub($redpick_log['money'], $dis_money, 3);
        $redlog['order_no'] = $redpicket['order_no'];
        $redlog['redgroup_id'] = $redgroup['id'];
        $redlog['redpacket_id'] = $redpacket_id;
        $redlog['currency_id'] = $redgroup['currency_id'];
        $redlog['scene'] = 2;
        $redlog['remark'] = '抢红包收益';
        $redlog['create_time'] = time();

        Db::startTrans();
        try {

            // 判定为下一个发送者 扣除押金
            if ($redpick_log['lucky'] == $redgroup['send_rule'])
            {
                $wallet_model->addLuckMoney($uid, $redlog['currency_id'], $redgroup['money']);
            }
            Db::name('redpacket_log')->where(['id' => $redpick_log['id'], 'uid' => 0])->update(['uid' => $uid, 'update_time' => time()]);
            Db::name('red_log')->insert($redlog);
            $wallet_model->addMoney($redlog['uid'], $redlog['currency_id'], $redlog['money'], $redlog['order_no'], $redlog['remark']);

            Db::commit();
            logs('红包id:'.$redpick_log['id'].'用户id:'.$uid, '', 'grabredlog');
        } catch (\Exception $e) {
            Db::rollback();
            return error('grab fail');
        }


        /** 发送分销奖励 */
        $dis_info['order_no'] = $redlog['order_no'];
        $dis_info['money'] = $redpick_log['money'];
        $dis_info['scene'] = 2;
        $dis_info['redpacket_id'] = $redpacket_id;
        $dis_info['redgroup_id'] = $redgroup['id'];
        $dis_info['currency_id'] = $redlog['currency_id'];
        $dis_info['remark'] = '红包收益';
        $user_model = new UserModel();
        $user_model->sendDisReward($uid, $dis_info);
        $need = false;
        /** 判断是否还有未抢红包 */
        $shengyu_log = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id, 'uid' => 0])->field('id')->find();
        // 最后一个包则发送记录
        if (empty($shengyu_log))
        {
            /** 发送下一个红包 */
            $need = true;
            /** 判断是否是下一个发送的幸运者 */
            if ($redpick_log['lucky'] == $redgroup['send_rule'])
            {
                $send_uid = $uid;

            } else
            {
                $send_uid = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id, 'lucky' => $redgroup['send_rule']])->value('uid');
            }

            // 更新红包抢完时间
            Db::name('redpacket')->where(['id' => $redpacket_id])->update(['finish_time' => time()]);
            self::createRedpicket($send_uid, $redgroup);
        }

        //return ['result' => 'success', 'money' => $redpick_log['money'], 'need' => $need];
        return ['has' => true, 'islog' => false, 'money' => true, 'success' => true];

    }

    /** 红包记录明细 */
    static function getRedpicketLog($uid, $redpacket_id)
    {

        $redpacket = Db::name('redpacket')->where('id', $redpacket_id)->find();

        if (empty($redpacket))
        {
            return error('该红包已丢失');
        }


        $redpacket_logs = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id])->where('uid', '>', 0)->select();
        // 获取群信息
        $group = Db::name('redgroup')->where('id', $redpacket['redgroup_id'])->field('currency_id,send_rule')->find();
        // 获取币种信息

        $curreny_name = Db::name('currency')->where('id', $group['currency_id'])->value('name');

        $nextUser = false;
        $myself = [];
        // 发包人信息
        $sendUser = Db::name('user')->where('uid', $redpacket['uid'])->field('nickname,avatar')->find();
        $sendUser['avatar'] = tomedia($sendUser['avatar']);
        $sendUser['currency_name'] = $curreny_name;
        $sendUser['rule'] = $group['send_rule'];
        foreach ($redpacket_logs as $key => $item)
        {
            $redpacket_logs[$key]['currency_name'] = $curreny_name;
            /** 获取用户信息 */
            $userInfo = Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $userInfo['avatar'] = tomedia($userInfo['avatar']);
            $redpacket_logs[$key]['nickname'] = $userInfo['nickname'];
            $redpacket_logs[$key]['avatar'] = $userInfo['avatar'];
            /** 判断是不是游客 */
            $collection = Db::name('red_collection')->where(['redgroup_id' => $redpacket['redgroup_id'], 'uid' => $item['uid']])->value('id');
            if ($collection)
            {
                $redpacket_logs[$key]['visitor'] = false;

            } else
            {
                $redpacket_logs[$key]['visitor'] = true;
            }

            if ($uid == $item['uid'])
            {
                $myself = $item['money'];
                /** 未完成不显示发送者信息 本人并且最小数额下一个发送者 */
                if ($redpacket['finish_time'] > 0 && $item['lucky'] == $group['send_rule'])
                {
                    $nextUser = true;
                }
            }

            // 未完成不显示其他人金额数量 及最多最少标志
            if ($redpacket['finish_time'] == 0)
            {
                $redpacket_logs[$key]['lucky'] = 0;
                if ($uid != $item['uid'])
                {
                    $redpacket_logs[$key]['money'] = 0;
                }
            }
            $redpacket_logs[$key]['update_time'] = date('Y-m-d H:i:s', $item['update_time']);
        }

        return ['list' => $redpacket_logs, 'nextUser' => $nextUser, 'sendUser' => $sendUser, 'myself' => $myself];
    }


    /** 群组红包记录 */
    static function getRedpicketList($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $group = Db::name('redgroup')->where('id', $where['redgroup_id'])->find();
        $list = Db::name('redpacket')->where(['redgroup_id' => $where['redgroup_id']])->field('id,currency_id,uid,money,count,create_time,finish_time')->order('create_time desc')->page($page, 50)->select();
        /** 修改排序 */
        $listLog = array_reverse($list);
        /** 获取发送人信息 */
        foreach ($listLog as $key => $item)
        {
            $userInfo = Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $userInfo['avatar'] = tomedia($userInfo['avatar']);
            $listLog[$key]['userInfo'] = $userInfo;
            /** 获取已抢数量 */
            $surplus_count = Db::name('redpacket_log')->where(['redpacket_id' => $item['id'], 'uid' => 0])->count();
            $listLog[$key]['surplus_count'] = $surplus_count;
            $listLog[$key]['userInfo'] = $userInfo;

            /** 判断是否还有未抢红包 */
            $islog = Db::name('redpacket_log')->where(['redpacket_id' => $item['id'], 'uid' => 0])->field('id')->find();
            if (!empty($islog))
            {
                /** 获取本人是否到 */
                $islog = Db::name('redpacket_log')->where(['redpacket_id' => $item['id'], 'uid' => $where['uid']])->value('id');
                $listLog[$key]['islog'] = $islog ? true : false;
//                if (empty($islog))
//                {
//                    $islog =  Db::name('redpacket_join')->where(['redpacket_id' => $item['id'], 'uid' => $where['uid']])->value('id');
//                }

            }  else
            {
                $listLog[$key]['islog'] = true;
            }





            /** 获取抢购记录 */
            $redtag = self::getRedTag($item, $where['uid']);
            if ($item['finish_time'] > 0)
            {
                $red_time = $item['finish_time'] - $item['create_time'];
                $red_time_text = time2string($red_time);
                $redtag[] = ['type' => 'time', 'text' => '红包已抢完, 耗时'.$red_time_text];

                // 获取下一个发送者
                $next = Db::name('redpacket_log')->where(['redpacket_id' => $item['id'],'lucky' => $group['send_rule']])->value('uid');
                $next_user = Db::name('user')->where(['uid' => $next])->value('nickname');
                $redtag[] = ['type' => 'next',  'text' => '下一个红包将由'.$next_user.'发出'];

            }

            $listLog[$key]['redtag'] = $redtag;
        }

        return $listLog;

    }

    /** 获取抢购记录 */
    static function getRedTag($redpacket, $uid)
    {

//        $tag = Db::name('redpacket_log')->where([['redpacket_id', '=', $redpacket['id']], ['uid', '>', 0]])->order('update_time desc')->select();
        //$create_user = Db::name('user')->where(['uid' => $redpacket['uid']])->value('nickname');
//        foreach ($tag as $key => $item)
//        {
//            $str = '';
//            if ($item['uid'] == $uid)
//            {
//                $str .= '您';
//            }
//            $str = '抢了';
//        }
        $currency_name = Db::name('currency')->where('id', $redpacket['currency_id'])->value('name');
        $tagText = [];
        $log = Db::name('redpacket_log')->where([['redpacket_id', '=', $redpacket['id']], ['uid', '=', $uid]])->find();
        if ($log)
        {

            $tagText[] = ['type' => 'log',  'text' => '您抢了'.$log['money'].'个'.$currency_name.'币'];
        }

        return $tagText;


    }

    /** 获取房间号 */
    public function getRoomNumber($i = 0)
    {
        if ($i > 50)
        {
            return error('fail');
        }

        $num = 'R'.time() . Random::number(2);
        if ($this->where('room_number', $num)->find())
        {
            $i++;
            $num = $this->getRoomNumber($i);
        }

        return $num;
    }

    /** 修改在线人数 */
    public function setOnlineCount($redgroup_id, $type, $uid)
    {
        $online_user_str = $this->where(['id' => $redgroup_id])->value('online_user');
        $online_user = explode(",", $online_user_str);
        if (empty($online_user))
        {
            $online_user = [];
        }
        // 增加
        if ($type == 1)
        {
             //判断是否存在
            if (!in_array($uid, $online_user))
            {
                $online_user[] = $uid;
                $online_user = implode(',', $online_user);
                $online_user_str = trim($online_user, ',');
                $this->where(['id' => $redgroup_id])->setField('online_user', $online_user_str);
            }

        } else
        {
            //判断是否存在
            if (in_array($uid, $online_user))
            {

                $key = array_search($uid, $online_user);
                array_splice($online_user, $key, 1);
                $online_user = implode(',', $online_user);
                $online_user_str = trim($online_user, ',');
                $this->where(['id' => $redgroup_id])->setField('online_user', $online_user_str);
            }

        }


    }

    /** 收藏/取消 */
    static function collectionGroup($uid, $redgroup_id)
    {

        /** 获取收藏记录 */
        $collection_id = Db::name('red_collection')->where(['uid' => $uid, 'redgroup_id' => $redgroup_id])->value('id');
        /** 有则删除 无则添加 */
        if ($collection_id)
        {
            /** 判断创建人 */
            $create_user = self::where(['uid' => $uid, 'id' => $redgroup_id])->value('id');
            if ($create_user)
            {
                return error('you are the founder');

            } else
            {
                Db::name('red_collection')->where('id', $collection_id)->delete();
                return ['state' => false];
            }


        } else
        {
            $collection_add['uid'] = $uid;
            $collection_add['redgroup_id'] = $redgroup_id;
            $collection_add['create_time'] = time();
            Db::name('red_collection')->insert($collection_add);
            return ['state' => true];
        }

    }

    /** 评论 */
    static function setComment($data)
    {
        $data['create_time'] = time();
        $res = Db::name('red_comment')->insert($data);
        if ($res)
        {
            $data['create_time'] = date('Y-m-d H:i:s', $data['create_time']);
            /** 获取用户信息 */
            $userInfo = Db::name('user')->where('uid', $data['uid'])->field('nickname,avatar')->find();
            $userInfo['avatar'] = tomedia($userInfo['avatar']);
            $data['userInfo'] = $userInfo;
            return $data;

        } else
        {
            return error('comment fail');
        }

    }

    /** 获取评论列表 */
    static function getComment($data)
    {
        $page = $data['page'] ? $data['page'] : 1;
        $list = Db::name('red_comment')->where(['redgroup_id' => $data['redgroup_id'], 'parent_id' => 0])->page($page, 10)->order('create_time desc')->select();
        /** 获取用户信息及子评论 */
        foreach ($list as $key => $item)
        {
            $userInfo = Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $userInfo['avatar'] = tomedia($userInfo['avatar']);
            $list[$key]['userInfo'] = $userInfo;
            $sub_comment = Db::name('red_comment')->where(['redgroup_id' => $data['redgroup_id'], 'parent_id' => $item['id']])->order('create_time desc')->select();
            foreach ($sub_comment as $key2 => $item2)
            {
                $userInfo = Db::name('user')->where('uid', $item2['uid'])->field('nickname,avatar')->find();
                $sub_comment[$key2]['userInfo'] = $userInfo;
                /** 点赞数量 */
                $zan_count = Db::name('red_zan')->where('comment_id', $item2['id'])->count();
                $sub_comment[$key]['zan_count'] = $zan_count;
                /** 我的点赞记录 */
                if (Db::name('red_zan')->where(['comment_id' => $item2['id'], 'uid' => $data['uid']])->value('id'))
                {
                    $sub_comment[$key]['isZan'] = true;

                } else
                {
                    $sub_comment[$key]['isZan'] = false;
                }
            }

            /** 点赞数量 */
            $zan_count = Db::name('red_zan')->where('comment_id', $item['id'])->count();
            /** 我的点赞记录 */
            if (Db::name('red_zan')->where(['comment_id' => $item['id'], 'uid' => $data['uid']])->value('id'))
            {
                $list[$key]['isZan'] = true;

            } else
            {
                $list[$key]['isZan'] = false;
            }
            $list[$key]['zan_count'] = $zan_count;
            $list[$key]['sub_comment'] = $sub_comment;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

        }

        return $list;
    }

    /** 用户群信息 */
    static function userGroup($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $psize = 20;
        $start = ($page - 1) * $psize;

        if ($where['type'] == 1)
        {
            // 喜欢的
            $listsql = "SELECT * FROM red_collection  WHERE (SELECT COUNT(1) AS num FROM redgroup  WHERE id = redgroup_id AND password = 0 AND  uid = ?) = 0 AND uid = ? ORDER BY istop DESC, id DESC limit $start,$psize";


        } else
        {
            // 创建的
            $listsql = "SELECT * FROM red_collection  WHERE (SELECT COUNT(1) AS num FROM redgroup  WHERE id = redgroup_id AND password = 0 AND  uid = ?) > 0 AND uid = ? ORDER BY istop DESC, id DESC limit $start,$psize";

        }
        $list = Db::query($listsql, [$where['uid'], $where['uid']]);
        foreach ($list as $key => $item)
        {
            $name = self::where('id',$item['redgroup_id'])->value('name');
            $list[$key]['name'] = $name;
            $users_count = Db::name('red_collection')->where(['redgroup_id' => $item['redgroup_id']])->count();
            $list[$key]['user_count'] = $users_count;
        }

        return $list;
    }

    /** 点赞 */
    static function setZan($data)
    {
        $comment_id = Db::name('red_comment')->where('id', $data['comment_id'])->value('id');

        if (empty($comment_id))
        {
            return error('zan fail');
        }
        $zan_id = Db::name('red_zan')->where(['uid' => $data['uid'], 'comment_id' => $data['comment_id']])->value('id');
        if ($zan_id)
        {
            return Db::name('red_zan')->where(['id' => $zan_id])->delete();

        } else
        {
            $data['create_time'] = time();

            return Db::name('red_zan')->insert($data);
        }
    }

    /** 置顶 */
    static function groupIstop($data)
    {
        $collection_log = Db::name('red_collection')->where(['uid' => $data['uid'], 'redgroup_id' => $data['redgroup_id']])->field('id,istop')->find();
        if ($collection_log)
        {
            if ($collection_log['istop'] == 0)
            {
                return Db::name('red_collection')->where('id', $collection_log['id'])->setField('istop', time());

            } else
            {
                return Db::name('red_collection')->where('id', $collection_log['id'])->setField('istop', 0);
            }

        } else
        {
            return error('please collect first');
        }
    }

    /** 设置公告 */
    static function setNotice($where, $notice)
    {
        $redgroup = self::where('id', $where['redgroup_id'])->find();
        if (empty($redgroup))
        {
            return error('redgroup not find');
        }
        if ($redgroup['uid'] != $where['uid'])
        {
            return error('not is creator');
        }
        $res = self::where('id', $redgroup['id'])->setField('notice', $notice);
        if ($res)
        {
            return true;
        }
        return error('operation failed');
    }

    /** 设置公告 */
    static function setRule($where, $rule)
    {
        $redgroup = self::where('id', $where['redgroup_id'])->find();
        if (empty($redgroup))
        {
            return error('redgroup not find');
        }
        if ($redgroup['uid'] != $where['uid'])
        {
            return error('not is creator');
        }
        $res = self::where('id', $redgroup['id'])->setField('rule', $rule);
        if ($res)
        {
            return true;
        }
        return error('operation failed');
    }

    /** 获取红包金额及最小金额 */
    public function getRedMoneyData($red_money, $count)
    {
        $money_arr = \vendor\RedCalculation::makeRandom($red_money, $count);
        $min_money = $red_money;
        $max_money = 0;
        foreach ($money_arr as $item)
        {
            if ($min_money > $item)
            {
                $min_money = $item;
            }
            if ($max_money < $item)
            {
                $max_money = $item;
            }
        }

        return [$money_arr, $min_money, $max_money];
    }


    /** 获取红包金额及最小金额 */
    static function getRedMoney($red_money, $count)
    {
        $money_arr = \vendor\RedCalculation::makeRandom($red_money, $count);
        $min_money = $red_money;
        $max_money = 0;
        foreach ($money_arr as $item)
        {
            if ($min_money > $item)
            {
                $min_money = $item;
            }
            if ($max_money < $item)
            {
                $max_money = $item;
            }
        }

        return [$money_arr, $min_money, $max_money];
    }

}
