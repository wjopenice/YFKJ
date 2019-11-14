<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

use think\Db;

class TestController extends Rest
{

    public function Get_registerAction()
    {
        /** 获取邀请码 */
        $sign = input('sign');
        if (empty($sign))
        {
           echo '请填写邀请码';
        }
        /** 定义好哪个是平台账号 */
        $user = Db::name('y_user')->where(['sign' => $sign])->field('id')->find();
        if (empty($user))
        {
           echo '未获取到用户信息';
        }


        /** 注册 */
        $add_user['username'] = '123456'.time();
        $add_user['userpass'] = '666666';
        $add_user['floor_id'] = $user['id'];
        $add_user['sign'] = time();
        $add_user['create_time'] = time();
        $add_user_id = Db::name('y_user')->insertGetId($add_user);
        if (!$add_user_id)
        {
            echo '注册失败';
        }

        /** 更新团队 */
        $this->updateGroup($user['id'], $add_user_id);
        echo '注册成功';

    }

    function updateGroup($uid, $add_user_id, $level = 1)
    {

        /** 超过两级不做处理 */
        if ($level > 2)
        {
            return true;
        }

        /** 获取上级团队 */
        $group = Db::name('y_group')->where(['guid' => $uid])->field('id,guid,users')->find();
        /** 没有则创建 */
        if (empty($group) && $level == 1)
        {
            $add_group['guid'] = $uid;
            $add_group['users'] = "{$uid},{$add_user_id}";
            $add_group['create_time'] = time();
            Db::name('y_group')->insert($add_group);
        }
        else
        {
            /** 有则更新 */
            $group_user = explode(",", $group['users']);
            if (!in_array($add_user_id, $group_user))
            {
                $group_up['users'] = "{$group['users']},{$add_user_id}";
                Db::name('y_group')->where(['id' => $group['id']])->update(['users' => $group_up['users']]);
            }
        }

        $puid =  $user = Db::name('y_user')->where(['id' => $uid])->value('floor_id');
        if (empty($puid) || $puid == 0)
        {
            return true;
        }

        $this->updateGroup($puid, $add_user_id, $level+1);

    }

    public function groupTop()
    {
        $sql = "SELECT guid, sum(money) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
        $topRes = Db::query($sql);
        return $topRes;
    }




}