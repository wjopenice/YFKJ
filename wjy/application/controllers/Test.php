<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

use think\Db;

class TestController extends Rest
{

    public function Get_indexAction()
    {
        $data = ['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOjgsImlhdCI6MTU2OTc1MzE1MSwiZXhwIjoxNTY5NzYwMzUxLCJuYmYiOjE1Njk3NTMxNTEsInN1YiI6IiIsImp0aSI6Ijc1MjhmZTMyNTA5NTcwZGE5ZWI3M2U2M2QzNTRkYjk5In0.9qk_tEjc8XXcee614ohgDXsVy5-tUaNd2ZGPaAcx-Bg'];
        echo Tool::bulidApiSign($data);die();
        $string = hash_hmac('sha256', 'wjyappkey', 'wjyappkey');
        echo $string;
    }
    public function Get_ratioAction()
    {
        $total_count = 1157;
        $top_number = (1+$total_count)*$total_count/2;

        $ratio_total = 0;
        for ($i = 1; $i <= $total_count; $i++)
        {
            $ratio_total += sprintf("%.3f",$top_number / $i);
        }

        $top_arr = [];
        for ($j = 1; $j <= $total_count; $j++)
        {
            $top_arr[] = sprintf("%.3f",($top_number / $j / $ratio_total));
        }

        echo "<pre>";
        print_r($top_arr);
        echo "<pre>";die();
    }

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
