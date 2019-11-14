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
             return error('订单信息错误');
         }

        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $pay_order['currency_id']])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $money = $pay_order['money'];
        if ($money < 0)
        {
            return error('余额不足');
        }
        if (empty($wallet) || $wallet['free'] < $money)
        {
            return error('余额不足');
        }

        $pay_order_up['state'] = 2;
        $pay_order_up['pay_time'] = time();

        $wallet_up['total'] = bcsub($wallet['total'], $money, 3);
        $wallet_up['free'] = bcsub($wallet['free'], $money, 3);
        $wallet_up['consume'] = bcadd($wallet['consume'], $money, 3);
        $wallet_up['update_time'] = time();

        $remark = [
            1 => '创建财富树消费',
            2 => '加入财富树消费',
            3 => '升级财富树消费'
        ];
        /** 账单记录 */
        $bill_add['uid'] = $uid;
        $bill_add['currency_id'] = $pay_order['currency_id'];
        $bill_add['order_no'] = $order_no;
        $bill_add['money'] = -$money;
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
            return error('支付失败');
        }

    }


    /**
     * 修改钱包余额
     */
    public function addMoney($uid, $currency, $money, $order_no = '', $remark = '')
    {
        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $currency])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        // 强制保留三位小数
        $money = floor($money * 1000) / 1000;
        $wallet_up['total'] = bcadd($wallet['total'], $money, 3);
        $wallet_up['free'] = bcadd($wallet['free'], $money, 3);
        if ($money < 0)
        {
            $wallet_up['consume'] = bcsub($wallet['consume'], $money, 3);
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

    /** 提现 */
    public function cashMoney($uid, $currency, $cashInfo)
    {

        /** 获取当前币种 */
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $currency])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $wallet_up['total'] = bcsub($wallet['total'], $cashInfo['total'], 3);
        $wallet_up['free'] = bcsub($wallet['free'], $cashInfo['total'], 3);
        if ($wallet_up['total'] < 0 || $wallet_up['free'] < 0 || $cashInfo['total'] <= 0)
        {
            return error('金额不足');
        }
        $wallet_up['consume'] = bcadd($wallet['consume'], $cashInfo['total'], 3);
        $wallet_up['update_time'] = time();
        /** 生成提现订单 */
        $order_no = $this->cashOrderno();

        if (is_error($order_no))
        {
            return $order_no;
        }
        $user_address = Db::name('wallet')->where(['uid' => $uid, 'currency_id' => $currency])->value('address');
        $cash['order_no'] = $order_no;
        $cash['uid'] = $uid;
        $cash['currency_id'] = $currency;
        $cash['total'] = $cashInfo['total'];
        $cash['money'] = $cashInfo['money'];
        $cash['service'] = $cashInfo['service'];
        $cash['address'] = $cashInfo['address'];
        $cash['state'] = $cashInfo['state'];
        $cash['create_time'] = time();


        /** 账单记录 */
        $bill_add['uid'] = $uid;
        $bill_add['currency_id'] = $currency;
        $bill_add['order_no'] = $order_no;
        $bill_add['money'] = -$cashInfo['total'];
        $bill_add['remark'] = '用户提现';
        $bill_add['create_time'] = time();

        Db::startTrans();
        try {

            $cash_id = Db::name('withdraw')->insertGetId($cash);
            Db::name('bill')->insert($bill_add);
            $this->where(['id' => $wallet['id']])->update($wallet_up);
            Db::commit();
            return ['result' => 'success', 'balance' => $wallet_up['free'], 'order_no' => $order_no, 'cash_id' => $cash_id];

        } catch (\Exception $e) {
            Db::rollback();
            return error('提现失败');
        }
    }

    /** 钱包余额地址信息 */
    public function walletInfo($uid, $currency = 1)
    {
        $wallet = $this->where(['uid' => $uid, 'currency_id' => $currency])->field(['id', 'total', 'free','address'])->find();
        return ['money' => $wallet['free'], 'address' => $wallet['address']];
    }
    /** 获取所有币种余额 */
    static function getUserWallet($uid, $keyword = '')
    {

        $where = [];
        if (!empty($keyword))
        {
            $where[] = ['name', 'like', $keyword.'%'];
        }
        /** 先获取币种 不用链表处理了 */
        $currency_list = Db::name('currency')->where($where)->field('id,name,tag,icon,state')->order('order_id')->select();
        foreach ($currency_list as $key => $item) {

            $total = self::where(['uid' => $uid, 'currency_id' => $item['id']])->value('total');
            $currency_list[$key]['balance'] = $total;
            $currency_list[$key]['icon'] = tomedia($item['icon']);

        }

        return $currency_list;

    }

    /** 获取某币种余额 */
    public function getUserWalletByCurrency($uid, $currency_id)
    {

        $free = $this->where(['uid' => $uid, 'currency_id' => $currency_id])->value('free');
        return $free;
    }

    /** 获取某个金币的详情 */
    static function getCurrencyInfo($uid, $currency_id)
    {
        /** 获取地址 生成二维码 */
        $user_address = self::where(['uid' => $uid, 'currency_id' => $currency_id])->value('address');
        $currency = Db::name('currency')->where('id', $currency_id)->field('id,name,tag,icon')->find();
        $currency['icon'] = tomedia($currency['icon']);
        $currency_wallet = self::where(['uid' => $uid, 'currency_id' => $currency_id])->field('total,free,lock')->find();
        $currency['wallet'] = $currency_wallet;
        $currency['address'] = $user_address;

        /** 生成文件名 */
        $filename = 'address/'.$user_address.'.png';
        /** 生成二维码图片 */
        QRcode::png($user_address, APP_ATTACHMENT.'/'.$filename, 'L', 6, 2);
        $currency['address_qrcode'] = tomedia($filename);

        return $currency;
    }

    /** 提现订单号 */
    public function cashOrderno($i = 0)
    {
        if ($i > 50)
        {
            logs('获取订单号失败', '', 'cashorderno');
            return error('订单号获取失败');
        }

        $order_no = create_order_no();
        $order_no = 'WTC'.$order_no;
        $res = Db::name('withdraw')->where(['order_no' => $order_no])->find();
        if (empty($res))
        {
            return $order_no;
        }

        return $this->cashOrderno($i + 1);
    }

    /** 充值记录 */
    public function getRechargeLog($where)
    {
        $page = $where['page'] ? $where['page'] : 1;
        $name = Db::name('currency')->where('id', $where['currency_id'])->value('name');
        $list = Db::name('recharge')->where(['uid' => $where['uid'], 'coinType' => $name])->page($page, 10)->select();
        return $list;

    }

    /** 转账 */
    public function transfer($from, $to, $money, $currency_id = 1)
    {
         /** 获取转账人信息 */
         $from_wallet = $this->where(['uid' => $from, 'currency_id' => $currency_id])->field(['id', 'total', 'free', 'lock', 'consume'])->find();
         $to_wallet = $this->where(['uid' => $to, 'currency_id' => $currency_id])->field(['id', 'total', 'free', 'lock', 'consume'])->find();
         if (empty($from_wallet) || empty($to_wallet))
         {
             return error('未获取到用户信息');
         }
         if ($from_wallet['free'] < $money)
         {
             return error('余额不足');
         }
         /** 转账人信息 */
        $from_wallet_up['total'] = bcsub($from_wallet['total'], $money, 3);
        $from_wallet_up['free'] = bcsub($from_wallet['free'], $money, 3);
        if ($from_wallet_up['total'] < 0 || $from_wallet_up['free'] < 0 || $money <= 0)
        {
            return error('金额不足');
        }
        $from_wallet_up['consume'] = bcadd($from_wallet['consume'], $money, 3);
        /** 账单记录 */
        $bill_from['uid'] = $from;
        $bill_from['currency_id'] = $currency_id;
        $bill_from['order_no'] = '';
        $bill_from['money'] = -$money;
        $bill_from['remark'] = '转账支出';
        $bill_from['create_time'] = time();
        /** 收款人信息 */
        $to_wallet_up['total'] = bcadd($to_wallet['total'], $money, 3);
        $to_wallet_up['free'] = bcadd($to_wallet['free'], $money, 3);
        /** 账单记录 */
        $bill_to['uid'] = $to;
        $bill_to['currency_id'] = $currency_id;
        $bill_to['order_no'] = '';
        $bill_to['money'] = $money;
        $bill_to['remark'] = '转账收入';
        $bill_to['create_time'] = time();
        /** 转账记录 */
        $transfer_add['fuid'] = $from;
        $transfer_add['tuid'] = $to;
        $transfer_add['currency_id'] = $currency_id;
        $transfer_add['money'] = $money;
        $transfer_add['create_time'] = time();

        Db::startTrans();
        try {

            $this->where(['id' => $from_wallet['id']])->update($from_wallet_up);
            $this->where(['id' => $to_wallet['id']])->update($to_wallet_up);
            Db::name('bill')->insert($bill_from);
            Db::name('bill')->insert($bill_to);
            Db::name('transfer')->insert($transfer_add);
            Db::commit();
            return ['result' => 'success', 'balance' => $from_wallet_up['free']];

        } catch (\Exception $e) {
            Db::rollback();
            return error('转账失败');
        }



    }
    /** 结算 */
    public function settlement($count, $currency_id)
    {

        /** 获取当前币种 */
        $wallet = self::where(['uid' => 1, 'currency_id' => $currency_id])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $money = $count;
        if ($money < 0)
        {
            return error('请输入合法金额');
        }
        if (empty($wallet) || $wallet['free'] < $money)
        {
            return error('余额不足');

        }

        $wallet_up['total'] = $wallet['total'] - $money;
        $wallet_up['free'] = $wallet['free'] - $money;
        $wallet_up['consume'] = $wallet['consume'] + $money;
        $wallet_up['update_time'] = time();

        /** 账单记录 */
        $bill_add['uid'] = 1;
        $bill_add['currency_id'] = $currency_id;
        $bill_add['order_no'] = '';
        $bill_add['money'] = -$money;
        $bill_add['remark'] = '平台结算';
        $bill_add['create_time'] = time();

        /** 结算记录 */
        $settlement_add['count'] = $count;
        $settlement_add['currency_id'] = $currency_id;
        $settlement_add['create_time'] = time();
        $settlement_add['update_time'] = time();

        Db::startTrans();
        try {

            Db::name('settlement')->insert($settlement_add);
            Db::name('bill')->insert($bill_add);
            $this->where(['id' => $wallet['id']])->update($wallet_up);
            Db::commit();
            return ['result' => 'success'];

        } catch (\Exception $e) {
            Db::rollback();
            return error('结算失败');
        }

    }

}
