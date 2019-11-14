<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/Api/models/Wallet.php';
include APP_MODULES.'/Api/models/PayOrder.php';
include APP_MODULES.'/Api/models/Redgroup.php';
include APP_MODULES.'/Api/models/RedLog.php';
include APP_MODULES.'/Api/models/User.php';
include APP_MODULES.'/Api/models/DisLog.php';
include APP_PATH.'/library/vendor/phpqrcode.php';
include APP_MODULES.'/Api/models/Profit.php';
include APP_MODULES.'/Api/models/Reward.php';

class RedgroupController extends Rest
{

    /** 红包 获取红包群 */
    public function GET_ListAction()
    {

        $this->checkLogin();

        $where['page'] = input('page');
        $where['uid'] = $this->uid;
        $list = RedgroupModel::getList($where);
        $this->success($list);

    }

    /** 获取下一个 */
    public function GET_nextAction()
    {
        $this->checkLogin();
        $page = input('page');
        $where['uid'] = $this->uid;
        $where['page'] = $page;
        $where['currency'] = input('currency');
        $where['fuid'] = input('fuid');
        $where['type'] = input('type');
        $list = RedgroupModel::getNext($where);
        $this->success($list);
    }

    /** 创建红包 */
    public function Post_createAction()
    {
        $this->checkLogin();
        $where['uid'] = $this->uid;
        $where['name'] = input('name');
        $where['currency_id'] = input('currency_id');
        $where['money'] = input('money');
        $where['count'] = input('count');
        $where['send_rule'] = input('send_rule');
        $tree_validate = new \validate\Redgroup();
        /* 验证 */
        if (!$tree_validate->scene('create')->check($where)) {

            $this->toast($tree_validate->getError());
        }
        if (input('need_pass') == 1)
        {
            $where['password'] = input('password');
            if (empty($where['password']) || str_strlen($where['password']) < 6)
            {
                 $this->toast('password must not be less than 6 digits');
            }
        }

        /** 查看币种 */
        $currency = \think\Db::name('currency')->where('id', $where['currency_id'])->find();
        if (empty($currency))
        {
            $this->toast('当前币种不存在');
        }
        if ($currency['state'] != 1)
        {
            //禁用状态
            $this->warning('当前币种禁止使用');
        }

        /** 支付的金额为红包的二倍 */
        $pay_money = $where['money'] * \beans\ProfitCode::RED_DEPOSIT;

        /** 创建订单 */
        $payOrder_model = new  PayOrderModel();
        $order_res = $payOrder_model->createOrder($this->uid, $where['currency_id'], $pay_money, 4);
        if (is_error($order_res))
        {
            $this->error($order_res['message']);
        }
        $wallet = new WalletModel();
        $pay_res = $wallet->payMoney($this->uid, $order_res['order_no']);

        if (is_error($pay_res))
        {
            $this->error($pay_res['message']);
        }

        //$pay_res = ['order_no' => '2019082962477393738418'];
        /** 生成红包群与红包 */
        $redGroup_model = new RedgroupModel();
        $result = $redGroup_model->createGroup($where, $pay_res);
        if (is_error($result))
        {
            $this->error($result['message']);
        }

        /** 发送奖励 */
        if ($currency['name'] != 'MCT')
        {
            $reward_res = RewardModel::sendRedgroupReward($this->uid, $result['redgroup_id']);
            if ($reward_res['result'] == 'success')
            {
                $result['message'] = $reward_res['msg'];
            }
        }

        $this->success($result);

    }

    /** 获取红包群信息 */
    public function Get_redGroupInfoAction()
    {
       $this->checkLogin();
       $redgroup_id = input('redgroup_id');
       if (empty($redgroup_id))
       {
           $this->error('redgroup can not be empty');
       }

       $info = RedgroupModel::getGroupInfo($this->uid, $redgroup_id);
       $this->success($info);

    }

    /** 获取群信息 */
    public function Get_groupDetailAction()
    {
       $this->checkLogin();
       $redgroup_id = input('redgroup_id');
       if (empty($redgroup_id))
       {
           $this->error('redgroup can not be empty');
       }

       $info = RedgroupModel::getGroupDetail($this->uid, $redgroup_id);
       $this->success($info);

    }

