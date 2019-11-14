<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";
include APP_MODULES.'/Api/models/Wallet.php';
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
        // 今日提现
        $withdraw_day = Db::name('cash')->whereTime('create_time', 'today')->sum('money');
        // 今日充值
        $recharge_day = Db::name('recharge')->whereTime('create_time', 'today')->sum('count');
        /** 已充值用户 */
        $recharge_user = Db::name('recharge')->group('uid')->count();
        /** 今日日活 */
        $active = Db::name('active')->whereTime('create_time', 'today')->find();
        $active['users'] = $active['users'] ? explode(',', $active['users']) : [];
        $active_day = count($active['users']);
        /** 财富树数量 */
        $tree_count = Db::name('tree')->count();
        /** 红包群数量 */
        $redgroup_count = Db::name('redgroup')->count();

        $data['user_count'] = $user_count;
        $data['user_count_week'] = $user_count_week;
        $data['user_count_day'] = $user_count_day;
        $data['withdraw_day'] = $withdraw_day;
        $data['recharge_day'] = $recharge_day;
        $data['recharge_user'] = $recharge_user;
        $data['active_day'] = $active_day;
        $data['tree_count'] = $tree_count;
        $data['redgroup_count'] = $redgroup_count;
        $this->success($data);
    }


    public function profitAction()
    {
        $currency_id = input('currency_id');
        $where = [];
        if (!empty($currency_id))
        {
            $where['id'] = $currency_id;
        }

        $currencyList = Db::name('currency')->where($where)->select();
        foreach ($currencyList as $key => $item)
        {
            /** 平台余额 */
            $platform_money = Db::name('wallet')->where(['uid' => 1, 'currency_id' => $item['id']])->find();
           // print_r($platform_money);
            $currencyList[$key]['platform_money'] = $platform_money['free'];
            /** 平台存量 */
            $stock = Db::name('wallet')->where(['currency_id' => $item['id']])->sum('total');
            $currencyList[$key]['stock'] = $stock;
            /** 今日充值 */
            $recharge_day = Db::name('recharge')->where(['coinType' => $item['name']])->whereTime('create_time', 'today')->sum('count');
            $currencyList[$key]['recharge_day'] = $recharge_day;
            /** 今日提现 */
            $withdraw_day = Db::name('cash')->where(['currency_id' => $item['id']])->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['withdraw_day'] = $withdraw_day;
            /** 红包群数量 */
            $redgroup_count = Db::name('redgroup')->where(['currency_id' => $item['id']])->count();
            $currencyList[$key]['redgroup_count'] = $redgroup_count;
            /** 财富树数量 */
            $tree_count = Db::name('tree')->where(['currency_id' => $item['id']])->count();
            $currencyList[$key]['tree_count'] = $tree_count;

            /** 收益信息 */
            /** 红包获取总收益 */
            $redProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 3], ['=', 4], 'or')->sum('money');
            $currencyList[$key]['redProfit'] = $redProfit;

            /** 红包今日收益 */
            $redProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 3], ['=', 4], 'or')->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['redProfit_today'] = $redProfit_today;

            /** 财富树收益总收益 */
            $treeProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 1], ['=', 2], 'or')->sum('money');
            $currencyList[$key]['treeProfit'] = $treeProfit;

            /** 财富树收益总收益 */
            $treeProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', ['=', 1], ['=', 2], 'or')->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['treeProfit_today'] = $treeProfit_today;

            /** 推广总收益 */
            $disProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 5)->sum('money');
            $currencyList[$key]['disProfit'] = $disProfit;
            $disProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 5)->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['disProfit_today'] = $disProfit_today;

            /** 手续费收益 */
            $serviceProfit = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 6)->sum('money');
            $currencyList[$key]['serviceProfit'] = $serviceProfit;
            $serviceProfit_today = Db::name('profit')->where(['currency_id' => $item['id']])->where('scene', 6)->whereTime('create_time', 'today')->sum('money');
            $currencyList[$key]['serviceProfit_today'] = $serviceProfit_today;

            $totalProfit = $redProfit + $treeProfit + $disProfit + $serviceProfit;
            $currencyList[$key]['totalProfit'] = $totalProfit;
            $totalProfit_today = $redProfit_today + $treeProfit_today + $disProfit_today + $serviceProfit_today;
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
        $res = WalletModel::settlement($count, $currency_id);
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
