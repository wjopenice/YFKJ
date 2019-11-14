<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


class UserController extends ApiBaseController
{

    /**
     * 登录
     */
    public function Post_loginAction()
    {

        $where['username'] = input('username');
        $where['password'] = input('password');

        if (empty($where['username']) || empty($where['password'])) {
            $this->error('参数不合法');
        }


        $user_model = new UserModel();
        $res = $user_model->login($where);

        if (is_error($res))
        {
            /** 登录失败 */
            $this->success(['error' => '1', 'msg' =>'登录失败, 请检查账号密码是否正确']);
        }

        $this->success($res);

    }

    /**
     * 退出登录
     */
    public function Post_logOutAction()
    {
        $this->checkLogin();
        $user_model = new UserModel();
        $res = $user_model->logout($this->token);
        $this->success();

    }

    /**
     * 注册
     */
    public function Post_registerAction()
    {
        $where['username'] = input('username');
        $where['password'] = input('password');
        $invite_code = input('invite_code');
        if (empty($invite_code)) {

            /* 没有invite_code错误 */
            return $this->error('请输入邀请码');
        }
        $user_validate = new \validate\User();
        /* 验证 */
        if (!$user_validate->scene('add')->check($where)) {

            return $this->error($user_validate->getError());
        }
        /* 获取对应用户 */
        $user_model = new UserModel();
        $user = $user_model->getUserByInviteCode($invite_code);
        if (empty($user)){

            /** 未找到用户信息错误 */
            return $this->success(['error' => 1, 'msg' => '无效验证码']);
        }
        /** 验证用户是否在官网财富树下 */
//        $tree_user = \think\Db::name('tree_user')->where(['tree_id' => 1, 'uid' => $user['uid']])->field('uid')->find();
//        if (empty($tree_user)) {
//
//            return $this->warning('无效推荐码');
//        }
        /** 验证用户是否被使用 */
        $user_log =\think\Db::name('user')->where('username', $where['username'])->value('uid');
        if (!empty($user_log))
        {
            return $this->success(['error' => 1, 'msg' => '当前用户已被使用']);
        }
        /* 创建新用户 */
        $res = $user_model->addUser($user, $where);
        if (is_error($res))
        {
            return $this->error($res['message']);
        }

        return $this->success(['error' => 0, 'msg' => '无效验证码']);

    }

    /**
     * 获取授权信息
     */
    public function Get_authInfoAction()
    {
        $token = input('token');

        if (empty($token))
        {
            $this->response(10003, '非法令牌');
        }
        $cash = cache('Auth_wtree'.$token);
//        if (empty($cash))
//        {
//            $this->response(10004, '令牌已过期');
//        }
        $user = \think\Db::name('user')->where(['token' => $token])->field('uid,nickname,avatar,wechat_code')->find();
        if (empty($user))
        {
            $this->response(10005, '其他客户端已登录');
        }
        $user['wechat_code'] = tomedia( $user['wechat_code']);
        $this->success($user);
    }


    /** 获取钱包信息 */
    public function Get_walletAction()
    {
        $this->checkLogin();
        $walletModel = new WalletModel();
        $walletData = $walletModel->walletInfo($this->uid);
        vendor('phpqrcode');
        /** 生成文件名 */
        $filename = 'address/'.$walletData['address'].'.png';
        /** 生成二维码图片 */
        QRcode::png($walletData['address'], APP_ATTACHMENT.'/'.$filename, 'L', 6, 2);
        $walletData['address_qrcode'] = tomedia($filename);
        $this->success($walletData);

    }


    /** 上传二维码 */
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

