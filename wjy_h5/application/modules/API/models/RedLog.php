<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class RedLogModel extends Model
{

    /** 群主收益 */
    public function sendRewardForGroupOwner($uid, $info)
    {

        /** 群主收益 */
        /** 分销奖励金额 */
        $dis_money = bcdiv(bcmul($info['group_owner'], 15, 3), 100, 3);
        $redlog_g['uid'] = $uid;
        $redlog_g['from_uid'] = 0;
        $redlog_g['money'] = bcsub($info['group_owner'], $dis_money, 3);
        $redlog_g['order_no'] = $info['order_no'];
        $redlog_g['redpacket_id'] = $info['redpacket_id'];
        $redlog_g['currency_id'] = $info['currency_id'];
        $redlog_g['scene'] = 1;
        $redlog_g['remark'] = '群主收益';
        $info['remark'] = '群主收益';
        $redlog_g['create_time'] = time();

        $wallet_model = new WalletModel();
        Db::startTrans();
        try {

            $this->insert($redlog_g);
            $wallet_model->addMoney($redlog_g['uid'], $redlog_g['currency_id'], $redlog_g['money'],  $redlog_g['order_no'], $redlog_g['remark']);
            Db::commit();


        } catch (\Exception $e) {

            logs($info['order_no'].'群主收益失败', '', 'redcommission');
            Db::rollback();


        }

        /** 分销奖励 */
        $info['scene'] = 2;
        $info['money'] = $info['group_owner'];
        $user_model = new UserModel();
        $user_model->sendDisReward($uid, $info);

    }

    /** 红包收益 */
    public function sendRewardForRedlog($uid, $info)
    {
        /** 分销奖励 */
        $info['scene'] = 2;
        $info['money'] = $info['group_owner'];
        $user_model = new UserModel();
        $user_model->sendDisReward($uid, $info);
    }

    /** 平台红包佣金 */
    public function platformCommission($uid, $info)
    {
        $redlog_p['uid'] = 1;
        $redlog_p['from_uid'] = $uid;
        $redlog_p['money'] = $info['platform'];
        $redlog_p['order_no'] = $info['order_no'];
        $redlog_p['redpacket_id'] = $info['redpacket_id'];
        $redlog_p['currency_id'] = $info['currency_id'];
        $redlog_p['scene'] = 1;
        $redlog_p['remark'] = '平台红包手续费';
        $redlog_p['create_time'] = time();
        
        $wallet_model = new WalletModel();
        /** 修改余额 */
        Db::startTrans();
        try {

            $this->insert($redlog_p);
            $wallet_model->addMoney($redlog_p['uid'], $redlog_p['currency_id'], $redlog_p['money'], $redlog_p['order_no'], $redlog_p['remark']);
            Db::commit();
            return ['result' => 'success'];

        } catch (\Exception $e) {
            Db::rollback();
            logs($info['order_no'].'平台收取红包佣金失败', '', 'redcommission');
            return error('send fail');
        }


    }
}