<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 include */
include APP_MODULES.'/API/models/Currency.php';
include APP_MODULES.'/API/models/Tree.php';
include APP_MODULES.'/API/models/TreeUser.php';
include APP_MODULES.'/API/models/User.php';
include APP_MODULES.'/API/models/Wallet.php';
include APP_MODULES.'/API/models/TreeLog.php';
include APP_MODULES.'/API/models/DisLog.php';
include APP_MODULES.'/API/models/PayOrder.php';

class TreeController extends Rest
{

    /**
     * 首页 获取我的财富树列表
     */
    public function GET_treeListAction()
    {

        $this->checkLogin();
        $treeModel = new TreeModel();
        $treeList = $treeModel->getTreeList($this->uid);
        $this->success($treeList);
    }

    /** 创建财富树 */
    public function Post_createTreeAction()
    {

        $this->checkLogin();
        $where['uid'] = $this->uid;
        $where['currency_id'] = input('currency_id');
        $where['level'] = input('level');
        $where['limit'] = input('limit');
        $where['money'] = input('money');
        $where['growth_ratio'] = input('growth_ratio');
        $tree_validate = new \validate\Tree();
        /* 验证 */
        if (!$tree_validate->scene('add')->check($where)) {

            $this->error($tree_validate->getError());
        }

        /** 计算支付金额 */
        $level = (int)$where['level'];
        $payMoney = getTreeUpgradeMoney(0, $level, $where['money'],  $where['growth_ratio']);

        /** 生成订单 */
        $payOrder_model = new PayOrderModel();
        $order_res = $payOrder_model->createOrder($this->uid,  $where['currency_id'], $payMoney, 1);
        if (is_error($order_res))
        {
            $this->error($order_res['message']);
        }
        /**  支付 支付后创建财富树 */
        $wallet_model = new WalletModel();
        $pay_res = $wallet_model->payMoney($this->uid,  $order_res['order_no']);
        if (is_error($pay_res))
        {
            $this->error($pay_res['message']);
        }

        $where['order_no'] = $order_res['order_no'];
        $treeModel = new TreeModel();
        $res = $treeModel->createTree($where);
        if (is_error($res))
        {
            $this->error('Create tree fail!');
        }

        $this->success($res);

    }

    /** 获取财富树信息 */
    public function Get_treeInfoAction()
    {
        $roomNumber = input('roomNumber');
        if (empty($roomNumber)){

            $this->error('can not find tree');
        }

        $treeModel = new TreeModel();
        $treeInfo = $treeModel->getTreeInfo($roomNumber);
        if ($treeInfo && is_array($treeInfo))
        {
            $this->success($treeInfo);
        }

        $this->error('can not find tree');
        
    }

