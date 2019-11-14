<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

use think\Db;

include APP_MODULES.'/API/models/TreeUser.php';
include APP_MODULES.'/API/models/Redgroup.php';

class IndexController extends Rest
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

    public function GET_indexAction()
    {

        echo base_convert(time() - 1420070400, 10, 36);die();
        $a = ['88', '77', '55'];
        print_r(array_reverse($a));die();
        $redpicket_logs = Db::name('redpacket_log')->where(['redpacket_id' => 37])->where('uid', '<>', 0)->select();
        print_r($redpicket_logs);die();
        $mode = new RedgroupModel();
        /** 生成每条红包金额 */
        list($money_arr, $min_money) = $mode->getRedMoneyData(100, 10);
        echo '1123';
        print_r($money_arr);
        die($min_money);
        /** 红包计算 */
        echo bcsub(10, -1, 2);die();
        $tree_data['tree_id'] = 5;
        $tree_data['level'] = 3;
        $tree_data['vip_level'] = 1;
        $tree_data['uid'] = 111;
        $tree_data['puid'] = 100;
        $tree_data['order_no'] = time();
        $array1[1] = array('red', 'back');
        $array2[2] = array('white', 'mm');
        $array3[2] = array('666', '777');

//                $tree_data['uid'] = $level * 10 + $i;
//                Db::name('tree_user')->insert($tree_data);
        //print_r($array3 + $array2);die();
       // print_r(array_merge_recursive($array3, $array2));die();
        $treeUser_model = new TreeUserModel();
        $res = $treeUser_model->getTreeUserOrderLevel(1, 5);
        print_r($res);die();
//        $users = Db::name('tree_user')->where(['tree_id' => 5, 'puid' => 14])->select();
//
//        $i = 1;
//        $tree_level = null;
//        $tree_data['tree_id'] = 5;
//        $level = 3;
//        $tree_data['level'] = $level;
//        $tree_data['vip_level'] = 1;
//        $tree_data['create_time'] = time();
//        $tree_data['update_time'] = time();
//        $tree_data['order_no'] = time();
//
//        foreach ($users as $item)
//        {
//            $j = 1;
//            $tree_data['puid'] = $item['uid'];
//            while($j < 3){
//
//                $tree_data['uid'] = $level * 10 + $i;
//                Db::name('tree_user')->insert($tree_data);
//                /** 插入数据 */
//                $i++;
//                $j++;
//
//            }
//        }
//
//        echo 'over';die();
        echo getTreeLevelMoeny(8, 20, 100);
        echo "<br>";
        echo getTreeUpgradeMoney(2, 4, 20, 100);die();
        //echo base_convert(time() - 1420070400, 10, 36);die();
        echo time();die();
        echo secret('123456', 'ceshi');die();
        $key ='ceshi';
//        echo '<br>';
//        $str = '123465';
//        $str2 = authcode($str, 'ENCODE', $key);
//        echo $str2;
//        echo '<br>';
        echo  authcode('0c08ym0A9eizi5iyc3lfxyvNdQbY4+AmSn9Wwnw24SNM1NY', 'DECODE', $key);die();
        echo RSA::authcode('123', 'E');die();
        $payload_test=array('iss'=>'admin','iat'=>time(),'exp'=>time()+7200,'nbf'=>time(),'sub'=>'www.admin.com','jti'=>md5(uniqid('JWT').time()));;
        $token_test=Jwt::getToken($payload_test);
        echo "<pre>";
        echo $token_test;
die();

        $getPayload_test=Jwt::verifyToken($token_test);
        echo "<br><br>";
        var_dump($getPayload_test);
        echo "<br><br>";die();
        echo time();die();
        //echo Random::word($n=8);die();

    }

    /**
     * 首页 获取所有币种
     */
    public function GET_currencyListAction()
    {
        $name = input('keyword');
        $currency_model = new CurrencyModel();
        $list = $currency_model->getList($name);
        return $this->success('', $list);

    }


}