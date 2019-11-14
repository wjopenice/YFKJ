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

class UpgradeController extends Controller
{

    public function indexAction()
    {
        /** 获取所有用户 */
        $users =  Db::name('user')->select();

        $total = count($users);
        $success = 0;
        $fail = 0;
        foreach ($users as $item)
        {

            $address = Db::name('wallet')->where(['uid' => $item['uid'], 'currency_id' => 1])->find()['address'];
            $wallet_add['uid'] = $item['uid'];
            $wallet_add['total'] = 0;
            $wallet_add['free'] = 0;
            $wallet_add['lock'] = 0;
            $wallet_add['consume'] = 0;
            $wallet_add['address'] = '';
            $wallet_add['create_time'] = time();
            $wallet_add['update_time'] = time();
            $wallet_add['currency_id'] = 3;
            $res = Db::name('wallet')->insert($wallet_add);
            if ($res)
            {
                $success++;
            } else
            {
                $fail++;
            }
        }

        echo "更新记录共{$total} 成功:{$success} 失败:{$fail}";die();
    }


}
