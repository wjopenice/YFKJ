<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/API/models/Wallet.php';
include APP_MODULES.'/API/models/PayOrder.php';
include APP_MODULES.'/API/models/Redgroup.php';
include APP_MODULES.'/API/models/RedLog.php';
include APP_MODULES.'/API/models/User.php';
include APP_MODULES.'/API/models/DisLog.php';

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

            $this->error($tree_validate->getError());
        }
        if (input('need_pass'))
        {
            $where['password'] = input('password');
            if (empty($where['password']) || str_strlen($where['password']) < 6)
            {
                 $this->error('password must not be less than 6 digits');
            }
        }

        /** 支付的金额为红包的二倍 */
        $pay_money = $where['money'] * \beans\ProfitCode::RED_DEPOSIT;

        /** 创建订单 */
        $payOrder_model = new  PayOrderModel();
        $order_res = $payOrder_model->createOrder($this->uid, $where['currency_id'], $pay_money, 5);
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

        $this->success($result);

    }

    /** 获取红包群信息 */
    public function Post_redGroupInfoAction()
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

    /** 抢红包 */
    public function Post_grabRedpicketAction()
    {
        $this->checkLogin();
        $redpicket_id = input('redpicket_id');
        if (empty($redpicket_id))
        {
            $this->error('redpicket can not be empty');
        }

        $result = RedgroupModel::grabRedpicket($this->uid, $redpicket_id);
    }

    /** 红包记录明细 */
    public function Get_redpicketLog()
    {
        $this->checkLogin();
        $redpicket_id = input('redpicket_id');
        if (empty($redpicket_id))
        {
            $this->error('redpicket can not be empty');
        }
        $logInfo = RedgroupModel::getRedpicketLog($this->uid, $redpicket_id);

        $this->success($logInfo);


    }

    /** 群红包记录 */
    public function Get_redgroupLog()
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
        $redGroup_model->setOnlineCount($redgroup_id,$type);
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
            $this->success();
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
            $where['reply_id'] = input('parent_id');
        }

        $red_validate = new \validate\Redgroup();
        /* 验证 */
        if (!$red_validate->scene('comment')->check($where)) {

            $this->error($red_validate->getError());
        }

        /** 添加评论 */
        if (RedgroupModel::setComment($where))
        {
            $this->success();
        }
        $this->error();
    }

    /** 点赞 */
    public function Post_zanAction()
    {
        $this->checkLogin();
        $where['comment_id'] = input('comment_id');
        $where['uid'] = $this->uid;
        if (empty($where['comment_id']))
        {
            $this->error();
        }
        
        if (RedgroupModel::setZan($where))
        {
            $this->success();
        }

        $this->error();
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


    

}