            \think\Db::name('user')->where('uid', $this->uid)->setField('wechat_code', $res.'?time='.time());
            $this->success(['imgUrl' => tomedia($res).'?time='.time()]);
        }
    }

    /** 用户 提现信息 */
    public function Get_withdrawInfoAction()
    {

        $curreny_id = 1;
        if (empty($curreny_id))
        {
            $this->error('币种不能为空');
        }

        $cash_info =\think\Db::name('currency')->where('id', $curreny_id)->field('cash_service_ratio,cash_service_max,cash_min,cash_max,cash_service_max,cash_review')->find();
        $this->success($cash_info);

    }


    /** 用户 提现 */
    public function Post_withdrawAction()
    {
        $this->checkLogin();
        //$this->warning('提现系统升级中...');
        $cash_money = input('money');
        if (empty($cash_money))
        {
            $this->warning('提币金额不能为空');
        }
        $curreny_id = 1;
        if (empty($curreny_id))
        {
            $this->warning('currency can not be empty');
        }
        $address = input('address');
        if (empty($address))
        {
            $this->warning('地址不能为空');
        }

        $rule = [
            'money'  => 'require|float',
        ];
        $msg = [
            'money.require' => '请输入提现金额',
            'money.float'  => '输入金额不合法',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check(['money' => $cash_money]);
        if (!$result)
        {
            $this->warning($validate->getError());
        }

        $cash_money = abs($cash_money);
        $walletModel = new WalletModel();
        /** 可用余额 */
        $money = $walletModel->getUserWalletByCurrency($this->uid, $curreny_id);
        /** 获取收费信息 */
        $cashInfo = \think\Db::name('currency')->where('id', $curreny_id)->field('cash_service_ratio,cash_service_max,cash_min,cash_max,cash_service_max,cash_review')->find();

        /** 最低金额 */
        if ($cashInfo['cash_min'] > 0 && $cashInfo['cash_min'] > $cash_money)
        {
            $this->warning('提币最小金额为'.$cashInfo['cash_min']);
        }
        /** 最高金额 */
        if ($cashInfo['cash_max'] > 0 && $cashInfo['cash_max'] < $cash_money)
        {
            $this->warning('提币最高金额为'.$cashInfo['cash_max']);
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
            $this->warning('余额不足');
        }


        /** 提现限制 */
        if ($cashInfo['cash_review'] > 0 && $cash_money >= $cashInfo['cash_review'])
        {
            $info['state'] = 1;

        } else
        {
            $info['state'] = 2;
        }

        // 测试状态带审核
        //$info['state'] = 1;

        $info['total'] = $cash_money;
        $info['service'] = $service_money;
        $info['money'] = $realMoney;
        $info['address'] = $address;
        $wallet_model = new WalletModel();

        $res = $wallet_model->cashMoney($this->uid, $curreny_id, $info);
        if (is_error($res))
        {
            $this->warning($res['message']);
        }

        /** 如果不需要审核则直接调用提现接口 */
        if ($info['state'] == 2)
        {
            // 转出地址
            $from_address = \think\Db::name('wallet')->where(['uid' => $this->uid])->value('address');
            $freePay = new Freepay();
            $witdrawData['to'] = $address;
            $witdrawData['from'] = $from_address;
            $witdrawData['order_on'] = $res['order_no'];
            $witdrawData['price'] = $realMoney;
            $witdrawData['currency'] = 'usdt';
            $cash_res = $freePay->withdraw($witdrawData);
            if ($cash_res['code'] == 0)
            {
                // 更新状态


            } else
            {
                logs('发送体现申请失败：'.$res['order_no'], '', 'casherror');
                $this->warning('提现失败, 请联系客服');
            }
        }


        $this->success($res);


    }

    /** 提现记录 */
    public function Get_withdrawListAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $list = \think\Db::name('withdraw')->where(['uid' => $this->uid])->field('id,address,uid,total,state,create_time')->order('id desc')->page($page, 10)->select();
        foreach ($list as $key => $item)
        {
            $list[$key]['create_time'] = date('Y-m-d H:i:s', time());
        }
        $this->success($list);
    }

    /** 转账 */
    public function Post_transferAction()
    {
        $this->checkLogin();
        $username = input('username');
        $money = input('money');

        if (empty($username) || empty($money))
        {
            $this->warning('信息错误, 请仔细核对用户信息');
        }
        $user_id = \think\Db::name('user')->where('username', $username)->value('uid');

        if (empty($user_id))
        {
            $this->warning('未找到用户信息, 请仔细核对用户信息');
        }

        $rule = [
            'money'  => 'require|min:1|float',
        ];
        $msg = [
            'money.require' => '请输入转账金额',
            'money.min'     => '转账最小金额为1',
            'money.float'  => '输入金额不合法',
        ];
        $validate = new Validate($rule, $msg);
        $result = $validate->check(['money' => $money]);
        if (!$result)
        {
            $this->warning($validate->getError());
        }

        if ($this->uid == $user_id)
        {
            $this->warning('非法操作');
        }
        $walletModel = new WalletModel();

        $res = $walletModel->transfer($this->uid, $user_id, $money);
        if (is_error($res))
        {
            $this->warning($res['message']);
        }

        $this->success($res);
    }

    /** 转账记录 */
    public function Get_transferListAction()
    {
        $this->checkLogin();
        $page = input('page') ? input('page') : 1;
        $list = \think\Db::name('transfer')->where(['fuid' => $this->uid])->order('id desc')->page($page, 10)->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['tuid'])->value('username');
            $list[$key]['tousername'] = $username;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $this->success($list);
    }

    /** 获取邀请信息 */
    public function Get_inviteInfoAction()
    {
        $this->checkLogin();
        $invite_code  = \think\Db::name('user')->where('uid', $this->uid)->value('invite_code');
        $data['invite_code'] = $invite_code;
        //图片地址
        vendor('phpqrcode');
        /** 注册地址 */
        $url = createUrl('').'#/register?code='.$invite_code;
        /** 生成文件名 */
        $filename = 'address/'.$invite_code.'.png';
        /** 生成二维码图片 */
        QRcode::png($url, APP_ATTACHMENT.'/'.$filename, 'L', 6, 2);
        $data['invite_qrcode'] = tomedia($filename);
        $this->success($data);
    }

    /** 备份账号信息 */
    public function Get_BackupInfoAction()
    {
        $this->checkLogin();
        $userModel = new UserModel();
        $AccountInfo = $userModel->getAccountInfo($this->uid);
        $this->success($AccountInfo);
    }

    /** 获取公告列表 */
    public function Get_noticeAction()
    {
        $list = \think\Db::name('notice')->where([])->order('order_id asc')->select();
        $look = \think\Db::name('look_notice')->where('uid', $this->uid)->find();
        /** 判断查看规则 */
        foreach ($list as $key => $item)
        {
            $list[$key]['create_time'] = date('Y-m-d H:i:s');
            if (empty($look))
            {
                $list[$key]['new'] = true;

            } else
            {
                $logs_arr = explode(',', $look['logs']);
                if (in_array($item['id'], $logs_arr))
                {
                    $list[$key]['new'] = false;

                } else
                {
                    $list[$key]['new'] = true;
                }
            }
        }

        $this->success($list);
    }


    /** 更新公告状态 */
    public function Post_lookNoticeAction()
    {
        $notice_id = input('notice_id');

        $notice = \think\Db::name('notice')->where('id', $notice_id)->find();

        if (empty($notice))
        {
            $this->error();
        }
        if (empty($this->uid))
        {
            $this->success($notice);
        }

        $look = \think\Db::name('look_notice')->where('uid', $this->uid)->find();
        if (empty($look))
        {
            $look_add['uid'] = $this->uid;
            $look_add['logs'] = $notice_id;
            $look_add['create_time'] = time();
            $look_add['update_time'] = time();
            \think\Db::name('look_notice')->insert($look_add);
            $this->success($notice);

        } else
        {
            $logs_arr = explode(',', $look['logs']);
            if (!in_array($notice_id, $logs_arr))
            {
                $look_up['logs'] = $look['logs'].','.$notice_id;
                $look_up['update_time'] = time();
                \think\Db::name('look_notice')->where('id', $look['id'])->update($look_up);
                $this->success($notice);
            }

            $this->success($notice);
        }
    }


    /** 查看个人等级 */
    public function Get_userLevelAction()
    {
        if (empty($this->uid))
        {
            $this->success();
        }

        $tree_level_one = \think\Db::name('tree_user')->where(['uid' => $this->uid, 'tree_id' => 1])->value('vip_level');
        $tree_level_two = \think\Db::name('tree_user')->where(['uid' => $this->uid, 'tree_id' => 2])->value('vip_level');

        $data['level_one'] = $tree_level_one ? $tree_level_one : 0;
        $data['level_two'] = $tree_level_two ? $tree_level_two : 0;
        $this->success($data);

    }

    /** 版本号 */
    public function Get_checkVersionAction()
    {
        $this->success(['version' => '1.4']);
    }

}