    /** 加入财富树 */
    public function Post_joinTreeAction()
    {
        $this->checkLogin();
        $where['uid'] = $this->uid;
        $where['id'] = input('tree_id');
        $where['invite_code'] = input('invite_code');

        $tree_validate = new \validate\Tree();
        /* 验证 */
        if (!$tree_validate->scene('join')->check($where)) {

            $this->error($tree_validate->getError());
        }
        /** 计算支付金额 */
        $treeModel = new TreeModel();
        $tree = $treeModel->getJoinTreeInfo($where['id']);
        if (empty($tree))
        {
            $this->error('can not find tree');
        }


        /** 1.获取推荐用户 */
        $user_Model = new UserModel();
        $code_uid = $user_Model->getUidByInviteCode($where['invite_code']);
        if (empty($code_uid))
        {
            $this->error('can not find the user');
        }


        /** 2. 判断用户是否存在财富树下 */
        $treeUser_mode = new TreeUserModel();
        $code_tree_user = $treeUser_mode->where(['tree_id' => $tree['id'], 'uid' => $code_uid])->field('uid')->find();
        if (empty($code_tree_user))
        {
            $this->error('user is not in this tree');
        }

        /** 生成订单 */
        $payOrder_model = new PayOrderModel();
        $order_res = $payOrder_model->createOrder($this->uid,  $tree['currency_id'], $tree['money'], 2);
        if (is_error($order_res))
        {
            $this->error($order_res['message']);
        }
        /**  支付  */
        $wallet_model = new WalletModel();
        $pay_res = $wallet_model->payMoney($this->uid,  $order_res['order_no']);

        if (is_error($pay_res))
        {
            $this->error($pay_res['message']);
        }

        /** 获取位置 */
        $tree_location = $treeUser_mode->getTreeLocation($tree, $this->uid, $code_uid);
        if (is_error($tree_location))
        {
            logs($pay_res['order_no'].'创建财富树用户失败, 未找到位置', '', 'treeuser');
            $this->error($tree_location['message']);
        }

        /** 增加财富树成员 */
        $add_res = $treeUser_mode->addTreeUser($this->uid, $tree, $pay_res, $tree_location);

        if (is_error($add_res))
        {
            logs($pay_res['order_no'].'创建财富树用户失败, 保存记录失败', '', 'treeuser');
            $this->error('join fail');
        }

        /** 奖励流水 */
        $treeLog_model = new TreeLogModel();
        $rewardInfo['tree_id'] = $tree['id'];
        $rewardInfo['money'] = $tree['mondy'];
        $rewardInfo['vip_level'] = 1; /** 首次加入会员等级为1 */
        $rewardInfo['currency_id'] = $tree['currency_id'];
        $rewardInfo['order_no'] = $pay_res['order_no'];
        $rewardInfo['remark'] = 'join tree';
        $treeLog_model->sendReward($this->uid, $rewardInfo);
        $this->success('join success');
        
    }

    /** 获取财富树下边 */
    public function Get_treeLogAction()
    {
        $this->checkLogin();
        $tree_id = input('tree_id');
        if (empty($tree_id)){

            $this->error('can not find tree');
        }

        $tree_model = new TreeModel();
        $tree =  $tree_model->getJoinTreeInfo($tree_id);
        if (empty($tree))
        {
            $this->error('can not find tree');
        }

        /** 流水记录 */
        $treeLog_model = new TreeLogModel();
        $head = $treeLog_model->incomeAndExpense($this->uid, $tree['id']);
        $list = $treeLog_model->treeLogList($this->uid, $tree['id']);

        $this->success(['head' => $head, 'list' => $list]);

    }

    /** 加载更多财务记录 */
    public function Get_treeLogMoreAction()
    {
        $this->checkLogin();
        $where['id'] = input('tree_id');
        $where['page'] = input('page');
        $tree_validate = new \validate\Tree();
        /* 验证 */
        if (!$tree_validate->scene('logmore')->check($where)) {

            $this->error($tree_validate->getError());
        }
        $treeLog_model = new TreeLogModel();
        $list = $treeLog_model->treeLogList($this->uid, $where['id']);
        $this->success($list);
    }

    /** 财富树下级成员 */
    public function Get_treeUserAction()
    {
         $this->checkLogin();
         $tree_id = input('tree_id');
         $treeUser_model = new TreeUserModel();
         $tree_user = $treeUser_model->getTreeUserOrderVip($this->uid, $tree_id);
         /** 获取用户注册信息及二维码 */
         $user_Model = new UserModel();
         $tree_user = $user_Model->getTreeUserInfo($tree_user);
         $this->success($tree_user);
    }

    /** 财富树信息 */
    public function Get_treeAction()
    {

        $this->checkLogin();
        $tree_id = input('tree_id');
        if (empty($tree_id)){

            $this->error('can not find tree');
        }

        $treeModel = new TreeModel();
        $treeInfo = $treeModel->getTreeInfoById($tree_id);
        $this->success($treeInfo);
    }