    /** 抢红包前验证 */
    public function Post_grabBeforeAction()
    {
        $this->checkLogin();
        $redpacket_id = input('redpacket_id');
        if (empty($redpacket_id))
        {
            $this->error('redpacket can not be empty');
        }

        // 验证红包是否存
        $redpacket = \think\Db::name('redpacket')->where('id', $redpacket_id)->field('id,realmoney,currency_id')->find();
        if (empty($redpacket))
        {
           $this->toast('红包不存在');
        }

        // 判断是否抢过
        $log = \think\Db::name('redpacket_log')->where(['uid' => $this->uid, 'redpacket_id' => $redpacket_id])->find();
        if (!empty($log))
        {
            $this->success(['log' => true]);
        }
        // 验证红包是否抢光
        $redlog = \think\Db::name('redpacket_log')->where(['uid' => 0, 'redpacket_id' => $redpacket_id])->find();
        if (empty($redlog))
        {
            // 判断余额是否充足
            $walletModel = new WalletModel();
            $user_money = $walletModel->getMoney($this->uid, $redpacket['currency_id']);
            if ($user_money < $redpacket['realmoney'])
            {
                $this->warning('当前余额不足');
            }

            $join_log = \think\Db::name('redpacket_join')->where(['uid' => $this->uid, 'redpacket_id' => $redpacket_id])->find();
            if (empty($join_log))
            {
                $redlog_join['uid'] = $this->uid;
                $redlog_join['redpacket_id'] = $redpacket_id;
                $redlog_join['create_time'] = time();
                \think\Db::name('redpacket_join')->insert($redlog_join);
            }

            $this->success(['log' => false, 'has' => false]);
        }

        $this->success(['log' => false, 'has' => true]);

    }

    /** 抢红包 */
    public function Post_grabRedpacketAction()
    {
        $this->checkLogin();
        $redpicket_id = input('redpacket_id');
        if (empty($redpicket_id))
        {
            $this->error('redpicket can not be empty');
        }


        $result = RedgroupModel::grabRedpicket($this->uid, $redpicket_id);
        if (is_error($result))
        {
            $this->error($result['message']);
        }

        $this->success($result);
    }

    /** 红包记录明细 */
    public function Get_redpacketLogAction()
    {
        $this->checkLogin();
        $redpicket_id = input('redpacket_id');
        if (empty($redpicket_id))
        {
            $this->error('redpicket can not be empty');
        }
        $logInfo = RedgroupModel::getRedpicketLog($this->uid, $redpicket_id);

        $this->success($logInfo);


    }

    /** 群红包记录 */
    public function Get_redgroupLogAction()
    {
        $this->checkLogin();
        $redgroup_id = input('redgroup_id');
        if (empty($redgroup_id))
        {
            $this->error('redgroup can not be empty');
        }
        $where['uid'] = $this->uid;
        $where['redgroup_id'] = $redgroup_id;
        $where['page'] = input('page');
        $list = RedgroupModel::getRedpicketList($where);
        $this->success($list);

    }

    /** 增加减少在线人数 */
    public function Post_onlineAction()
    {
        $this->checkLogin();
        $type = input('type');
        $redgroup_id = input('redgroup_id');
        if (empty($type))
        {
            $this->error('type can not be empty');
        }
        if (empty($redgroup_id))
        {
            $this->error('redgroup can not be empty');
        }

        $redGroup_model = new RedgroupModel();
        $redGroup_model->setOnlineCount($redgroup_id, $type, $this->uid);
        $this->success();
    }

    /** 收藏红包群 */
    public function Post_collectionAction()
    {
        $this->checkLogin();
        $redgroup_id = input('redgroup_id');
        if (empty($redgroup_id))
        {
            $this->error('redgroup can not be empty');
        }

        $result = RedgroupModel::collectionGroup($this->uid, $redgroup_id);
        if (is_error($result))
        {
            $this->error($result['message']);
        }
        if ($result)
        {
            $this->success($result);
        }
        $this->error();
    }

    /** 获取评论列表 */
    public function Get_getCommentAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        $where['page'] = input('page');
        $where['uid'] = $this->uid;
        if (empty($where['redgroup_id']))
        {
            $this->error('redgroup can not be empty');
        }

