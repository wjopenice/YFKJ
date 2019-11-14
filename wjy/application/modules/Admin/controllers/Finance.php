<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";


class FinanceController extends AdminApi {

    /** 财富树列表 */
    public function payOrderListAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('order_no'))
        {
            $where[] = ['order_no', '=', input('order_no')];
        }
        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 状态 */
        if (input('paystate'))
        {
            $where[] = ['state', '=', input('paystate')];
        }

        $list = \think\Db::name('pay_order')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $list[$key]['pay_time'] = date('Y-m-d H:i:s', $item['pay_time']);
            $list[$key]['order_no'] = $item['order_no'].' ';
        }
        $total = \think\Db::name('pay_order')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }


    /** 提现列表 */
    public function cashListAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('order_no'))
        {
            $where[] = ['order_no', '=', input('order_no')];
        }
        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 状态 */
        if (input('state'))
        {
            $where[] = ['state', '=', input('state')];
        }

        $list = \think\Db::name('cash')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $list[$key]['order_no'] = $item['order_no'].' ';
        }
        $total = \think\Db::name('cash')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 修改提现状态 */
    public function setCashStateAction()
    {

        $id = input('cash_id');
        $type = input('type');

        if (empty($id) || empty($type))
        {

            $this->error();
        }

        if ($type == 'agree')
        {
            // 发送提现请求
            $cash = \think\Db::name('cash')->where('id', $id)->find();

            // 验证本人账单记录
            $bill_money = \think\Db::name('bill')->where(['uid' => $cash['uid'], 'currency_id' => $cash['currency_id']])->sum('money');
            // 钱包余额是否相符
            $wallet_money = \think\Db::name('wallet')->where(['uid' => $cash['uid'], 'currency_id' => $cash['currency_id']])->value('total');

            if ($bill_money != $wallet_money)
            {
                $this->error('当前账号财务信息不匹配, 建议人工审核');
            }

            $witdrawData['address'] = $cash['address'];
            $witdrawData['order_no'] = $cash['order_no'];
            $witdrawData['money'] = $cash['money'];
            $witdrawData['currency'] = $cash['currency_id'] == 1 ? 'usdt' : 'dyx';
            $cash_res = Tool::withdraw($witdrawData);
            if ($cash_res['msg'] == 'success' && $cash_res['code'] == 0)
            {
                \think\Db::name('cash')->where('id', $id)->update(['state' => 2]);
                $res = true;

            } else
            {
                // 发送提现申请失败
                logs('发送提现申请失败'.json_encode($cash_res), '', 'notifyerror');
                $res = false;
            }

        }else if ($type == 'refuse')
        {
            $res = \think\Db::name('cash')->where('id', $id)->update(['state' => 3]);
        }

        if ($res)
        {
            $this->success();
        }

        $this->error();
    }


    /** 充值列表 */
    public function rechargeListAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 币种 */
        if (input('coinType'))
        {
            $where[] = ['coinType', '=', input('coinType')];
        }

        $list = \think\Db::name('recharge')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {

            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

        }
        $total = \think\Db::name('recharge')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 充值excel */
    public function rechargeExcelAction(){


        $where = [];
        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 币种 */
        if (input('coinType'))
        {
            $where[] = ['coinType', '=', input('coinType')];
        }
        $list = \think\Db::name('recharge')->where($where)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {

            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

        }
        $this->success($list);
    }

    /** 后台充值记录 */
    public function billAdminAction()
    {

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 币种 */
        if (input('currency'))
        {
            $where[] = ['currency_id', '=', input('currency')];
        }
        /** 时间 */
        if (input('date'))
        {
            $whereTime = (['create_time', 'between', input('date')]);
        }
        $where[] = ['remark', '=', '后台操作'];

        $list = \think\Db::name('bill')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $total = \think\Db::name('bill')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }
}
