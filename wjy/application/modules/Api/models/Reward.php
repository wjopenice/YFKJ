<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class RewardModel extends Model
{

    /** mct邀请奖励 */
    static  function sendInviteReward($uid, $rel_id, $level = 1, $group = 'mct')
    {
        /** 如果上级为平台直接结束 */
        if ($uid == 1)
        {
            return true;
        }

        /** 获取用户 */
        $user = Db::name('user')->where('uid', $uid)->field('uid,puid')->find();
        if (empty($user))
        {
            return true;
        }
        $mct_reward = getConfig($group);
        // 奖励状态
        if ($mct_reward['state'] != 2)
        {
            return true;
        }
        $currency_id = $mct_reward['currency_id'];

        if ($level == 1)
        {
            $count = $mct_reward['invite_1'];

        } else if ($level == 2)
        {
            $count = $mct_reward['invite_2'];

        } else {

            $count = $mct_reward['invite_3'];
        }
        $wallet_model = new WalletModel();


        /** 新增奖励记录 */
        $reward_add['uid'] = $uid;
        $reward_add['currency_id'] = $currency_id;
        $reward_add['rel_id'] = $rel_id;
        $reward_add['count'] = $count;
        $reward_add['type'] = 1;
        $reward_add['remark'] = "{$level}级邀请奖励{$group}";
        $reward_add['create_time'] = time();
        $reward_add['update_time'] = time();
        Db::startTrans();
        try {

            self::insert($reward_add);
            // 增加平台收益
            $wallet_model->addMoney($uid, $currency_id, $count, '', $reward_add['remark']);
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($rel_id.'奖励发送失败', '', 'reward');
        }

        $puid = $user['puid'];
        if ($puid == 1 || empty($puid))
        {
            return true;
        }

        return self::sendInviteReward($puid, $rel_id, $level+1, $group);
    }

    /** mct创建红包奖励 */
    static function sendRegisterReward($uid, $rel_id, $group = 'mct')
    {
        /** 如果上级为平台直接结束 */
        if ($uid == 1)
        {
            return true;
        }
         /** 查看是否奖励 */
         $reward = self::where(['uid' => $uid, 'type' => 2])->find();
         if (!empty($reward))
         {
             return true;
         }
        /** 获取用户 */
        $user = Db::name('user')->where('uid', $uid)->field('uid,puid')->find();
        if (empty($user))
        {
            return true;
        }
        $mct_reward = getConfig($group);
        // 奖励状态
        if ($mct_reward['state'] != 2)
        {
            return true;
        }
        $currency_id = $mct_reward['currency_id'];
        $count = $mct_reward['register'];
        $wallet_model = new WalletModel();
        $rmb = bcmul($count, $mct_reward['rmb']);
        /** 新增奖励记录 */
        $reward_add['uid'] = $uid;
        $reward_add['currency_id'] = $currency_id;
        $reward_add['rel_id'] = $rel_id;
        $reward_add['count'] = $count;
        $reward_add['type'] = 2;
        $reward_add['remark'] = "注册登录奖励{$group}";
        $reward_add['create_time'] = time();
        $reward_add['update_time'] = time();
        Db::startTrans();
        try {

            self::insert($reward_add);
            $wallet_model->addMoney($uid, $currency_id, $count, '', $reward_add['remark']);
            Db::commit();
            return ['result' => 'success', 'msg' => "登录成功，奖励您价值{$rmb}元的MCT
币，总计{$count}个MCT币已经发放到您的账户。"];

        } catch (\Exception $e) {
            Db::rollback();
            logs($rel_id.'奖励发送失败', '', 'reward');
        }

    }


    /** mct创建财富树奖励 */
    static function sendTreeReward($uid, $rel_id, $group = 'mct')
    {
        /** 如果上级为平台直接结束 */
        if ($uid == 1)
        {
            return true;
        }

        /** 获取用户 */
        $user = Db::name('user')->where('uid', $uid)->field('uid,puid')->find();
        if (empty($user))
        {
            return true;
        }
        $mct_reward = getConfig($group);
        // 奖励状态
        if ($mct_reward['state'] != 2)
        {
            return true;
        }
        $currency_id = $mct_reward['currency_id'];
        $count = $mct_reward['create_tree'];
        $wallet_model = new WalletModel();

        $rmb = bcmul($count, $mct_reward['rmb']);

        /** 新增奖励记录 */
        $reward_add['uid'] = $uid;
        $reward_add['currency_id'] = $currency_id;
        $reward_add['rel_id'] = $rel_id;
        $reward_add['count'] = $count;
        $reward_add['type'] = 4;
        $reward_add['remark'] = "创建财富树奖励{$group}";
        $reward_add['create_time'] = time();
        $reward_add['update_time'] = time();
        Db::startTrans();
        try {

            self::insert($reward_add);
            $wallet_model->addMoney($uid, $currency_id, $count, '', $reward_add['remark']);
            Db::commit();
            return ['result' => 'success', 'msg' => "创建成功，奖励您价值{$rmb}元的MCT
币，总计{$count}个MCT币已经发放到您的账户。"];

        } catch (\Exception $e) {
            Db::rollback();
            logs($rel_id.'奖励发送失败', '', 'reward');
        }

    }

    /** mct创建红包奖励 */
    static function sendRedgroupReward($uid, $rel_id, $group = 'mct')
    {
        /** 如果上级为平台直接结束 */
        if ($uid == 1)
        {
            return true;
        }

        /** 获取用户 */
        $user = Db::name('user')->where('uid', $uid)->field('uid,puid')->find();
        if (empty($user))
        {
            return true;
        }
        $mct_reward = getConfig($group);
        // 奖励状态
        if ($mct_reward['state'] != 2)
        {
            return true;
        }
        $currency_id = $mct_reward['currency_id'];
        $count = $mct_reward['create_redgroup'];
        $wallet_model = new WalletModel();
        $rmb = bcmul($count, $mct_reward['rmb']);
        /** 新增奖励记录 */
        $reward_add['uid'] = $uid;
        $reward_add['currency_id'] = $currency_id;
        $reward_add['rel_id'] = $rel_id;
        $reward_add['count'] = $count;
        $reward_add['type'] = 3;
        $reward_add['remark'] = "创建红包奖励{$group}";
        $reward_add['create_time'] = time();
        $reward_add['update_time'] = time();
        Db::startTrans();
        try {

            self::insert($reward_add);
            // 增加平台收益
            $wallet_model->addMoney($uid, $currency_id, $count, '', $reward_add['remark']);
            Db::commit();
            return ['result' => 'success',  'msg' => "创群成功，奖励您价值{$rmb}元的MCT
币，总计{$count}个MCT币已经发放到您的账户。"];

        } catch (\Exception $e) {
            Db::rollback();
            logs($rel_id.'奖励发送失败', '', 'reward');
        }

    }



}
