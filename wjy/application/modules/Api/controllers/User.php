<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/Api/models/User.php';
include APP_MODULES.'/Api/models/Wallet.php';
include APP_MODULES.'/Api/models/Currency.php';
include APP_MODULES.'/Api/models/Cash.php';
include APP_MODULES.'/Api/models/TreeLog.php';
include APP_MODULES.'/Api/models/Profit.php';
include APP_MODULES.'/Api/models/Reward.php';

include APP_PATH.'/library/vendor/phpqrcode.php';

use think\Db;
class UserController extends Rest
{

    /**
     * 用户 注册
     */
    public function Post_registerAction()
    {

        $invite_code = input('inviteCode');
        if (empty($invite_code)) {

            /* 没有invite_code错误 */
            return $this->error('please input a inite code');
        }
        /* 获取对应用户 */
        $user_model = new UserModel();
        $user =  $user_model->getUserByInviteCode($invite_code);
        if (empty($user)){

            /** 未找到用户信息错误 */
            return $this->error('cant not find userinfo');
        }

        /* 创建新用户  */
        $res = $user_model->addUser($user);
        if (is_error($res))
        {
            return $this->error();
        }

        /** MCT邀请奖励奖励 */
        RewardModel::sendInviteReward($user['uid'], $res['uid']);
        return $this->success($res);

    }

    /**
     * 用户 登录
     */
    public function Post_loginAction()
    {

        $where['username'] = input('username');
        $where['password'] = input('password');
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('add')->check($where)) {

            $this->error($user_validate->getError());
        }

        $user_model = new UserModel();
        $res = $user_model->login($where);

        if (is_error($res))
        {
            /** 登录失败 */
            $this->toast('登录失败, 请检查账号密码是否正确');
        }