    /** 财富树升级信息 */
    public function Get_upgradeInfoAction()
    {
        $this->checkLogin();
        $tree_id = input('tree_id');
        if (empty($tree_id))
        {
            $this->error('can not find tree');
        }

        $tree_model = new TreeModel();
        $tree = $tree_model->getTreeInfoById($tree_id);
        if (empty($tree))
        {
            $this->error('can not find tree');
        }
        /** 获取当前等级 */
        $treeUser_model = new TreeUserModel();
        $vip_level =  $treeUser_model->getTreeLevel($this->uid, $tree_id);
        if (empty($vip_level))
        {
            $this->error('user is not in this tree');
        }
        /** 查询用户余额 */
        $wallet_model = new WalletModel();
        $money = $wallet_model->getMoney($this->uid, $tree['currency_id']);

        $result['tree'] = $tree;
        $result['userInfo']['money'] = $money;
        $result['userInfo']['vip_level'] = $vip_level;

        $this->success($result);

        


    }

    /** 升级当前等级 */
    public function Post_upgradeAction()
    {

         $this->checkLogin();
         $userModel = new UserModel();
         $where['uid'] = $this->uid;
         $where['tree_id'] = input('tree_id');
         $where['tolevel'] = input('tolevel');
        $tree_validate = new \validate\Tree();
        /* 验证 */
        if (!$tree_validate->scene('upgrade')->check($where)) {

            $this->error($tree_validate->getError());
        }
        
        /** 计算升级价格于币种 */
        $treeUser_model = new TreeUserModel();
        $upgradeInfo = $treeUser_model->upgradeMoney($where['uid'],  $where['tolevel'], $where['tree_id']);
        if (is_error($upgradeInfo))
        {
            $this->error($upgradeInfo['message']);
        }
        /** 生成订单 */
        $payOrder_model = new PayOrderModel();
        $order_res = $payOrder_model->createOrder($this->uid, $upgradeInfo['currency_id'], $upgradeInfo['money'], 3);
        if (is_error($order_res))
        {
            $this->error($order_res['message']);
        }
        /**  支付  */
        $wallet_model = new WalletModel();
        $pay_res = $wallet_model->payMoney($this->uid,  $order_res['order_no']);

        if (is_error($pay_res))
        {
            $this->error($pay_res['message']);
        }

        $upgradeInfo['tolevel'] = $where['tolevel'];
        $upgradeInfo['tree_id'] = $where['tree_id'];
        $upgradeInfo['order_no'] = $pay_res['order_no'];
        $upgradeInfo['uid'] = $this->uid;
        unset($upgradeInfo['currency_id']);
        /** 升级 */
        $upgrade_res = $treeUser_model->upgradeTreeUser($upgradeInfo);
        if (is_error($upgrade_res))
        {
            logs("升级失败{$pay_res['order_no']},tree_id:{$upgradeInfo['tree_id']}", '', 'treeupgrade');
            $this->error($upgrade_res['message']);

        }

        /** 升级奖励 */
        $treeLog_model = new TreeLogModel();
        $treeLog_model->sendUpgradeReward($upgradeInfo);
        $this->success('upgrade tree success');

        
    }

    /** 获取财富树邀请码信息 */
    public function Get_treeInviteAction()
    {
        $this->checkLogin();
        $tree_id = input('tree_id');
        if (empty($tree_id)){

            $this->error('can not find tree');
        }

        $userModel = new UserModel();
        $treeModel = new TreeModel();

        $user_invite_code = $userModel->getUserColumn($this->uid, 'invite_code').'';
        $tree_invite_code = $treeModel->where(['id' => $tree_id])->value('room_number');
        if (empty($tree_id)){

            $this->error('can not find tree');
        }

        $this->success(['user_code' => $user_invite_code, 'tree_code' => $tree_invite_code]);
    }

    /** 三维数组转二维 */
    public function arraytoSecond($data = [])
    {
        $result = [];
        foreach ($data as $item1)
        {
            foreach ($item1 as $value)
            {
                if (is_array($value))
                {
                    foreach ($value as $value2)
                    {
                        $result[] = $value2;
                    }

                } else
                {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }    


}