        $this->success(RedgroupModel::getComment($where));
    }

    /** 获取用户群信息 */
    public function Get_userGroupAction()
    {
        $this->checkLogin();
        $where['uid'] = input('uid');
        if (empty($where['uid']))
        {
            $this->error('user can not find');
        }
        $where['type'] = input('type');
        if (empty($where['type']))
        {
            $this->error('type can not find');
        }
        $list = RedgroupModel::userGroup($where);
        $this->success($list);


    }

    /** 评论 */
    public function Post_setCommentAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        $where['content'] = input('content');
        $where['uid'] = $this->uid;
        if (input('parent_id'))
        {
            $where['parent_id'] = input('parent_id');
        }
        if (input('reply_id'))
        {
            $where['reply_id'] = input('reply_id');
        }

        $red_validate = new \validate\Redgroup();
        /* 验证 */
        if (!$red_validate->scene('comment')->check($where)) {

            $this->toast($red_validate->getError());
        }

        //评论成功
        $data = RedgroupModel::setComment($where);
        if (is_error($data))
        {
            $this->toast('评论失败');
        }
        //$data['create_time'] = date('Y-m-d H:i:s', strtotime($data['create_time']));
        $this->success($data);
    }

    /** 点赞 */
    public function Post_zanAction()
    {
        $this->checkLogin();
        $where['comment_id'] = input('comment_id');
        $where['uid'] = $this->uid;
        if (empty($where['comment_id']))
        {
            $this->error('please choose comment');
        }

        $res = RedgroupModel::setZan($where);
        if (is_error($res))
        {
            $this->error();
        }

       $this->success();
    }

    /** 分享 */
    public function Get_shareAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        if (empty(input('redgroup_id')))
        {
            $this->toast('redgroup can not be empty');
        }

        //红包群信息
        $redgroup = \think\Db::name('redgroup')->where('id', $where['redgroup_id'])->field('id,room_number')->find();
        // 个人信息
        $invite_code = UserModel::getInviteInfo($this->uid)['invite_code'];

        /** 注册地址 */
        $url = createUrl('').'#/register?code='.$invite_code;
        /** 生成文件名 */
        $filename = 'qrcode/'.$invite_code.'.png';
        /** 生成二维码图片 */
        QRcode::png($url, APP_ATTACHMENT.'/'.$filename, 'L', 6, 2);
        $register_qrcode = tomedia($filename).'?time='.time();
        $redgroup['qrcode_url'] = $register_qrcode;
        $redgroup['invite_code'] = $invite_code;
        $this->success($redgroup);



    }

    /** 置顶 */
    public function Post_istopAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        $where['uid'] = $this->uid;
        if (empty($where['redgroup_id']))
        {
            $this->error();
        }

        $res = RedgroupModel::groupIstop($where);
        if (is_error($res))
        {
            $this->error($res['message']);
        }
        if ($res)
        {
            $this->success();
        }

        $this->error();
    }

    /** 获取公告 */
    public function Get_groupNoticeAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        if (empty($where['redgroup_id']))
        {
            $this->error();
        }

        $group = \think\Db::name('redgroup')->where('id', $where['redgroup_id'])->field('uid,notice')->find();

        $data = [];
        if ($group['uid'] == $this->uid)
        {
            $data['iscreate'] = true;
        }
        $data['content'] = $group['notice'];
        $this->success($data);


    }

    /** 设置公告 */
    public function Post_setGroupNoticeAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        $where['uid'] = $this->uid;
        $content = input('content');
        if (empty($where['redgroup_id']))
        {
            $this->error();
        }
        $res = RedgroupModel::setNotice($where, $content);
        if (is_error($res))
        {
            $this->error($res['message']);
        }

        $this->success();
    }

    /** 获取规则 */
    public function Get_groupRuleAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        if (empty($where['redgroup_id']))
        {
            $this->error();
        }

        $group = \think\Db::name('redgroup')->where('id', $where['redgroup_id'])->field('uid,rule')->find();

        $data = [];
        if ($group['uid'] == $this->uid)
        {
            $data['iscreate'] = true;
        }
        $data['content'] = $group['rule'];
        $this->success($data);


    }

    /** 设置规则 */
    public function Post_setGroupRuleAction()
    {
        $this->checkLogin();
        $where['redgroup_id'] = input('redgroup_id');
        $where['uid'] = $this->uid;
        $content = input('content');
        if (empty($where['redgroup_id']))
        {
            $this->error();
        }
        $res = RedgroupModel::setRule($where, $content);
        if (is_error($res))
        {
            $this->error($res['message']);
        }

        $this->success();
    }

    /** 验证密码 */
    public function Get_neePassAction()
    {
        $this->checkLogin();

        $redgroup_id = input('redgroup_id');
        if (empty($redgroup_id))
        {
            $this->error('未获取到群信息');
        }

        $group = \think\Db::name('redgroup')->where('id', $redgroup_id)->field('uid,password')->find();
        if ($group['password'] == 0 || $group['uid'] == $this->uid)
        {
            $this->success(['need' => false]);
        }
        /** 获取收藏信息 */
        $collection = \think\Db::name('red_collection')->where(['redgroup_id' => $redgroup_id, 'uid' => $this->uid])->find();
        if (!empty($collection))
        {
            $this->success(['need' => false]);
        }
        /** 获取验证记录 */
        $redgroup_auth = \think\Db::name('redgroup_auth')->where(['redgroup_id' => $redgroup_id, 'uid' => $this->uid])->find();
        if (!empty($redgroup_auth))
        {
            $this->success(['need' => false]);
        }

        $this->success(['need' => true, 'password' => md5($group['password'])]);

    }

    /** 红包攻略 */
    public function Get_redstrategyAction()
    {
        $content = \think\Db::name('config')->where(['group' => 'redgroup', 'name' => 'strategy'])->value('value');
        $this->success($content);
    }




}
