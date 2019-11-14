<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

use think\Db;

class TreeUserModel extends Model
{

    /** 增加财富树成员 */
    public function addTreeUser($uid, $tree, $pay_res, $tree_location)
    {

        /** 新增财富树用户 */
        $add_user['order_no'] = $pay_res['order_no'];
        $add_user['puid'] = $tree_location['puid'];
        $add_user['level'] = $tree_location['level'];
        $add_user['vip_level'] = 1;
        $add_user['tree_id'] = $tree['id'];
        $add_user['uid'] = $uid;
        $add_user['create_time'] = time();
        $add_user['update_time'] = time();
        
        /** 新增财富树流水记录 */
        $tree_log['order_no'] = $pay_res['order_no'];
        $tree_log['uid'] = $uid;
        $tree_log['from_uid'] = 0;
        $tree_log['loss_uid'] = 0;
        $tree_log['tree_id'] = $tree['id'];
        $tree_log['currency_id'] = $tree['currency_id'];
        $tree_log['money'] = -$tree['money'];
        $tree_log['scene'] = 1;
        $tree_log['remark'] = 'join tree';
        $tree_log['create_time'] = time();

        $treeLogMode = new TreeLogModel();
        Db::startTrans();
        try {

            $this->insert($add_user);
            $treeLogMode->insert($tree_log);
            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            return error('create tree fail');
        }


    }

    /** 获取财富树价格 */
    public function getTreeCount($tree_id)
    {
        return $this->where('tree_id', $tree_id)->count();
    }

    /** 获取财富树位置 */
    public function getTreeLocation($tree, $uid, $puid)
    {
        /** 两种类型 一种一人推有限制的 还有一种没有限制的 */
        /** 1. 获取推荐人层级 */
        $p_level = $this->where(['tree_id' => $tree['id'], 'uid' => $puid])->value('level');

         if ($tree['limit'] == 0)
         {
             /** 无限制 */
             /** 2. 获取当前层级 */
             $level = $p_level + 1;
             return ['level' => $level, 'puid' => $puid];

         }
         else
         {

             /** 有限制 */
             /** 2.获取当前下级是否满员 因为count比较快所以先查count */
             $user_count = $this->where(['tree_id' => $tree['id'], 'puid' => $puid])->count();
             if ($user_count < $tree['limit'])
             {

                 $level = $p_level + 1;
                 return ['level' => $level, 'puid' => $puid];
             }
             /** 3. 当前父级已满 从上到下从左到右原则获取位置 */
             /** 3.1 获取当前用户所有子用户 */
             $tree_user = $this->getTreeUserOrderLevel($puid, $tree['id']);
             $key_level = null;
             foreach ($tree_user as $key => $item)
             {
                 /** 先获取该层级应该有多少人 */
                 $level_count = pow((int)$tree['limit'], $key-1);
                 $level_count_now = count($item);
                 /** 当前人数已满 */
                 if ($level_count_now >= $level_count)
                 {
                     continue;
                 }
                 /** 当前人数未满 */
                 $key_level = $key - 1;
                 break;
             }

             /** 获取到当前层数 通过对比数据找到位置 */
             $result = [];
             foreach ($tree_user[$key_level] as $key => $item)
             {
                 $count = $this->where(['tree_id' => $tree['id'], 'puid' => $item['uid']])->count();
                 if ($count < $tree['limit'])
                 {
                     $result['puid'] = $item['uid'];
                     $result['level'] = $key_level + 1;
                 }
             }

             if (empty($result))
             {
                 return error('not find tree location');
             }

             return $result;
         }
    }
    
    /** 计算升级价格 */
    public function upgradeMoney($uid, $tolevel, $tree_id)
    {

        $treeModel = new TreeModel();
        $tree = $treeModel->where('id', $tree_id)->field('id,currency_id,money,growth_ratio,level')->find();
        if (empty($tree))
        {
            return error('can not find tree');
        }
        $level = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->value('vip_level');
        if (empty($level))
        {
            return error('user is not in this tree');
        }

        if ($level >= $tolevel)
        {
            return error('must be above the current level');
        }

        if ($tolevel > $tree['level'])
        {
            return error('level beyond limit');
        }

        $money = getTreeUpgradeMoney($level, $tolevel, $tree['money'], $tree['growth_ratio']);
        if ($money)
        {
            return ['money' => $money, 'currency_id' => $tree['currency_id'], 'level' => $level];
        }

        return error('money is error');



    }

    /** 升级用户等级 */
    public function upgradeTreeUser($upgrade)
    {

        /** 更新用户等级 保存更新记录 */
        $user_up['vip_level'] = $upgrade['tolevel'];
        $user_up['update_time'] = time();

        $upgrade['create_time'] = time();

        Db::startTrans();
        try {

            $this->where(['uid' => $upgrade['uid'], 'tree_id' => $upgrade['tree_id']])->update($user_up);
            Db::name('tree_upgrade')->insert($upgrade);
            Db::commit();
            return true;

        } catch (\Exception $e) {
            Db::rollback();
            return error('upgrade tree fail');
        }
    }

