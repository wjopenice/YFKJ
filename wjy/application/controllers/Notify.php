<?php

use think\Db;
include APP_MODULES.'/Api/models/Wallet.php';
include APP_MODULES.'/Api/models/Profit.php';

class NotifyController extends Controller
{

    /**
     * 初始化 REST 路由
     * 修改操作 和 绑定参数
     *
     * @access protected
     */
    protected function init()
    {
        parent::init();
        Yaf_Dispatcher::getInstance()->disableView(); //立即输出响应，并关闭视图模板
    }

    public function callbackAction()
    {
        $input = file_get_contents('php://input');
        logs($input, '', 'notify');

        $result = json_decode($input,true);
        if($result['direction'] == 'in' &&  $result['type'] == "cold"){

            // 充值成功 (冷钱包充值)
            $address = $result['address'];
            $recharge['order_no'] = '';
            $recharge['count'] = sprintf("%.3f",$result['qty']);
            $recharge['create_time'] = substr($result['updatetime'],0,-3);
            $recharge['address'] = $address;
            $recharge['status'] = 1;
            if(strpos($result['coinType'], 'DYX') !== false)
            {
                $recharge['coinType'] = 'DYX';
                $currency_id = 2;

            }else if(strpos($result['coinType'], 'USDT') !== false)
            {
                $recharge['coinType'] = 'USDT';
                $currency_id = 1;

            }else {

                // 暂不支持币种
                logs('不支持币种充值:'.$result['coinType'], '', 'notifyerror');
            }

            // 根据地址获取用户信息
            $uid = Db::name('wallet')->where(['address' => $address])->value('uid');
            $recharge['uid'] = $uid;

            /** 更新用户余额 */
            $walletModel = new WalletModel();
            Db::startTrans();
            try {

                Db::name('recharge')->insert($recharge);
                $walletModel->addMoney($uid, $currency_id,  $recharge['count'], '', '用户充值');
                Db::commit();
                echo json_encode(['code' => 0]);die();

            } catch (\Exception $e) {
                Db::rollback();
                echo json_encode(['code' => 0]);die();
            }


        } else if($result['direction'] == 'in' &&  $result['type'] == "hot"){

            // 热钱包充值
            $address = $result['address'];
            $rechdata['order_no'] = '';
            $recharge['count'] = sprintf("%.3f",$result['qty']);
            $rechdata['create_time'] = substr($result['updatetime'], 0, -3);
            $rechdata['address'] = $address;
            $rechdata['status'] = 1;
            if(strpos($result['coinType'], 'DYX') !== false)
            {
                $recharge['coinType'] = 'DYX';


            }else if(strpos($result['coinType'], 'USDT') !== false)
            {
                $recharge['coinType'] = 'USDT';


            }else {

                $recharge['coinType'] = $result['coinType'];
                // 暂不支持币种
                logs('不支持币种充值:'.$result['coinType'], '', 'notifyerror');
            }
            $rechdata['uid'] = 0;
            Db::name('recharge')->insert($recharge);
            echo json_encode(['code' => 0]);die();

        }else if($result['type'] == "withdraw"){

            // 订单号
            $order_no = $result['serialNumber'];
            // 获取提现记录
            $cash_log = Db::name('cash')->where(['order_no' => $order_no])->find();
            if(!empty($cash_log))
            {

                // 订单状态为2是正常需要处理的
                if ($cash_log['state'] == 2)
                {
                    // 更新提现状态为已完成 4
                    $cash_up['state'] = 4;
                    $cash_up['update_time'] = time();
                    $res = Db::name('cash')->where(['order_no' => $order_no])->update($cash_up);
                    if (empty($res))
                    {
                        // 暂不支持币种
                        logs('提现状态修改失败:'.$order_no, '', 'notifyerror');
                    }
                    $wallet_model = new WalletModel();
                    Db::startTrans();
                    try {

                        // 增加平台收益
                        $wallet_model->addMoney(1, $cash_log['currency_id'], $cash_log['service'], $order_no, '提现手续费收益');
                        // 增加收益记录
                        ProfitModel::addProfit($cash_log['id'], $cash_log['service'], $cash_log['currency_id'], 6);

                    } catch (\Exception $e) {
                        logs('提现手续费到账失败:'.$order_no, '', 'notifyerror');
                        Db::rollback();
                    }

                    echo json_encode(['code' => 0]);die();
                }

                echo json_encode(['code' => 0]);die();

            }else{

                //没有订单
                logs('无订单的提现:'.$order_no, '', 'notifyerror');
                echo json_encode(['code' => 0]);die();
            }
        } else
        {
            // 其他功能
            echo json_encode(['code' => 0]);die();
        }

    }

    public function testAction()
    {
        //充值
//        $input = '{"blockHight":"8494598","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT","createtime":"1567748382000","address":"0x220df565654c987195e3639e029a6295ccbb88fd","uniquekey":"0x006b1974d87885af4aed34ed46aa3dc7136cc75b90793802e0e934e3c32640be0xdac17f958d2ee523a2206206994597c13d831ec7ETH_USDT0x1f77c901cd0420cd6073ab641da1f9a3cb93c76e","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"dd182993a64b76d039713daa2db819dd10bea87c2b671ad6907c2f94b86b1e40","txid":"0x006b1974d87885af4aed34ed46aa3dc7136cc75b90793802e0e934e3c32640be","type":"cold","userid":"1567578224910","curBlockHight":"0","blockhash":"0x23da194ba38dde2cc500b67b5ee19df0aae9dd1d5e9da4dd0c18cf1866205f3e","secondNoticeCounter":"0","firstNoticeCounter":"0","qty":"302.78000000","id":"50366","updatetime":"1567748382000","direction":"in","status":"new"}';
//提现
//        $input = '{"blockHight":"0","coinType":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","symbol":"ETH_USDT_0xdac17f958d2ee523a2206206994597c13d831ec7","createtime":"1568007830000","address":"0x261B56Ef88241BE0f307ba5809c80afc64FdD6E0","serialNumber":"WC2019092786282251708354","uniquekey":"5d75e693d7980","contract":"0xdac17f958d2ee523a2206206994597c13d831ec7","sign":"f7d217bd9a4a473615671aa078fb413f83eb3242bdea7988bcb62dc0521ab5f3","txid":"0x3902956a87fda2497df2b2070c50e3e219f33fdd98f73be3222d058a52b8606c","type":"withdraw","userid":"1567578224910","curBlockHight":"0","blockhash":"\"\"","secondNoticeCounter":"0","firstNoticeCounter":"99","qty":"1.00000000","id":"50441","updatetime":"1568007830000","direction":"in","status":"new"}';
    }
}
