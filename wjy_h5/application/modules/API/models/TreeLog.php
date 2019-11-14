<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/8/26
 * Time: 16:18
 */

use think\Db;

class TreeLogModel extends Model
{

    /** 发放奖励 */
    public function sendReward($uid, $info)
    {
        /** 获取上级uid与会员等级 */
        $treeUser_model = new TreeUserModel();
        $p_user = $treeUser_model->getRewardUid($uid, $info['tree_id'], $info['vip_level']);
        $wallet_model = new WalletModel();


        $log_add['currency_id'] = $info['currency_id'];
        $log_add['tree_id'] = $info['tree_id'];
        $log_add['from_uid'] = $uid;
        $log_add['order_no'] = $info['order_no'];
        $log_add['scene'] = $info['scene'] ? $info['scene'] : 1;
        $log_add['remark'] = $info['remark'];
        $log_add['create_time'] = time();
        $disReward = false;
        /** 判断是否为总部 */
        if ($p_user['uid'] == 1)
        {
            /** 不需要分销奖励 全部奖励给总部即可 */
            $log_add['uid'] = $p_user['uid'];
            $log_add['loss_uid'] = 0;
            $log_add['money'] = $info['money'];

        } else
        {

            /** 非总部 需要检查等级是否满足 */
            if ($p_user['vip_level'] >= $info['vip_level'])
            {
                /** 等级满足 需要设置分销奖励 所以只取85%*/
                $log_add['uid'] = $p_user['uid'];
                $log_add['loss_uid'] = 0;
                $log_add['money'] = ((float)$info['money'] * 85) / 100;
                $disReward = true;

            } else
            {
                /** 不满足 丢失奖励 直接给总部 不需要分销 */
                $log_add['uid'] = 1;
                $log_add['loss_uid'] = $p_user['uid'];
                $log_add['money'] = $info['money'];


            }
        }

        /** 修改余额 */
        Db::startTrans();
        try {

            $this->insert($log_add);
            $wallet_model->addMoney($log_add['uid'], $info['currency_id'], $log_add['money']);
            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'].'奖励发送失败', '', 'treereward');
        }

        /** 分销奖励 */
        if ($disReward)
        {
            /** 分销奖励的scene代表 1.财富树 2.红包 */
            $info['scene'] = 1;
            $user_model = new UserModel();
            $user_model->sendDisReward($log_add['uid'], $info);
        }
    }

    /** 升级奖励 */
    public function sendUpgradeReward($upgrade)
    {

         $tree_model = new TreeModel();
         $tree = $tree_model->getJoinTreeInfo($upgrade['tree_id']);
         $reward_Info['currency_id'] = $tree['currency_id'];
         $reward_Info['tree_id'] = $tree['id'];
         $reward_Info['order_no'] = $upgrade['order_no'];
         $reward_Info['scene'] = 2;


         /** 每升一级对上级奖励 */
         $level = $upgrade['level'];
         $tolevel = $upgrade['tolevel'];
         for ($level; $level < $tolevel; $level++)
         {

             $uplevel = $level+1;
             $reward_Info['vip_level'] = $uplevel;
             $reward_Info['money'] = getTreeUpgradeMoney($level, $uplevel, $tree['money'], $tree['growth_ratio']);
             $reward_Info['remark'] = "从{$level}级到{$uplevel}级";
             $this->sendReward($upgrade['uid'], $reward_Info);
         }
    }

    /** 流水记录 */
    public function treeLogList($uid, $tree_id, $page = 1)
    {
          $list = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->field('id,uid,from_uid,money,create_time')->page($page, 10)->select();
          /** 获取用户信息 */
          $user_model = new UserModel();
          foreach ($list as $key => $item)
          {
              if ($item['from_uid'] == 0)
              {
                  $user_info = $user_model->where('uid', $uid)->field('nickname,avatar')->find();

              } else
              {
                  $user_info = $user_model->where('uid', $item['from_uid'])->field('nickname,avatar')->find();
              }

              $list[$key]['userinfo'] = $user_info;

          }

          return $list;
    }

    /** 获取财富树下的支出和收入 */
    public function incomeAndExpense($uid, $tree_id)
    {

        /** 支出 */
        $expense = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->where('money', '<', 0)->sum('money');

        /** 收入 */
        $income = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->where('money', '>', 0)->sum('money');

        return ['income' => $income, 'expense' => $expense];
    }
}