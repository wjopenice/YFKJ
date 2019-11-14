<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

class TreeController extends ApiBaseController
{

    /** 获取我的财富树列表 */
    public function GET_treeListAction()
    {

        $treeModel = new TreeModel();
        $where['page'] = input('page');
        $treeList = $treeModel->getTreeList($this->uid, $where);
        $this->success($treeList);
    }

    /** 创建财富树 */
    public function Post_createTreeAction()
    {

        $this->checkLogin();
        $where['uid'] = $this->uid;
        $where['currency_id'] = 1;
        $where['name'] = input('name');
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
        $payMoney = getTreeUpgradeMoney(0, $level, $where['money'], $where['growth_ratio']);

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
            $this->warning($pay_res['message']);
        }

        $where['order_no'] = $order_res['order_no'];
        $treeModel = new TreeModel();
        $res = $treeModel->createTree($where, $payMoney);
        if (is_error($res))
        {
            $this->error('创建财富树失败');
        }

        $this->success($res);

    }

    /** 获取财富树信息 */
    public function Get_treeInfoAction()
    {
        $tree_id = input('tree_id');
        if (empty($tree_id)){

            $this->error('错误信息');
        }

        $treeModel = new TreeModel();
        $treeInfo = $treeModel->getTreeInfo($tree_id);
        // 参与记录
        $join_state = false;
        if ($this->uid)
        {
            $join_id = \think\Db::name('tree_user')->where(['uid' => $this->uid, 'tree_id' => $tree_id])->value('id');
            if ($join_id)
            {
                $join_state = true;
            }
        }
        $treeInfo['join_state'] = $join_state;
        if ($treeInfo)
        {
            $this->success($treeInfo);
        }


        $this->error('未找到信息');

    }

    /** 加入财富树 */
    public function Post_joinTreeAction()
    {
        $this->checkLogin();
        $where['uid'] = $this->uid;
        $where['id'] = input('tree_id');
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
            $this->error('未找到水滴还信息');
        }

        /** 1.获取推荐用户 */
        $code_uid = \think\Db::name('user')->where('uid', $this->uid)->value('puid');
        if (empty($code_uid))
        {
            $this->warning('推荐用户不存在');
        }

        /** 2. 判断推荐用户是否存在财富树下 */
        $treeUser_mode = new TreeUserModel();
        $code_tree_user = $treeUser_mode->where(['tree_id' => $tree['id'], 'uid' => $code_uid])->field('uid')->find();

        if (empty($code_tree_user))
        {
            // 推荐人没有加入默认放到系统下
            $code_uid = 1;
        }

        /** 3. 判断当前用户是否存在财富树下 */
        $self_tree_user = $treeUser_mode->where(['tree_id' => $tree['id'], 'uid' => $this->uid])->field('uid')->find();
        if (!empty($self_tree_user))
        {
            $this->warning('您已经加入过');
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
            if ($pay_res['message'] == '余额不足')
            {
                $this->success(['error' => 1, 'msg' => '余额不足']);
            }
            $this->warning($pay_res['message']);
        }

        /** 获取位置 */
        $tree_location = $treeUser_mode->getTreeLocation($tree, $this->uid, $code_uid);
        if (is_error($tree_location))
        {
            logs($pay_res['order_no'].'创建财富树用户失败, 未找到位置财富树id:'.$tree['id'], '', 'treeuser');
            $this->error($tree_location['message']);
        }

        $tree_location['tuid'] = $code_uid;
        /** 增加财富树成员 */
        $add_res = $treeUser_mode->addTreeUser($this->uid, $tree, $pay_res, $tree_location);
        if (is_error($add_res))
        {
            $this->warning('加入失败, 如已扣费请联系管理员');
        }

        /** 奖励流水 */
        $treeLog_model = new TreeLogModel();
        $rewardInfo['tree_id'] = $tree['id'];
        $rewardInfo['scene'] = 1;
        $rewardInfo['money'] = $tree['money'];
        $rewardInfo['vip_level'] = 1; /** 首次加入会员等级为1 */
        $rewardInfo['currency_id'] = $tree['currency_id'];
        $rewardInfo['order_no'] = $pay_res['order_no'];
        $rewardInfo['remark'] = '加入财富树';
        $treeLog_model->sendJoinReward($this->uid, $rewardInfo);
        $this->success(['error' => 0, 'msg' => '加入成功']);

    }

    /** 获取财富树流水记录 */
    public function Get_treeLogAction()
    {

        $where['id'] = input('tree_id');
        $where['page'] = input('page');

        $tree_id = input('tree_id');
        if (empty($tree_id)){

            $this->error('未找到财富树');
        }
        $tree_model = new TreeModel();
        $tree =  $tree_model->getJoinTreeInfo($tree_id);
        if (empty($tree))
        {
            $this->error('未找到财富树');
        }

        /** 流水记录 */
        $treeLog_model = new TreeLogModel();
        $data = array();
        if ($where['page'] == 1)
        {
            $head = $treeLog_model->incomeAndExpense($this->uid, $tree['id']);
            $data['head'] = $head;
        }

        $list = $treeLog_model->treeLogList($this->uid, $where['id']);
        $data['list'] = $list;
        $this->success($data);
    }

    /** 财富树下级成员 */
    public function Get_treeUserAction()
    {

        $tree_id = input('tree_id');
        $treeUser_model = new TreeUserModel();
        $tree_user = $treeUser_model->getTreeUserOrderLevelUp($this->uid, $tree_id);
        /** 获取用户注册信息及二维码 */
        $user_Model = new UserModel();
        $tree_user['users'] = $user_Model->getTreeUserInfo($tree_user['users']);
        $this->success($tree_user);
    }

    /** 财富树升级信息 */
    public function Get_upgradeInfoAction()
    {
        if (empty($this->uid))
        {
            $this->error('未登录无法获取');
        }
        $tree_id = input('tree_id');
        if (empty($tree_id))
        {
            $this->error('未获取到财富树');
        }

        $tree_model = new TreeModel();
        $tree = $tree_model->getTreeInfo($tree_id);
        if (empty($tree))
        {
            $this->error('未获取到财富树');
        }
        /** 获取当前等级 */
        $treeUser_model = new TreeUserModel();
        $vip_level = $treeUser_model->getTreeLevel($this->uid, $tree_id);
        if (empty($vip_level))
        {
            $vip_level = 0;
        }
        /** 查询用户余额 */
        $wallet_model = new WalletModel();
        $money = $wallet_model->getMoney($this->uid, $tree['currency_id']);

        $result['money'] = $money;
        $result['vip_level'] = $vip_level;

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
            $this->toast($upgradeInfo['message']);
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
            $this->warning('支付失败, 请检查当前余额是否充足');
        }
        $free_money = $wallet_model->getMoney($this->uid, $upgradeInfo['currency_id']);

        $upgradeInfo['tolevel'] = $where['tolevel'];
        $upgradeInfo['tree_id'] = $where['tree_id'];
        $upgradeInfo['order_no'] = $pay_res['order_no'];
        $upgradeInfo['uid'] = $this->uid;

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

        /** 查询当前余额 */

        $this->success(['vip_level' => $where['tolevel'], 'money' => $free_money]);


    }




}