        $this->success($res);


    }

    /**
     * 用户 退出
     */
    public function Post_logoutAction()
    {

        $this->checkLogin();
        $user_model = new UserModel();
        $res = $user_model->logout($this->token);
        $this->success();
    }


    /** 用户 个人资金 */
    public function Get_walletAction()
    {

        $this->checkLogin();
        $keyword = input('keyword');
        $list = WalletModel::getUserWallet($this->uid, $keyword);
        $this->success($list);

    }


    /** 用户 某个资金金额 */
    public function Get_myCurrencyAction()
    {
        $this->checkLogin();
        $curreny_id = input('currency_id');
        if (empty($curreny_id))
        {
            $this->error('currency can not be empty');
        }
        $currency_num = WalletModel::getUserWalletByCurrency($this->uid, $curreny_id);
        $data['num'] = $currency_num;
        $this->success($data);
    }


    /** 用户 某个资金详情 */
    public function Get_currencyInfoAction()
    {
        $this->checkLogin();
        $curreny_id = input('currency_id');
        if (empty($curreny_id))
        {
           $this->error('currency can not be empty');
        }
        $currency_info = WalletModel::getCurrencyInfo($this->uid, $curreny_id);
        $this->success($currency_info);
    }


    /** 用户 提现信息 */
    public function Get_cashInfoAction()
    {
        $this->checkLogin();
        $curreny_id = input('currency_id');
        if (empty($curreny_id))
        {
            $this->error('currency can not be empty');
        }

        /** 可用余额 */
        $money = WalletModel::getUserWalletByCurrency($this->uid, $curreny_id);
        /** 获取收费信息 */
        $cashInfo = CurrencyModel::cashInfo($curreny_id);
        $cashInfo['money'] = $money;
        $this->success($cashInfo);

    }


    /** 用户 提现 */
    public function Post_cashAction()
    {
        $this->checkLogin();
        $cash_money = input('cash_money');
        if (empty($cash_money))
        {
            $this->error('cash money can not be empty');
        }
        $curreny_id = input('currency_id');
        if (empty($curreny_id))
        {
            $this->error('currency can not be empty');
        }
        $address = input('address');
        if (empty($address))
        {
            $this->error('address can not be empty');
        }

        $cash_money = abs($cash_money);
        /** 可用余额 */
        $money = WalletModel::getUserWalletByCurrency($this->uid, $curreny_id);
        /** 获取收费信息 */
        $cashInfo = CurrencyModel::cashInfo($curreny_id);
        /** MCT暂不支持 */
        if ($cashInfo['name'] == 'MCT' || $cashInfo['name'] == 'mct')
        {
            $this->warning('暂不支持');
        }
        /** 最低金额 */
        if ($cashInfo['cash_min'] > 0 && $cashInfo['cash_min'] > $cash_money)
        {
            $this->error('cash money must more than cash_min');
        }
        /** 最高金额 */
        if ($cashInfo['cash_max'] > 0 && $cashInfo['cash_max'] < $cash_money)
        {
            $this->error('cash money more than cash_max');
        }
        $cashInfo['cash_service_ratio'] = $cashInfo['cash_service_ratio'] / 100;
        $service_money = bcmul($cash_money, $cashInfo['cash_service_ratio'], 3);

        /** 最高手续费判断 */
        if ($cashInfo['cash_service_max'] > 0 && $service_money > $cashInfo['cash_service_max'])
        {
            $service_money = $cashInfo['cash_service_max'];
        }
        $realMoney = bcsub($cash_money, $service_money, 3);

        if ($cash_money > $money)
        {
            $this->error('money is not enough');
        }

        /** 提现限制 */
        if ($cashInfo['cash_review'] > 0 && $cash_money >= $cashInfo['cash_review'])
        {
            $info['state'] = 1;

        } else
        {
            $info['state'] = 2;
        }
        $info['total'] = $cash_money;
        $info['service'] = $service_money;
        $info['money'] = $realMoney;
        $info['address'] = $address;
        $wallet_model = new WalletModel();

        $res = $wallet_model->cashMoney($this->uid, $curreny_id, $info);
        if (is_error($res))
        {
            $this->error($res['message']);
        }

        /** 如果不需要审核则直接调用提现接口 */
        if ($info['state'] == 2)
        {
            $witdrawData['address'] = $address;
            $witdrawData['order_no'] = $res['order_no'];
            $witdrawData['money'] = $realMoney;
            $witdrawData['currency'] = $curreny_id == 1 ? 'usdt' : 'dyx';
            $cash_res = Tool::withdraw($witdrawData);
            if ($cash_res['msg'] == 'success' && $cash_res['code'] == 0)
            {
//                // 更新状态
//                \think\Db::name('cash')->where(['order_no' => $res['order_no']])->update(['state' => , 'update_time' => time()]);

            } else
            {
                logs('发送体现申请失败：'.$res['order_no'], '', 'casherror');
                $this->warning('提现失败, 请联系客服');
            }
        }


        $this->success($res);


    }


    /** 用户 提现记录 */
    public function Get_cashLogAction()
    {
        $this->checkLogin();
        $where['page'] = input('page');
        $where['currency_id'] = input('currency_id');
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('page')->check($where)) {

            $this->error($user_validate->getError());
        }
        $where['uid'] = $this->uid;
        $list = CashModel::cashLog($where);
        $this->success($list);
    }


    /** 用户 充值记录 */
    public function Get_rechargeLogAction()
    {
        $this->checkLogin();
        $where['page'] = input('page');
        $where['currency_id'] = input('currency_id');
        $where['uid'] = $this->uid;
        if (empty($where['currency_id']))
        {
           $this->error('currency can not be empty');
        }

        $list = WalletModel::getRechargeLog($where);
        $this->success($list);

    }


    /** 用户 获取用户信息 */
    public function Get_getUserInfoAction()
    {
        $this->checkLogin();
        $user = UserModel::getUserInfo($this->uid);
        $this->success($user);
    }


    /** 用户 获取用户信息UID */
    public function Get_userInfoAction()
    {
        $this->checkLogin();
        $uid = input('uid');
        if (empty($uid))
        {
            $this->error('user cant not find');
        }
        $user = UserModel::getUserInfo($uid);
        unset($user['wechat_code']);
        $this->success($user);
    }


    /** 用户 设置个人信息 */
    public function Post_setUserInfoAction()
    {
        $this->checkLogin();
        $where['nickname'] = input('nickname');
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('edit')->check($where)) {

            $this->error($user_validate->getError());
        }

        $user_model = new UserModel();
        $res = $user_model->setUserInfo($this->uid, $where);
        if ($res)
        {
            $this->success();
        }

        $this->error();
    }


    /** 用户 上传头像 */
    public function Post_avatarAction()
    {

        $this->checkLogin();
        if ($_FILES['file'])
        {
            $res = Upload::uploadImage($this->uid, 'avatar');
            if (is_error($res))
            {
                $this->error($res['message']);
            }

            \think\Db::name('user')->where('uid', $this->uid)->setField('avatar', $res);
            $this->success(['imgUrl' => tomedia($res).'?time='.time()]);
        }
    }


    /** 用户 上传二维码 */
    public function Post_wechatAction()
    {

        $this->checkLogin();
        if ($_FILES['file'])
        {

            $res = Upload::uploadImage($this->uid, 'wechat');
            if (is_error($res))
            {
                $this->error($res['message']);
            }

            \think\Db::name('user')->where('uid', $this->uid)->setField('wechat_code', $res);
            $this->success(['imgUrl' => tomedia($res).'?time='.time()]);
        }
    }


    /** 用户 账号密码 */
    public function Get_AccountInfoAction()
    {
        $this->checkLogin();
        $AccountInfo = UserModel::getAccountInfo($this->uid);
        $this->success($AccountInfo);
    }


    /** 用户 邀请信息 */
    public function Get_inviteInfoAction()
    {
       $this->checkLogin();
       $inviteInfo = UserModel::getInviteInfo($this->uid);
        /** 注册地址 */
        $url = createUrl('').'#/register?code='.$inviteInfo['invite_code'];
        /** 生成文件名 */
        $filename = 'qrcode/'.$inviteInfo['invite_code'].'.png';
        /** 生成二维码图片 */
        QRcode::png($url, APP_ATTACHMENT.'/'.$filename, 'L', 6, 2);
        $qrcode = tomedia($filename).'?time='.time();
        $inviteInfo['invite_qrcode'] = $qrcode;
       $this->success($inviteInfo);

    }


    /** 用户 财富树总收益 */
    public function Get_treeTotalAction()
    {
        $this->checkLogin();
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $treeLogModel = new  TreeLogModel();
        $res = $treeLogModel->incomeAndExpenseByCurrency($this->uid, $currency_id);
        $this->success($res);

    }


    /** 用户 财富树总流水 */
    public function Get_treeLogTotalAction()
    {
        $this->checkLogin();
        $currency_id = input('currency_id');
        $page = input('page') ? input('page') : 1;
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $treeLogModel = new  TreeLogModel();
        $res = $treeLogModel->treeLogListByCurrency($this->uid, $currency_id, $page);
        $this->success($res);
    }


    /** 用户 财富树分销流水 */
    public function Get_treeDisLogAction()
    {

    }


    /** 用户 红包总收益 */
    public function Get_redProfitAction()
    {
        $this->checkLogin();
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('cash money can not be empty');
        }
        /** 分销收益 */
        $dis_money = \think\Db::name('dis_log')->where(['scene' => 2, 'uid' => $this->uid, 'currency_id' => $currency_id])->sum('money');
        /** 红包收益 */
        $red_money = \think\Db::name('red_log')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->where('money', '>', 0)->sum('money');
        $redProfit = bcadd($dis_money, $red_money, 3);
        $this->success(['redProfit' => $redProfit]);
    }


    /** 用户 红包推广收益 */
    public function Get_redDisAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency money can not be empty');
        }
        $data = array();
        if ($page == 1)
        {
            /** 累计推广收益 */
            $disProfit = \think\Db::name('dis_log')->where(['uid' => $this->uid, 'scene' => 2, 'currency_id' => $currency_id])->sum('money');
            $data['disProfit'] = $disProfit;
        }
        $list = \think\Db::name('dis_log')->where(['uid' => $this->uid, 'scene' => 2, 'currency_id' => $currency_id])->order('create_time desc')->page($page, 20)->select();

        foreach ($list as $key => $item)
        {
            $userInfo = \think\Db::name('user')->where('uid', $item['from_uid'])->field('username, nickname')->find();
            if ($userInfo)
            {
                $list[$key]['username'] = $userInfo['nickname'] ? $userInfo['nickname'] : $userInfo['username'];
            }
            else
            {
                $list[$key]['username'] = '查无此人';
            }
            $list[$key]['create_time'] = date('Y.m.d', $item['create_time']);
        }
        $data['list'] = $list;
        $this->success($data);
    }


    /** 用户 红包群主收益 */
    public function Get_groupProfitAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency money can not be empty');
        }
        $data = array();
        if ($page == 1)
        {
             /** 累计群主奖励 */
             $groupProfit = \think\Db::name('red_log')->where(['uid' => $this->uid, 'scene' => 1, 'currency_id' => $currency_id])->sum('money');
            $data['groupProfit'] = $groupProfit;
        }
        $list = \think\Db::name('redgroup')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->field('id,name,create_time')->order('create_time desc')->page($page, 20)->select();
        foreach ($list as $key => $item)
        {
           $list[$key]['create_time'] = date('Y.m.d', $item['create_time']);
           /** 获取群收益 */
           $profit = \think\Db::name('red_log')->where(['uid' => $this->uid, 'scene' => 1, 'currency_id' => $currency_id, 'redgroup_id' => $item['id']])->sum('money');
            $list[$key]['profit'] = $profit;

        }

        $data['list'] = $list;

        $this->success($data);

    }


    /** 用户 红包流水 */
    public function Get_redLogAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $data = array();
        if ($page == 1)
        {
            /** 总共发出 */
            $sendTotal = \think\Db::name('redpacket')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->sum('realmoney');
            /** 获取群主的创建的金额 */
            $group_money = Db::name('redgroup')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->sum('money');
            $data['sendTotal'] = bcsub($sendTotal, $group_money);

            /** 总共抢到 */
            $getTotal =  \think\Db::name('red_log')->where(['uid' => $this->uid, 'scene' => 2, 'currency_id' => $currency_id])->sum('money');

            $data['getTotal'] = $getTotal;
        }

        $list = \think\Db::name('red_log')->where(['uid' => $this->uid, 'scene' => 2, 'currency_id' => $currency_id])->field('id,money,redgroup_id,create_time')->page($page, 20)->select();

        foreach ($list as $key => $item)
        {
            $group_name = \think\Db::name('redgroup')->where('id', $item['redgroup_id'])->value('name');
            $list[$key]['name'] = $group_name;
            $list[$key]['create_time'] = date('Y.m.d', $item['create_time']);
        }

        $data['list'] = $list;

        $this->success($data);

    }


    /** 用户 下级红包收益 */
    public function Get_redUserAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $level = input('level');
        if (empty($level))
        {
            $this->error('level can not be empty');
        }
        $list = [];
        $totalMoney = 0;
        if ($level == 1)
        {
            $list = \think\Db::name('user')->where(['puid' => $this->uid])->field('uid,nickname,avatar,wechat_code,create_time')->select();

        }
        else if ($level == 2)
        {
            $sql = "SELECT * FROM user WHERE puid IN(SELECT uid FROM user WHERE puid=".$this->uid.")";
            $list = \think\Db::query($sql);
        } else if ($level == 3)
        {
            $sql = "SELECT * FROM user WHERE puid IN(SELECT uid  FROM user WHERE  puid IN(SELECT uid FROM user WHERE puid=".$this->uid."))";
            $list = \think\Db::query($sql);
        }

        foreach ($list as $key => $item)
        {
            $list[$key]['avatar'] = tomedia($item['avatar']);
            $list[$key]['wechat_code'] = tomedia($item['wechat_code']);
            // 红包收益
            $disMoney = \think\Db::name('dis_log')->where(['from_uid' => $item['uid'], 'uid' => $this->uid, 'currency_id' => $currency_id, 'scene' => 2])->sum('money');
            $list[$key]['disMoney'] = $disMoney;
            $totalMoney = bcadd($totalMoney, $disMoney, 3);
        }

        $this->success(['list' => $list, 'totalMoney' => $totalMoney, 'totalUser' => count($list)]);
    }


    /** 用户 下级财富树收益 */
    public function Get_treeUserAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $level = input('level');
        if (empty($level))
        {
            $this->error('level can not be empty');
        }
        $list = [];
        $totalMoney = 0;

        if ($level == 1)
        {
            $list = \think\Db::name('user')->where(['puid' => $this->uid])->field('uid,nickname,avatar,wechat_code,create_time')->select();

        }
        else if ($level == 2)
        {
            $sql = "SELECT * FROM user WHERE puid IN(SELECT uid FROM user WHERE puid=".$this->uid.")";
            $list = \think\Db::query($sql);
        } else if ($level == 3)
        {
            $sql = "SELECT * FROM user WHERE puid IN(SELECT uid  FROM user WHERE  puid IN(SELECT uid FROM user WHERE puid=".$this->uid."))";
            $list = \think\Db::query($sql);
        }

        foreach ($list as $key => $item)
        {
            $list[$key]['avatar'] = tomedia($item['avatar']);
            $list[$key]['wechat_code'] = tomedia($item['wechat_code']);
            // 红包收益
            $disMoney = \think\Db::name('dis_log')->where(['from_uid' => $item['uid'], 'uid' => $this->uid, 'currency_id' => $currency_id, 'scene' => 1])->sum('money');
            $list[$key]['disMoney'] = $disMoney;
            $totalMoney = bcadd($totalMoney, $disMoney, 3);
        }

        $this->success(['list' => $list, 'totalMoney' => $totalMoney, 'totalUser' => count($list)]);
    }


    /** 用户 财富树流水 */
    public function Get_treeLogAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $data = [];
        // 获取头部信息
        if ($page == 1)
        {
            /** 支出 */
            $expense =  \think\Db::name('tree_log')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->where('money', '<', 0)->sum('money');
            /** 收入 */
            $income =  \think\Db::name('tree_log')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->where('money', '>', 0)->sum('money');
            $head['income'] = $income;
            $head['expense'] = $expense;
            $data['head'] = $head;

        }
        $list = \think\Db::name('tree_log')->where(['uid' => $this->uid, 'currency_id' => $currency_id])->where('money', '>', 0)->field('id,tree_id,uid,from_uid,money,remark,create_time')->page($page, 20)->select();
        /** 获取用户信息 */
        foreach ($list as $key => $item)
        {
            if ($item['from_uid'] == 0)
            {
                $user_info = \think\Db::name('user')->where('uid', $this->uid)->field('nickname,avatar,wechat_code')->find();

            } else
            {
                $user_info = \think\Db::name('user')->where('uid', $item['from_uid'])->field('nickname,avatar,wechat_code')->find();
            }
            $room_number = \think\Db::name('tree')->where('id', $item['tree_id'])->value('room_number');
            $list[$key]['room_number'] = $room_number;
            $user_info['avatar'] = tomedia($user_info['avatar']);
            $user_info['wechat_code'] = tomedia($user_info['wechat_code']);
            $list[$key]['userinfo'] = $user_info;
            $list[$key]['datetime'] = date('Y.m.d', $item['create_time']);

        }

        $data['list'] = $list;
        $this->success($data);

    }


    /** 意见反馈 */
    public function Post_opinionAction()
    {
        $this->checkLogin();
        $where['content'] = input('content');
        $where['email'] = input('email');
        $where['uid'] = $this->uid;
        $user_validate = new \validate\User();
        /* 采用场景验证 */
        if (!$user_validate->scene('opinion')->check($where)) {

            $this->error($user_validate->getError());
        }

        if (UserModel::setOpinion($where))
        {
            $this->success();
        }

        $this->error();
    }


    /** 红包财富树 公告/规则 */
    public function Get_getConfigAction()
    {
        $group = input('group');
        if (empty($group))
        {
            $this->error();
        }

        $data = \think\Db::name('config')->where('group', $group)->select();
        $config = [];
        foreach ($data as $key => $item)
        {
            $config[$item['name']] = $item['value'];
        }
        $this->success($config);

    }


    /** 个人中心 公告规则 */
    public function Get_ruleNoticeAction()
    {
        $type = input('type');
        $list = \think\Db::name('notice')->where('type', $type)->order('order_id asc')->select();

        $this->success($list);
    }


    /** 规则/公告详情 */
    public function Get_noticeInfoAction()
    {
       $id = input('id');

       $content = \think\Db::name('notice')->where('id', $id)->value('content');
       $this->success($content);
    }


    /** 个人规则公告查看记录 */
    public function Get_lookNoticeAction()
    {
        $this->checkLogin();

        $notice = Db::name('notice')->select();
        $unlook = 0;
        foreach ($notice as $item)
        {
            $sql = "SELECT * FROM look_notice where uid = ? AND FIND_IN_SET(".$item['id'].", logs)";
            $res = Db::query($sql, [$this->uid]);
            if (!$res)
            {
                $unlook++;
            }
        }

        $this->success(['unlook' => $unlook]);


    }


    /** 更新个人更新规则 */
    public function Get_upLookNoticeAction()
    {
        $this->checkLogin();
        $notice = Db::name('notice')->select();
        foreach ($notice as $item)
        {
            $look = Db::name('look_notice')->where('uid', $this->uid)->find();
            if (empty($look))
            {
                $look_add['uid'] = $this->uid;
                $look_add['logs'] = $item['id'];
                $look_add['create_time'] = time();
                $look_add['update_time'] = time();
                Db::name('look_notice')->insert($look_add);

            } else
            {
                $logs_arr = explode(',', $look['logs']);
                if (!in_array($item['id'], $logs_arr))
                {
                    $look_up['logs'] = $look['logs'].','.$item['id'];
                    $look_up['update_time'] = time();
                    Db::name('look_notice')->where('id', $look['id'])->update($look_up);
                }
            }

        }

        $this->success();
    }


    /** mct奖励记录 */
    public function Get_rewardInfoAction()
    {
        $this->checkLogin();

        $mct_dis = Db::name('reward')->where(['uid' => $this->uid, 'type' => 1])->sum('count');
        $mct_register = Db::name('reward')->where(['uid' => $this->uid, 'type' => 2])->sum('count');
        $mct_red = Db::name('reward')->where(['uid' => $this->uid, 'type' => 3])->sum('count');
        $mct_tree = Db::name('reward')->where(['uid' => $this->uid, 'type' => 4])->sum('count');


        $data['mct_dis'] = $mct_dis;
        $data['mct_register'] = $mct_register;
        $data['mct_red'] = $mct_red;
        $data['mct_tree'] = $mct_tree;

        $this->success($data);

    }

}
