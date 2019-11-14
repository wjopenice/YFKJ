<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/10/29
 * Time: 14:08
 */

class TreeUserModel extends Model
{
    // 获取财富树参与人数
    public function getTreeCount($tree_id)
    {
        return $this->where('tree_id', $tree_id)->count();
    }

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
        $add_user['tuid'] = $tree_location['tuid'];
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
        $tree_log['remark'] = '加入财富树';
        $tree_log['create_time'] = time();

        $treeLogMode = new TreeLogModel();
        \think\Db::startTrans();
        try {

            $this->insert($add_user);
            $treeLogMode->insert($tree_log);
            \think\Db::commit();
            return true;

        } catch (\Exception $e) {
            \think\Db::rollback();
            logs('增加用户失败 订单号:'.$add_user['order_no'].'用户id:'.$uid.'财富树id:'.$tree['id'], '', 'treeuser');
            return error('create tree fail');
        }
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
                $level_count = pow((int)$tree['limit'], ($key - $p_level));
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
            if (empty($key_level))
            {
                end($tree_user);
                $key_level = key($tree_user);
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
                    break;
                }
            }

            if (empty($result))
            {
                return error('未找到财富树位置');
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

        /** 保存升级流水 */
        $tree_log['order_no'] = $upgrade['order_no'];
        $tree_log['uid'] = $upgrade['uid'];
        $tree_log['from_uid'] = 0;
        $tree_log['loss_uid'] = 0;
        $tree_log['tree_id'] = $upgrade['tree_id'];
        $tree_log['currency_id'] = $upgrade['currency_id'];
        $tree_log['money'] = -$upgrade['money'];
        $tree_log['scene'] = 2;
        $tree_log['remark'] = "从{$upgrade['level']}级到{$upgrade['tolevel']}级";
        $tree_log['create_time'] = time();

        unset($upgrade['currency_id']);
        \think\Db::startTrans();
        try {

            $this->where(['uid' => $upgrade['uid'], 'tree_id' => $upgrade['tree_id']])->update($user_up);
            \think\Db::name('tree_upgrade')->insert($upgrade);
            \think\Db::name('tree_log')->insert($tree_log);
            \think\Db::commit();
            return true;

        } catch (\Exception $e) {
            \think\Db::rollback();
            return error('升级失败');
        }
    }

    /** 获取某个uid下边的所有成员 */
    public function getTreeUserByUid($uid, $tree_id)
    {
        $arr = [];
        $users = $this->where(['tree_id' => $tree_id, 'puid' => $uid])->field('uid,puid,level,vip_level')->select()->toArray();

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

    /** 获取某个uid下边的所有成员 */
    public function getTreeUserByUidEchat($uid, $tree_id)
    {
        $arr = $this->getTreeUserByUid($uid, $tree_id);
        foreach ($arr as $key => $item)
        {
            $name = \think\Db::name('user')->where(['uid' => $item['uid']])->value('username');
            $arr[$key]['name'] = $name.'('.$item['vip_level'].')';

        }

        $data = $this->list_to_tree($arr);

        return $data;
    }

    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     */
    function list_to_tree($list, $pk='uid', $pid = 'puid', $child = 'children', $root = 1) {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }

            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
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
        /** 获取财富树层级 */
        $tree_level = \think\Db::name('tree')->where('id', $tree_id)->value('level');
        $tree_users = [];
        for($i = 1; $i <= $tree_level; $i++)
        {
            $tree_users[$i] = [];
        }
        $res = $this->getTreeUserByUid($uid, $tree_id);
        foreach ($res as $key => $item)
        {
            $tree_users[$item['vip_level']][] = $item;
        }

        $user_count = count($res);
        return ['users' => $tree_users, 'user_count' => $user_count];
    }

    /** 按等级获取uid下边所有成员 */
    public function getTreeUserOrderLevelUp($uid, $tree_id)
    {
        /** 获取财富树层级 */
        $tree_level = \think\Db::name('tree')->where('id', $tree_id)->value('level');
        $tree_users = [];
        /** 获取用户当前层级 */
        $u_level = \think\Db::name('tree_user')->where('uid', $uid)->value('level');
        for($i = 1; $i <= $tree_level; $i++)
        {
            $tree_users[$i] = [];
        }
        $res = $this->getTreeUserByUid($uid, $tree_id);
        foreach ($res as $key => $item)
        {
            $tree_users[$item['level'] - $u_level][] = $item;
        }

        $user_count = count($res);
        return ['users' => $tree_users, 'user_count' => $user_count];
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
