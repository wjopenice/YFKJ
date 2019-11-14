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
    public function getTreeList($uid)
    {
        $treeList = $this->where('uid', $uid)->field('id,currency_id,level,limit,money,growth_ration')->order('id')->select()->toArray();
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
                $upgradeMoney[$i+1] = getTreeLevelMoeny($i+1, 20, (int)$item['growth_ratio']);
            }

            $treeList[$key]['upgrade_money'] = $upgradeMoney;
        }

        return $treeList;
    }

    /** 创建财富树 */
    public function createTree($tree_add)
    {
        /** 获取币种标识 */
        $currency_model = new CurrencyModel();
        $currency_tag = $currency_model->getTage($tree_add['currency_id']);
        if (!$currency_tag)
        {
            return error('currency not find');
        }

        /** 房间号 */
        $roomNumber = $this->getRoomNumber($currency_tag);
        if (is_error($roomNumber))
        {
            return error('room number fail');;
        }
        $tree_add['room_number'] = $roomNumber;
        $tree_add['create_time'] = time();

        /** 同时生成会员 */
        $tree_user['uid'] = $tree_add['uid'];
        $tree_user['puid'] = 0;
        $tree_user['level'] = 0;
        $tree_user['order_no'] = $tree_add['order_no'];
        $tree_user['vip_level'] = $tree_add['level'];
        $tree_user['create_time'] = time();
        $tree_user['update_time'] = time();

        $treeUserMode = new  TreeUserModel();
        Db::startTrans();
        try {

            $tree_id = $this->insertGetId($tree_add);
            /** 处理邀请码 暂时用自动补全 */
            $tree_user['tree_id'] = $tree_id;
            $treeUserMode->insert($tree_user);
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
         $tree = $this->where('room_number', $roomNumber)->field(['id', 'currency_id', 'level', 'limit', 'money', 'growth_ratio'])->find()->toArray();

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
         
         for ($i = 0; $i < $tree['level']; $i++)
         {
             $upgradeMoney[$i+1] = getTreeLevelMoeny($i+1, 20, (int)$tree['growth_ratio']);
         }
         $tree['upgrade_money'] = $upgradeMoney;

         return $tree;
    }

    /** 通过id获取财富树信息 */
    public function getTreeInfoById($tree_id)
    {
         $tree = $this->where('id', $tree_id)->field(['id', 'currency_id', 'level', 'limit', 'money', 'growth_ratio'])->find();

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

         for ($i = 0; $i < $tree['level']; $i++)
         {
             $upgradeMoney[$i+1] = getTreeLevelMoeny($i+1, 20, (int)$tree['growth_ratio']);
         }
         $tree['upgrade_money'] = $upgradeMoney;

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