    /**
     * 发现实现不了树枝所在 (已淘汰)
     * 从上到下从左到右 依次获取位置
     * 1.先获取每层的应该有的人数 推广人 limit 层数 i 公式 limit的i-1次方
     * 2.获取这颗树下的该等级的人数 等级从0开始 所以level = i - 1;
     * 3.获取到level层有空位 代表 level-1层有人下级没有满 获取level-1层所有用户id排序
     * 4.遍历这个用户数组 获取每个用户的下级个数做比较 返回该用户的id与level 结束循环
     */
    public function successively1($uid, $tree)
    {
        /** 获取那个层级未满*/
        $location = [];
        $i = 1;
        $tree_level = null;
        while($i){

            $i++;
           /** 先获取该层级应该有多少人 */
           $level_count = pow((int)$tree['limit'], $i-1);
           /** 搜索该层级人数 */
           $level = $i - 1;
           $tree_level_count = $this->where(['tree_id' > $tree['id'], 'level' => $level])->count();
           if ($tree_level_count < $level_count)
           {

               $tree_level = $i;
               break;
           }
        }

        /** 获取该层级所有人员 */
        $tree_level_users = $this->where(['tree_id' => $tree['id'], 'level' => $tree_level])->field('uid', 'puid', 'level')->order('id')->select()->toArray();

        foreach ($tree_level_users as $item)
        {
            /** 获取当前用户推荐数量 */
            $count = $this->where(['tree_id' => $tree['id'], 'puid' => $item['uid']])->count();
            if ($count < $tree['limit'])
            {
                /** 符合条件 结束循环 */
                $location['level'] = $item['level'] + 1;
                $location['puid'] = $item['uid'];
                break;
            }
        }

        if (empty($location))
        {
            return $this->error('No suitable location');
        }

        return $location;

    }

    /** 从上到下从左到右获取位置 */
    public function successively($uid, $tree, $userArray = [], $order = 0)
    {
        $location = [];
        /** 第一次进入时候没有用户数组 */
        if (empty($userArray))
        {
            $tree_users = $this->where(['tree_id' => $tree['id'], 'puid' => $uid])->field('uid', 'puid', 'level')->order('id')->select()->toArray();
            foreach ($tree_users as $item)
            {
                /** 获取当前用户推荐数量 */
                $count = $this->where(['tree_id' => $tree['id'], 'puid' => $item['uid']])->count();
                if ($count < $tree['limit'])
                {
                    /** 符合条件 结束循环 */
                    $location['level'] = $item['level'] + 1;
                    $location['puid'] = $item['uid'];
                    break;
                }
            }

            $this->successively($uid, $tree, $userArray);
        }
        else
        {
            /** 很明显第一层级没有符合条件的 还需要继续 */
            /** 判断是否验证完 */
            if (count($userArray) == $order)
            {

            } else
            {

            }

        }

        /**  */
        if (empty($location))
        {

        }

        return $location;
    }

    /** 获取某个uid下边的所有成员 */
    public function getTreeUserByUid($uid, $tree_id)
    {
         $arr = [];
         $users = $this->where(['tree_id' => $tree_id, 'puid' => $uid])->field('uid,level,vip_level')->select()->toArray();
         
         if (empty($users))
         {
             return [];
         }

         $arr = $users;
        
         foreach ($users as $item)
         {
             $res = $this->getTreeUserByUid($item['uid'], $tree_id);
             if (!empty($res))
             {
                 $arr = array_merge_recursive($arr, $res);
             }

         }

         return $arr;
    }

    /** 按等级获取uid下边所有成员 */
    public function getTreeUserOrderLevel($uid, $tree_id)
    {
        $res = $this->getTreeUserByUid($uid, $tree_id);
        $tree_users = [];
        foreach ($res as $key => $item)
        {
            $tree_users[$item['level']][] = $item;
        }

        return $tree_users;
    }

    /** 按等级获取uid下边所有成员 */
    public function getTreeUserOrderVip($uid, $tree_id)
    {
        $res = $this->getTreeUserByUid($uid, $tree_id);
        $tree_users = [];
        foreach ($res as $key => $item)
        {
            $tree_users[$item['vip_level']][] = $item;
        }

        return $tree_users;
    }

    /** 根据会员等级获取上级的uid */
    public function getRewardUid($uid, $tree_id, $level)
    {

        // id为1的为总部
        $p_uid = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->value('puid');

        if ($p_uid == 0)
        {
            $p_uid = 1;
            $p_vip_level = 0;
        }
        else
        {
            $p_vip_level = $this->where(['uid' => $p_uid, 'tree_id' => $tree_id])->value('vip_level');
        }

        if ($level == 1 || $p_uid == 1)
        {
            return ['uid' => $p_uid, 'vip_level' => $p_vip_level];
        }

        $result = $this->getRewardUid($p_uid, $tree_id, $level - 1);

        return $result;
    }

    /** 获取会员等级 */
    public function getTreeLevel($uid, $tree_id)
    {
        $level = $this->where(['uid' => $uid, 'tree_id' => $tree_id])->value('vip_level');
        return $level;
    }
}