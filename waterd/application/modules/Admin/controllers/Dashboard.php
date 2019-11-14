<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";
use think\Db;

class DashboardController extends AdminApi {

    /** 列表 */
    public function indexAction(){

        // 会员总数
        $user_count = Db::name('user')->count();
        // 本周会员总数
        $user_count_week = Db::name('user')->whereTime('create_time', 'week')->count();
        // 今日新增
        $user_count_day = Db::name('user')->whereTime('create_time', 'today')->count();
        /** 已充值用户 */
        $recharge_user = Db::name('recharge')->group('uid')->count();
        /** 今日日活 */
        $active = Db::name('active')->whereTime('create_time', 'today')->find();
        $active['users'] = $active['users'] ? explode(',', $active['users']) : [];
        $active_day = count($active['users']);
        /** 财富树数量 */
        $tree_count = Db::name('tree')->count();

        $data['user_count'] = $user_count;
        $data['user_count_week'] = $user_count_week;
        $data['user_count_day'] = $user_count_day;
        $data['recharge_user'] = $recharge_user;
        $data['active_day'] = $active_day;
        $data['tree_count'] = $tree_count;

        $this->success($data);
    }


    public function profitAction()
    {

        $currencyList = Db::name('currency')->where([])->select();
        foreach ($currencyList as $key => $item)
        {

            /** 平台余额 */
            $platform_money = Db::name('wallet')->where(['uid' => 1, 'currency_id' => $item['id']])->find();
            $currencyList[$key]['platform_money'] = $platform_money['free'];

            /** 平台存量 */
            $stock = Db::name('wallet')->where(['currency_id' => $item['id']])->sum('total');
            $currencyList[$key]['stock'] = $stock;

            /** 今日充值 */
            $recharge_day = Db::name('recharge')->where(['coinType' => $item['name']])->whereTime('create_time', 'today')->sum('count');
            $currencyList[$key]['recharge_day'] = $recharge_day;

            /** 今日提现 */
            $withdraw_day = Db::name('withdraw')->where(['currency_id' => $item['id'], 'state' => 4])->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['withdraw_day'] = $withdraw_day;

            /** 收益信息 */

            /** 财富树收益总收益 */
            $treeProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 1], ['=', 2], 'or')->sum('money');
            $currencyList[$key]['treeProfit'] = $treeProfit;

            /** 财富树收益总收益 */
            $treeProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 1], ['=', 2], 'or')->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['treeProfit_today'] = $treeProfit_today;

            /** 手续费收益 */
            $serviceProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 6)->sum('money');
            $currencyList[$key]['serviceProfit'] = $serviceProfit;
            $serviceProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 6)->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['serviceProfit_today'] = $serviceProfit_today;

            $totalProfit = $treeProfit + $serviceProfit;
            $currencyList[$key]['totalProfit'] = $totalProfit;
            $totalProfit_today = $treeProfit_today + $serviceProfit_today;
            $currencyList[$key]['totalProfit_today'] = $totalProfit_today;
        }


        $this->success(['currency' => $currencyList]);
    }

    /** 结算记录 */
    public function settlementListAction()
    {
        $currency_id = input('currency_id');
        $list = Db::name('settlement')->where(['currency_id' => $currency_id])->select();
        foreach ($list as $key => $item)
        {
           $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $this->success(['list' => $list]);
    }

    /** 结算 */
    public function settlementAction()
    {
        $currency_id = input('currency_id');
        $count = input('count');
        if (empty($currency_id) || empty($count))
        {
            $this->error('请输入正确币种和金额');
        }
        $WalletModel = new WalletModel();
        $res = $WalletModel->settlement($count, $currency_id);
        if (is_error($res))
        {
            $this->error($res['message']);
        }

        if ($res['result'] == 'success')
        {
            $this->success(['state' => 1]);

        } else
        {
            $this->success(['state' => 2]);
        }


    }

    /** 收益信息 */
    public function profit1Action()
    {
        /** 获取币种 */
        $currencyList = Db::name('currency')->select();
        $list['total'] = [];
        $list['red'] = [];
        $list['tree'] = [];
        $list['dis'] = [];
        foreach ($currencyList as $key => $item)
        {
            /** 红包获取总收益 */
            $redProfit = Db::name('red_log')->where(['uid' => 1, 'currency_id' => $item['id']])->sum('money');
            $item['redProfit'] = $redProfit;

            /** 红包今日收益 */
            $redProfit_today = Db::name('red_log')->where(['uid' => 1, 'currency_id' => $item['id']])->whereTime('create_time', 'today')->sum('money');
            $item['redProfit_today'] = $redProfit_today;

            /** 财富树收益总收益 */
            $treeProfit = Db::name('tree_log')->where(['uid' => 1, 'currency_id' => $item['id']])->sum('money');
            $item['treeProfit'] = $treeProfit;
            /** 财富树收益总收益 */
            $treeProfit_today = Db::name('tree_log')->where(['uid' => 1, 'currency_id' => $item['id']])->whereTime('create_time', 'today')->sum('money');
            $item['treeProfit_today'] = $treeProfit_today;

            /** 推广总收益 */
            $disProfit = Db::name('dis_log')->where(['uid' => 1, 'currency_id' => $item['id']])->sum('money');
            $item['disProfit'] = $disProfit;
            $disProfit_today = Db::name('dis_log')->where(['uid' => 1, 'currency_id' => $item['id']])->whereTime('create_time', 'today')->sum('money');
            $item['disProfit_today'] = $disProfit_today;

            /** 总收益 */
            $total = $redProfit + $treeProfit + $disProfit;
            $item['totalProfit'] = $total;
            /** 今日总收益 */
            $total_today = $redProfit_today + $treeProfit_today + $disProfit_today;
            $item['totalProfit_today'] = $total_today;
            $list['total'][] = $item;
            $list['red'][] = $item;
            $list['tree'][] = $item;
            $list['dis'][] = $item;


            /** 充值 */
            $rechargeTotal = Db::name('recharge')->where('coinType', $item['name'])->sum('count');
            $currencyList[$key]['rechargeTotal'] = $rechargeTotal;
            $recharge_today = Db::name('recharge')->where('coinType', $item['name'])->whereTime('create_time', 'today')->sum('count');
            $currencyList[$key]['recharge_today'] = $recharge_today;
            /** 提现 */
            $cashTotal = Db::name('cash')->where(['currency_id' => $item['id'], 'state' => 4])->sum('money');
            $cash_today = Db::name('cash')->where(['currency_id' => $item['id'], 'state' => 4])->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['cashTotal'] = $cashTotal;
            $currencyList[$key]['cash_today'] = $cash_today;
        }


        $this->success(['profit' => $list, 'currencyList' => $currencyList]);

    }

}
