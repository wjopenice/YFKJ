<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/10/29
 * Time: 14:08
 */

class TreeModel extends Model
{

    // 获取财富树列表
    public function getTreeList($uid, $where)
    {
        if (empty($uid))
        {
            return [];
        }
        $page = $where['page'] ? $where['page'] : 1;
        $psize = 8;
        $start = ($page - 1) * $psize;
        $sql = "SELECT `b`.`id`,`currency_id`,`b`.`level`,`b`.`uid`,`name`,`limit`,`money`,`growth_ratio` FROM `tree_user` `a` LEFT JOIN `tree` `b` ON `b`.`id`=`a`.`tree_id` WHERE `a`.`uid` = $uid ORDER BY `a`.`create_time` LIMIT $start,$psize";
        $treeList = Db::query($sql);
        $treeUser_model = new TreeUserModel();
        foreach ($treeList as $key => $item)
        {
            /** 获取币种 */
            $treeList[$key]['currency_name'] = 'USDT';
            /** 获取当前参与人数 */
            $join_count = $treeUser_model->getTreeCount($item['id']);
            $treeList[$key]['join_count'] = $join_count;

            if ($uid == $item['uid'])
            {
                $treeList[$key]['creater'] = true;
            } else
            {
                $treeList[$key]['creater'] = false;
            }

        }

        return $treeList;
    }

    // 获取财富树信息
    public function getTreeInfo($tree_id)
    {
        $tree = $this->where('id', $tree_id)->field('id,name,room_number,currency_id,level,limit,money,money_rmb,growth_ratio')->find();
        /** 获取当前参与人数 */
        $treeUser_model = new TreeUserModel();
        $join_count = $treeUser_model->getTreeCount($tree['id']);
        $tree['join_count'] = $join_count;
        /** 获取升级金额 */
        $upgradeMoney = [];
        $lowMoney = [];
        $upTotalMoney = 0;
        $lowTotalMoney = 0;
        $lowTotalCount = 0;
        /** 人民币 */
        $upgradeMoney_rmb = [];
        $lowMoney_rmb = [];
        $upTotalMoney_rmb = 0;
        $lowTotalMoney_rmb = 0;
        $lowTotalCount_rmb = 0;

        for ($i = 0; $i < $tree['level']; $i++)
        {
            $money = getTreeLevelMoeny($i+1, $tree['money'], (int)$tree['growth_ratio']);
            $upgradeMoney[] = $money;
            $upTotalMoney = bcadd($upTotalMoney, $money);
            $limit = $tree['limit'] == 0 ? 5 : $tree['limit'];
            $low_item['money'] = $money;
            $low_item['count'] = $count = pow($limit, $i+1);
            $low_item['total'] = $total = bcmul($money, $count);
            $lowTotalMoney = bcadd($lowTotalMoney, $total);
            $lowTotalCount = bcadd($lowTotalCount, $count);
            $lowMoney[] = $low_item;

            /** 获取人民币金额 */

            $money_rmb = getTreeLevelMoeny($i+1, $tree['money_rmb'], (int)$tree['growth_ratio']);
            $upgradeMoney_rmb[] = $money_rmb;
            $upTotalMoney_rmb = bcadd($upTotalMoney_rmb, $money_rmb);
            $limit_rmb = $tree['limit'] == 0 ? 5 : $tree['limit'];
            $low_item_rmb['money'] = $money_rmb;
            $low_item_rmb['count'] = $count_rmb = pow($limit_rmb, $i+1);
            $low_item_rmb['total'] = $total_rmb = bcmul($money_rmb, $count_rmb);
            $lowTotalMoney_rmb = bcadd($lowTotalMoney_rmb, $total_rmb);
            $lowTotalCount_rmb = bcadd($lowTotalCount_rmb, $count_rmb);
            $lowMoney_rmb[] = $low_item_rmb;


        }
        $tree['upgrade_money'] = $upgradeMoney;
        $tree['upTotalMoney'] = $upTotalMoney;
        $tree['low_money'] = $lowMoney;
        $tree['lowTotalMoney'] = $lowTotalMoney;
        $tree['lowTotalCount'] = $lowTotalCount;

        // 人民币
        $tree['upgrade_money_rmb'] = $upgradeMoney_rmb;
        $tree['upTotalMoney_rmb'] = $upTotalMoney_rmb;
        $tree['low_money_rmb'] = $lowMoney_rmb;
        $tree['lowTotalMoney_rmb'] = $lowTotalMoney_rmb;
        $tree['lowTotalCount_rmb'] = $lowTotalCount_rmb;

        $tree['profittimes'] = bcdiv($lowTotalMoney, $upTotalMoney, 2);
        /** 获取当前成长级 */
        $grow_up = $treeUser_model->where('tree_id', $tree['id'])->max('level');
        $tree['grow_up'] = $grow_up;

        return $tree;

    }

    // 创建财富树
    public function createTree($tree_add, $payMoney)
    {

        $tree_add['room_number'] = 0;
        $tree_add['create_time'] = time();

        /** 同时生成会员 */
        $tree_user['uid'] = $tree_add['uid'];
        $tree_user['puid'] = 0;
        $tree_user['level'] = 0;
        $tree_user['order_no'] = $tree_add['order_no'];
        $tree_user['vip_level'] = $tree_add['level'];
        $tree_user['create_time'] = time();
        $tree_user['update_time'] = time();

        $tree_log['currency_id'] = $tree_add['currency_id'];
        $tree_log['uid'] = $tree_add['uid'];
        $tree_log['from_uid'] = 0;
        $tree_log['loss_uid'] = 0;
        $tree_log['order_no'] = $tree_add['order_no'];
        $tree_log['scene'] = 0;
        $tree_log['money'] = -$payMoney;
        $tree_log['remark'] = '创建财富树';
        $tree_log['create_time'] = time();

        $treeUserMode = new  TreeUserModel();
        $treeLog_model = new TreeLogModel();
        $wallet_model = new WalletModel();
        $profit_model = new WalletModel();

        Db::startTrans();
        try {

            $tree_id = $this->insertGetId($tree_add);
            /** 处理邀请码  */
            $roomNumber = 'T'.(100000 + $tree_id);
            $this->where('id', $tree_id)->setField('room_number', $roomNumber);
            $tree_user['tree_id'] = $tree_id;
            $tree_log['tree_id'] = $tree_id;
            $treeUserMode->insert($tree_user);
            $treeLog_model->insert($tree_log);
            // 增加平台收益
            $wallet_model->addMoney(1, $tree_add['currency_id'], $payMoney, $tree_add['order_no'], '创建财富树收益');
            // 增加收益记录
            $profit_model->addProfit($tree_id, $payMoney, $tree_add['currency_id'], 1);
            Db::commit();
            return ['tree_id' => $tree_id];

        } catch (\Exception $e) {
            Db::rollback();
            return error('创建财富树失败');
        }

    }

    // 获取加入支付信息
    public function getJoinTreeInfo($tree_id)
    {
        $tree = $this->where('id', $tree_id)->field(['id', 'currency_id', 'level', 'limit', 'money', 'growth_ratio'])->find();
        return $tree;

    }

}
