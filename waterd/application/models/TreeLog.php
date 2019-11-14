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

        $tree_service = 0;
        /** 判断是否为总部 */
        if ($p_user['uid'] == 1)
        {
            /** 不需要手续费 */
            $log_add['uid'] = $p_user['uid'];
            $log_add['loss_uid'] = 0;
            $log_add['money'] = $info['money'];

        } else
        {

            /** 非总部 需要检查等级是否满足 需要手续费*/
            if ($p_user['vip_level'] >= $info['vip_level'])
            {
                // 10%手续费暂时去掉了
//                $tree_service = bcmul($info['money'], 0.1);
                $tree_service = 0;
                $send_money = $info['money'];
                $log_add['uid'] = $p_user['uid'];
                $log_add['loss_uid'] = 0;
                $log_add['money'] = $send_money;

            } else
            {
                /** 不满足 丢失奖励 直接给总部 不需手续费 */
                $log_add['uid'] = 1;
                $log_add['loss_uid'] = $p_user['uid'];
                $log_add['money'] = $info['money'];

            }
        }
        $ProfitModel = new ProfitModel();
        /** 修改余额 */
        Db::startTrans();
        try {

            $this->insert($log_add);
            $wallet_model->addMoney($log_add['uid'], $info['currency_id'], $log_add['money'], $info['order_no'], '财富树收益');
            /** 用户为平台时 增加收益记录 */
            if ($log_add['uid'] == 1)
            {
                $ProfitModel->addProfit($info['tree_id'], $log_add['money'], $info['currency_id'], 2);
            }
            if ($tree_service > 0)
            {
                $wallet_model->addMoney(1, $info['currency_id'], $tree_service, $info['order_no'], '财富树下级手续费');
                $ProfitModel->addProfit($info['tree_id'], $tree_service, $info['currency_id'], 3);
            }

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'].'奖励发送失败'.'id'.$info['tree_id'], '', 'treereward');
        }

    }

    /** 财富树加入奖励 */
    public function sendJoinReward($uid, $info)
    {
        /** 获取上级uid与会员等级 */
        $p_uid = Db::name('tree_user')->where(['uid' => $uid, 'tree_id' => $info['tree_id']])->value('tuid');
        if (empty($p_uid))
        {
            // 没有就给系统
            $p_uid = 1;
        }

        $wallet_model = new WalletModel();

        $log_add['currency_id'] = $info['currency_id'];
        $log_add['tree_id'] = $info['tree_id'];
        $log_add['from_uid'] = $uid;
        $log_add['order_no'] = $info['order_no'];
        $log_add['scene'] = $info['scene'] ? $info['scene'] : 1;
        $log_add['remark'] = $info['remark'];
        $log_add['create_time'] = time();
        $log_add['uid'] = $p_uid;
        $log_add['loss_uid'] = 0;
        $log_add['money'] = $info['money'];

        $ProfitModel = new ProfitModel();
        /** 修改余额 */
        Db::startTrans();
        try {

            $this->insert($log_add);
            $wallet_model->addMoney($log_add['uid'], $info['currency_id'], $log_add['money'], $info['order_no'], '财富树收益');
            /** 用户为平台时 增加收益记录 */
            if ($log_add['uid'] == 1) {
                $ProfitModel->addProfit($info['tree_id'], $log_add['money'], $info['currency_id'], 2);
            }

            Db::commit();

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'] . '奖励发送失败' . 'id' . $info['tree_id'], '', 'treereward');
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
        $list = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->field('id,uid,from_uid,money,remark,create_time')->page($page, 1000)->select();
        /** 获取用户信息 */
        $user_model = new UserModel();
        foreach ($list as $key => $item)
        {
            if ($item['from_uid'] == 0)
            {
                $user_info = $user_model->where('uid', $uid)->field('nickname,avatar,wechat_code')->find();

            } else
            {
                $user_info = $user_model->where('uid', $item['from_uid'])->field('nickname,avatar,wechat_code')->find();
            }
            $user_info['avatar'] = tomedia($user_info['avatar']);
            $user_info['wechat_code'] = tomedia($user_info['wechat_code']);
            $list[$key]['userinfo'] = $user_info;
            $list[$key]['datetime'] = date('Y.m.d', strtotime($item['create_time']));

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

        return ['income' => $income, 'expense' => -$expense];
    }

}
