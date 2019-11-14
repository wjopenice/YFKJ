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

            $redgroup = self::where('id', $item['redgroup_id'])->field('id,currency_id,uid as create_uid,online_count,money,count')->find();
            $result[] = $redgroup;
        }

        return $result;
    }

    /** 获取某个红包信息 */
    static function getGroupInfo($uid, $redgroup_id)
    {

        $groupInfo = self::where('id', $redgroup_id)->field('id,uid as create_uid,online_count,name,send_rule,notice')->find();

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

        
        $groupInfo['is_create'] = $uid == $groupInfo['create_uid'] ? true : false;

        /** 获取评论条数 只算一级评论 */
        $comment_count = Db::name('red_comment')->where(['redgroup_id' => $redgroup_id, 'parent_id' => 0])->count();
        $groupInfo['comment_count'] = $comment_count;
        /** 获取群成员信息 */
        $redgroup_user = Db::name('red_collection')->where(['redgroup_id' => $redgroup_id])->field('uid')->order('create_time asc')->select();
        $users = [];
       
        foreach ($redgroup_user as $key => $item)
        {
            $user = Db::name('user')->where('uid', $item['uid'])->field('uid,nickname,avatar')->find();
            $users[] = $user;
        }
        $groupInfo['users'] = $users;

        return $groupInfo;

    }

    /** 生成红包群 */
    public function createGroup($group, $pay_res)
    {

        $roomNumber = $this->getRoomNumber();
        if (is_error($roomNumber))
        {
            return error('room number fail');;
        }
         $group['order_no'] = $pay_res['order_no'];
         $group['room_number'] = $roomNumber;
         $group['create_time'] = time();

        /** 去除群主收益与平台收益 */
        $group_owner_money = $group['money'] * \beans\ProfitCode::RED_GROUP_OWNER / 100;
        $platform_money = $group['money'] * \beans\ProfitCode::RED_PLATFORM / 100;

        $red_money = bcsub($group['money'], $group_owner_money, 3);
        $red_money = bcsub($red_money, $platform_money, 3);

         /** 创建红包记录 */
         $redpacket_add['uid'] = $group['uid'];
         $redpacket_add['money'] = $red_money;
         $redpacket_add['order_no'] = $pay_res['order_no'];
         $redpacket_add['count'] =  $group['count'];
         $redpacket_add['create_time'] = time();

         /** 创建自己的收藏记录 */
         $collection_add['uid'] = $group['uid'];
         $collection_add['create_time'] = time();

         /** 生成每条红包金额 */
        list($money_arr, $min_money, $max_money) = $this->getRedMoneyData($red_money, $group['count']);

        Db::startTrans();
        try {

            $group_id = $this->insertGetId($group);
            $redpacket_add['redgroup_id'] = $group_id;
            $redpacket_id = Db::name('redpacket')->insertGetId($redpacket_add);
            $collection_add['redgroup_id'] = $group_id;
            Db::name('red_collection')->insert($collection_add);
            foreach ($money_arr as $item)
            {
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
            return error('create tree fail');
        }

        /** 创建红包成功红发送奖励 */
        $info['order_no'] = $group['order_no'];
        $info['redgroup_id'] = $redpacket_id;
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
        /** 支付的金额为红包的二倍 */
        $pay_money = $group['money'];

        /** 创建订单 */
        $payOrder_model = new  PayOrderModel();
        $order_res = $payOrder_model->createOrder($uid, $group['currency_id'], $pay_money, 6);
        if (is_error($order_res))
        {
            return  $order_res;
        }
        $wallet = new WalletModel();
        $pay_res = $wallet->payMoney($uid, $order_res['order_no']);

        if (is_error($pay_res))
        {
            return  $pay_res;
        }

        /** 去除群主收益与平台收益 */
        $group_owner_money = $group['money'] * \beans\ProfitCode::RED_GROUP_OWNER / 100;
        $platform_money = $group['money'] * \beans\ProfitCode::RED_PLATFORM / 100;

        $red_money = bcsub($group['money'], $group_owner_money, 3);
        $red_money = bcsub($red_money, $platform_money, 3);

        /** 创建红包记录 */
        $redpacket_add['redgroup_id'] = $group['id'];
        $redpacket_add['uid'] = $uid;
        $redpacket_add['money'] = $red_money;
        $redpacket_add['order_no'] = $pay_res['order_no'];
        $redpacket_add['count'] =  $group['count'];
        $redpacket_add['create_time'] = time();

        /** 生成每条红包金额 */
        list($money_arr, $min_money, $max_money) = self::getRedMoneyData($red_money, $group['count']);
        Db::startTrans();
        try {

            $redpacket_id = Db::name('redpacket')->insertGetId($redpacket_add);
            foreach ($money_arr as $item)
            {
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
            logs('抢红后发送红包失败', '', 'sendred');
            return error('create tree fail');
        }


        /** 创建红包成功红发送奖励 */
        $info['order_no'] = $pay_res['order_no'];
        $info['redgroup_id'] = $redpacket_id;
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
    static function grabRedpicket($uid, $redpicket_id)
    {
        /** 获取记录 */
        $redpicket = Db::name('redpicket')->where('id', $redpicket_id)->find();
        if (empty($redpicket))
        {
            return error('can not find redpicket');
        }

        /** 获取群组信息 */
        $redgroup = self::where('id', $redpicket['redgroup_id'])->find();
        /** 判断是否还有未抢红包 */
        $redpick_log = Db::name('redpicket_log')->where(['redpicket_id' => $redpicket_id, 'uid' => 0])->field('id,money,lucky')->order('id asc')->find();
        if (empty($redpick_log))
        {
            return error('redpicket is none');
        }
        /** 判断是否参与红包 */
        $log = Db::name('redpicket_log')->where(['redpicket_id' => $redpicket_id, 'uid' => $uid])->value('id');
        if ($log)
        {
            return error('you already grab');
        }

        /** 修改红包用户 增加金额 */
        $wallet_model = new WalletModel();
        /** 判断余额是否充足 */
        $my_money = $wallet_model->getMoney($uid, $redgroup['currency_id']);
        if ($my_money < $redgroup['money'])
        {
            return error('money not enough');
        }

        /** 红包流水 */
        $dis_money = bcdiv(bcmul($redpick_log['money'], 15, 3), 100, 3);
        $redlog['uid'] = $uid;
        $redlog['from_uid'] = $redpicket['uid'];
        $redlog['money'] = bcsub($redpick_log['money'], $dis_money, 3);
        $redlog['order_no'] = $redpicket['order_no'];
        $redlog['redpacket_id'] = $redpicket_id;
        $redlog['currency_id'] = $redgroup['currency_id'];
        $redlog['scene'] = 2;
        $redlog['remark'] = '红包收益';
        $redlog['create_time'] = time();

        Db::startTrans();
        try {

            Db::name('redpicket_log')->where(['id' => $redpick_log['id'], 'uid' => 0])->update(['uid' => $uid, 'update_time' => time()]);
            Db::name('red_log')->insert($redlog);
            $wallet_model->addMoney($redlog['uid'], $redlog['currency_id'], $redlog['money'], $redlog['order_no'], $redlog['remark']);
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            return error('grab fail');
        }

        /** 发送分销奖励 */
        $dis_info['order_no'] = $redlog['order_no'];
        $dis_info['money'] = $redpick_log['money'];
        $dis_info['scene'] = 2;
        $dis_info['redpacket_id'] = $redpicket_id;
        $dis_info['currency_id'] = $redlog['currency_id'];
        $dis_info['remark'] = '红包收益';
        $user_model = new UserModel();
        $user_model->sendDisReward($uid, $dis_info);

        /** 判断是否是下一个发送的幸运者 */
        if ($redpick_log['lucky'] == 1)
        {
            /** 发送下一个红包 */
            self::createRedpicket();
        }

        return ['result' => 'success'];

    }

    /** 红包记录明细 */
    static function getRedpicketLog($uid, $redpacket_id)
    {
        
        $redpacket = Db::name('redpacket')->where('id', $redpacket_id)->find();
        $redpacket_logs = Db::name('redpacket_log')->where(['redpacket_id' => $redpacket_id])->where('uid', '<>', 0)->select();
        $nextUser = false;
        foreach ($redpacket_logs as $key => $item)
        {
            /** 获取用户信息 */
            $userInfo = Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $redpicket_logs[$key]['nickname'] = $userInfo['nickname'];
            $redpicket_logs[$key]['avatar'] = $userInfo['avatar'];
            /** 判断是不是游客 */
            $collection = Db::name('collection')->where(['redgroup_id' => $redpacket['redgroup_id'], 'uid' => $item['uid']])->value('id');
            if ($collection)
            {
                $redpicket_logs[$key]['visitor'] = false;
                
            } else
            {
                $redpicket_logs[$key]['visitor'] = true;
            }

            /** 本人并且最小数额下一个发送者 */
            if ($item['lucky'] == 1 && $uid == $item['uid'])
            {
                $nextUser = $userInfo;
            }

        }

        return ['redpicket_logs' => $redpicket_logs, 'nextUser' => $nextUser];
    }

    /** 群组红包记录 */
    static function getRedpicketList($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $list = Db::name('redpacket')->where(['redgroup_id' => $where['redgroup_id']])->field('id,uid,money,create_time')->order('create_time desc')->page($page, 10)->select();
        /** 修改排序 */
        $listLog = array_reverse($list);
        /** 获取发送人信息 */
        foreach ($listLog as $key => $item)
        {
            $userInfo = Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $listLog[$key]['userInfo'] = $userInfo;
        }

        return $listLog;
        
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
    public function setOnlineCount($redgroup_id, $type)
    {
        if ($type == 1)
        {
            $this->where(['id' => $redgroup_id])->setInc('online_count');
            
        } else
        {

            $this->where(['id' => $redgroup_id])->where('online_count', '>', 0)->setDec('online_count');
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
                return Db::name('red_collection')->where('id', $collection_id)->delete();
            }


        } else
        {
            $collection_add['uid'] = $uid;
            $collection_add['redgroup_id'] = $redgroup_id;
            $collection_add['create_time'] = time();
            return Db::name('red_collection')->insert($collection_add);
        }

    }

    /** 评论 */
    static function setComment($data)
    {
        $data['create_time'] = time();
        return Db::name('red_comment')->insert($data);
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
            $list[$key]['userInfo'] = $userInfo;
            $sub_comment = Db::name('red_comment')->where(['redgroup_id' => $data['redgroup_id'], 'parent_id' => $item['id']])->order('create_time desc')->select();
            foreach ($sub_comment as $key2 => $item2)
            {
                $userInfo = Db::name('user')->where('uid', $item2['uid'])->field('nickname,avatar')->find();
                $sub_comment[$key2]['userInfo'] = $userInfo;
                /** 点赞数量 */
                $zan_count = Db::name('red_zan')->where('comment_id', $item2['id'])->count();
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
            
        }

        return $list;
    }

    /** 点赞 */
    static function setZan($data)
    {
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
        $collection_log = Db::name('red_collection')->where(['uid' => $data['uid'], 'redgroup_id' => $data['redgroup_id']])->field('id,istop');
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

}