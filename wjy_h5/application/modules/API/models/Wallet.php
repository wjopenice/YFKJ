<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class WalletModel extends Model
{
    /**
     * 支付
     * return  array
     */
    public function payMoney($uid, $order_no)
    {

        /** 通过订单号获取订单信息 */
         $pay_order = Db::name('pay_order')->where(['order_no' => $order_no, 'uid' => $uid])->field('state,type,money,currency_id')->find();
         if (empty($pay_order))
         {
             return error('order info error');
         }

        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $pay_order['currency_id']])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $money = $pay_order['money'];
        if (empty($wallet) || $wallet['free'] < $money)
        {
            return error('money is not enough');

        }

        $pay_order_up['state'] = 2;
        $pay_order_up['pay_time'] = time();

        $wallet_up['total'] = bcsub($wallet['total'], $money, 2);
        $wallet_up['free'] = bcsub($wallet['free'], $money, 2);
        $wallet_up['consume'] = bcadd($wallet['consume'], $money, 2);
        $wallet_up['update_time'] = time();

        $remark = [
            1 => '创建财富树消费',
            2 => '加入财富树消费',
            3 => '升级财富树消费',
            4 => '创建红包消费',
            5 => '发送红包消费',
        ];
        /** 账单记录 */
        $bill_add['uid'] = $uid;
        $bill_add['currency_id'] = $pay_order['currency_id'];
        $bill_add['order_no'] = $order_no;
        $bill_add['money'] = $money;
        $bill_add['remark'] = $remark[$pay_order['type']];
        $bill_add['create_time'] = time();

        Db::startTrans();
        try {

            Db::name('pay_order')->where(['order_no' => $order_no])->update($pay_order_up);
            Db::name('bill')->insert($bill_add);
            $this->where(['id' => $wallet['id']])->update($wallet_up);
            Db::commit();
            return ['result' => 'success', 'order_no' => $order_no];

        } catch (\Exception $e) {
            Db::rollback();
            return error('pay fail');
        }

    }


    /**
     * 修改钱包余额
     */
    public function addMoney($uid, $currency, $money, $order_no = '', $remark = '')
    {
        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $currency])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $wallet_up['total'] = bcadd($wallet['total'], $money, 2);
        $wallet_up['free'] = bcadd($wallet['free'], $money, 2);
        if ($money < 0)
        {
            $wallet_up['consume'] = bcsub($wallet['consume'], $money, 2);
        }
        $wallet_up['update_time'] = time();


        $res = $this->where(['id' => $wallet['id']])->update($wallet_up);
        if ($res)
        {
            /** 账单记录 */
            $bill_add['uid'] = $uid;
            $bill_add['currency_id'] = $currency;
            $bill_add['order_no'] = $order_no;
            $bill_add['money'] = $money;
            $bill_add['remark'] = $remark;
            $bill_add['create_time'] = time();
            Db::name('bill')->insert($bill_add);
        }

        return  $res;



    }


    /** 获取钱包余额 */
    public function getMoney($uid, $currency)
    {
        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $currency])->field(['id', 'total', 'free'])->find();
        return $wallet['free'];
    }

    /** 获取所有币种余额 */
    static function getUserWallet($uid)
    {

        /** 先获取币种 不用链表处理了 */
        $currency_list = Db::name('currency')->field('id,name,tag,icon')->order('order_id')->select();
        foreach ($currency_list as $key => $item) {

            $total = self::where(['uid' => $uid, 'currency_id' => $item['id']])->value('total');
            $currency_list[$key]['balance'] = $total;
            $currency_list[$key]['icon'] = tomedia($item['icon']);

        }

        return $currency_list;

    }
}