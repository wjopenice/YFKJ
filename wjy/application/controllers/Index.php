<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

use think\Db;

include APP_MODULES.'/Api/models/Currency.php';
include APP_MODULES.'/Api/models/Tree.php';
include APP_MODULES.'/Api/models/TreeUser.php';
include APP_MODULES.'/Api/models/User.php';
include APP_MODULES.'/Api/models/Wallet.php';
include APP_MODULES.'/Api/models/TreeLog.php';
include APP_MODULES.'/Api/models/DisLog.php';
include APP_MODULES.'/Api/models/PayOrder.php';
include APP_MODULES.'/Api/models/Reward.php';

class IndexController extends Controller
{

    public function indexAction()
    {
        /** 获取群组信息 */
        $this->getView();
    }

    public function testAction()
    {

        $active = Db::name('active')->whereTime('create_time', 'today')->select();
        print_r($active);die();
        $res = RewardModel::sendInviteReward(8, 81);
        print_r($res);die();
        print_r(getConfig('mct'));die();
        for ($i = 0; $i < 100; $i++)
        {
            $res = \vendor\RedCalculation::makeRandom(100, 10);
            $total = 0;
            foreach ($res as $item)
            {
                $total = $total + $item;
                if ($item < 0)
                {
                    echo "bug";die();
                }
                echo $item .'****';
            }
            echo $total."<br>";
        }
        die();
        $notice = Db::name('notice')->select();
        $unlook = 0;
        foreach ($notice as $item)
        {
            $sql = "SELECT * FROM look_notice where uid = ? AND FIND_IN_SET(".$item['id'].", logs)";
            $res = Db::query($sql, [1]);
            print_r($res);die();
            if ($res)
            {
                $unlook++;
            }
        }

        echo $unlook;die();
        /** 获取群组信息 */
        $this->getView();
//die();
//        $tree = Db::name('tree')->where('id', 11)->find();
//        $treeUser_mode = new TreeUserModel();
//        $tree_location = $treeUser_mode->getTreeLocation($tree, 42, 36);
//        print_r($tree_location);
//        die();
////        $datepath = date("Y/m/d");
////        $filename = time().'.png';
////        $filepath =  "/images/" .$datepath.'/'. $filename;
////        mkdirs(APP_ATTACHMENT.$filepath);
//        echo md5('wjyq1w2e3r4');die();
//        $data = ['token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOjgsImlhdCI6MTU2OTc1MzE1MSwiZXhwIjoxNTY5NzYwMzUxLCJuYmYiOjE1Njk3NTMxNTEsInN1YiI6IiIsImp0aSI6Ijc1MjhmZTMyNTA5NTcwZGE5ZWI3M2U2M2QzNTRkYjk5In0.9qk_tEjc8XXcee614ohgDXsVy5-tUaNd2ZGPaAcx-Bg'];
//        echo Tool::bulidApiSign($data);die();
    }

}
