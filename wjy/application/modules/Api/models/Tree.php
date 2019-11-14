<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

//include APP_MODULES.'/API/models/Currency.php';

use think\Db;

class TreeModel extends Model
{
    /**
     * 查询列表
     * return  array
     */
    public function getTreeList($uid, $where)
    {

//        $treeList = Db::name('tree_user')->as('a')->leftJoin('tree b', 'b.id = a.tree_id')->where('uid', $uid)->field('id,currency_id,level,limit,money,growth_ratio')->order('id')->select(false);
//        echo $treeList;die();
        $page = $where['page'] ? $where['page'] : 1;
        $psize = 20;
        $start = ($page - 1) * $psize;
          $sql = "SELECT `b`.`id`,`currency_id`,`b`.`level`,`b`.`uid`,`name`,`limit`,`money`,`growth_ratio` FROM `tree_user` `a` LEFT JOIN `tree` `b` ON `b`.`id`=`a`.`tree_id` WHERE `a`.`uid` = $uid ORDER BY `a`.`create_time` LIMIT $start,$psize";
        $treeList = Db::query($sql);
        $treeUser_model = new TreeUserModel();
        $currencyModel = new CurrencyModel();
        foreach ($treeList as $key => $item)
        {
            /** 获取币种 */
            $currency_name = $currencyModel->getName($item['currency_id']);
            $treeList[$key]['currency_name'] = $currency_name;
            /** 获取当前参与人数 */
            $join_count = $treeUser_model->getTreeCount($item['id']);
            $treeList[$key]['join_count'] = $join_count;
            /** 获取升级金额 */
            $upgradeMoney = [];
            for ($i = 0; $i < $item['level']; $i++)
            {
                $upgradeMoney[$i+1] = getTreeLevelMoeny($i+1, $item['money'], (int)$item['growth_ratio']);
            }

            if ($uid == $item['uid'])
            {
                $treeList[$key]['creater'] = true;
            } else
            {
                $treeList[$key]['creater'] = false;
            }

            $treeList[$key]['upgrade_money'] = $upgradeMoney;
        }

        return $treeList;
    }

    /** 创建财富树 */
    public function createTree($tree_add, $payMoney)
    {
        /** 获取币种标识 */
        $currency_model = new CurrencyModel();
        $currency_tag = $currency_model->getTage($tree_add['currency_id']);
        if (!$currency_tag)
        {
            return error('currency not find');
        }

        /** 房间号 */
//        $roomNumber = $this->getRoomNumber($currency_tag);
//        if (is_error($roomNumber))
//        {
//            return error('room number fail');;
//        }
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
            ProfitModel::addProfit($tree_id, $payMoney, $tree_add['currency_id'], 1);

            Db::commit();
            return ['tree_id' => $tree_id];

        } catch (\Exception $e) {
            Db::rollback();
            return error('create tree fail');
        }

    }

    /** 获取财富树信息 */
    public function getTreeInfo($roomNumber)
    {
         $tree = $this->where('room_number', $roomNumber)->field(['id', 'currency_id', 'name', 'level', 'limit', 'money', 'growth_ratio'])->find();

         /** 获取币种 */
         $currencyModel = new CurrencyModel();
         $currency_name = $currencyModel->getName($tree['currency_id']);
         $tree['currency_name'] = $currency_name;
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

         }
         $tree['upgrade_money'] = $upgradeMoney;
         $tree['upTotalMoney'] = $upTotalMoney;
         $tree['low_money'] = $lowMoney;
         $tree['lowTotalMoney'] = $lowTotalMoney;
         $tree['lowTotalCount'] = $lowTotalCount;
        $tree['profittimes'] = bcdiv($lowTotalMoney, $upTotalMoney, 2);
         /** 获取当前成长级 */
         $grow_up = $treeUser_model->where('tree_id', $tree['id'])->max('level');
         $tree['grow_up'] = $grow_up;
         return $tree;
    }

    /** 通过id获取财富树信息 */
    public function getTreeInfoById($tree_id)
    {
         $tree = $this->where('id', $tree_id)->field(['id', 'currency_id', 'name', 'level', 'limit', 'money', 'growth_ratio'])->find();

         /** 获取币种 */
         $currencyModel = new CurrencyModel();
         $currency_name = $currencyModel->getName($tree['currency_id']);
         $tree['currency_name'] = $currency_name;
         /** 获取当前参与人数 */
         $treeUser_model = new TreeUserModel();
         $join_count = $treeUser_model->getTreeCount($tree['id']);
         $tree['join_count'] = $join_count;
        /** 获取升级金额 */
        $upgradeMoney = [];
        $lowMoney = [];
        $lowTotalMoney = 0;
        $lowTotalCount = 0;
        $upTotalMoney = 0;
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

        }
        $tree['upgrade_money'] = $upgradeMoney;
        $tree['upTotalMoney'] = $upTotalMoney;
        $tree['low_money'] = $lowMoney;
        $tree['lowTotalMoney'] = $lowTotalMoney;
        $tree['lowTotalCount'] = $lowTotalCount;
        $tree['profittimes'] = bcdiv($lowTotalMoney, $upTotalMoney, 2);
        /** 获取当前成长级 */
        $grow_up = $treeUser_model->where('tree_id', $tree['id'])->max('level');
        $tree['grow_up'] = $grow_up;

         return $tree;
    }

    /** 获取加入支付信息 */
    public function getJoinTreeInfo($tree_id)
    {
        $tree = $this->where('id', $tree_id)->field(['id', 'currency_id', 'level', 'limit', 'money', 'growth_ratio'])->find();
        return $tree;

    }

    /** 获取房间号 */
    public function getRoomNumber($tag, $i = 0)
    {
         if ($i > 50)
         {
             return error('fail');
         }
         $num = 'T'.$tag.time().Random::number(2);
         if ($this->where('room_number', $num)->find())
         {
             $num = $this->getRoomNumber($tag, $i+1);
         }

         return $num;
    }


}
