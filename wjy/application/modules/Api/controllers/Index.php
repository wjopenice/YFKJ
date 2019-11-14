<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */


/** 系统框架把命名空间关闭了 所有暂时先使用require */
include APP_MODULES.'/Api/models/Currency.php';
include APP_MODULES.'/Api/models/Redgroup.php';
include APP_MODULES.'/Api/models/Reward.php';
include APP_MODULES.'/Api/models/Wallet.php';
class IndexController extends Rest
{

    /**
     * 首页 获取所有币种
     */
    public function GET_currencyListAction()
    {
        $name = input('keyword');
        $uid = $this->getUid();
        $currency_model = new CurrencyModel();
        $list = $currency_model->getList($name, $this->uid);
        return $this->success($list);
    }

    /**
     * 首页 获取第一个
     */
    public function GET_currencyInfoAction()
    {

        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $currency_model = new CurrencyModel();
        $currency = $currency_model->getCurrencyById($currency_id);
        return $this->success($currency);
    }

    /**
     * 首页 获取第一个
     */
    public function GET_currencyFirstAction()
    {

        $currency_model = new CurrencyModel();
        $currency = $currency_model->getFirst();
        return $this->success($currency);
    }


    /** 首页 轮播图 */
    public function GET_adListAction()
    {
        $list = \think\Db::name('ad')->field('id,pic,type')->order('order_id asc')->select();
        foreach ($list as $key => $item)
        {
            $list[$key]['pic'] = tomedia($item['pic']);
        }
        $this->success($list);
    }

    /** 轮播详情 */
    public function Get_adDetailAction()
    {
         $id = input('id');
         if (empty($id))
         {
             $this->error('未找到相关信息');
         }
         $ad = \think\Db::name('ad')->where('id', $id)->field('id,content')->find();
         $this->success($ad);
    }

    /** 首页 获取特定币种红包 */
    public function Get_currencyRedAction()
    {
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $room_number = input('room_number');
        $where['currency_id'] = $currency_id;
        $where['room_number'] = $room_number;
        $where['page'] = input('page');
        $list = RedgroupModel::getRedgroupByCurrency($where);
        $this->success($list);


    }


    /** 首页 先整合为一个方法 */
    public function Get_homeListAction()
    {
        $currency_id = input('currency_id');
        if (empty($currency_id))
        {
            $this->error('currency can not be empty');
        }
        $currency_model = new CurrencyModel();
        $currency = $currency_model->getList();

        // 相关币种红包
        $redList = \think\Db::name('redgroup')->where(['currency_id' => $currency_id, 'password' => 0])->order('create_time desc')->page(1, 8)->field('id,name,currency_id')->select();
        foreach ($redList as $key => $item)
        {
            foreach ($currency as $key2 => $item2)
            {
                if ($item['currency_id'] == $item2['id'])
                {
                    $redList[$key]['icon'] = $item2['icon'];
                }

            }
        }

        $this->success(['currency' => $currency, 'redList' => $redList]);


    }


    /** 首页 搜索红包 */
    public function Get_searchRedAction()
    {

        $room_number = input('room_number');

        if (empty($room_number))
        {
            $this->success([]);
        }

        // 搜索房间号码
        $list = \think\Db::name('redgroup')->where('room_number', 'like', $room_number.'%')->field('id,currency_id,name,money,count,send_rule,create_time')->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->field('id,name,tag,icon')->find();
            $currency['icon'] = tomedia($currency['icon']);
            $list[$key]['currency'] = $currency;
            $users_count = \think\Db::name('red_collection')->where(['redgroup_id' => $item['id']])->count();
            $list[$key]['user_count'] = $users_count;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        $this->success($list);
    }


    /** 首页 热门币种红包 */
    public function Get_hotRedAction()
    {

        $room_number = input('room_number');
        $page = input('page') ? input('page') : 1;
        $where = [];
        if (!empty($room_number))
        {
            $where[] = ['room_number', 'like', $room_number.'%'];
        }
        $where[] = ['password', '=', 0];
        // 搜索房间号码
        $list = \think\Db::name('redgroup')->where($where)->field('id,currency_id,name,money,count,send_rule,create_time')->order('create_time desc')->page($page, 20)->select();
        foreach ($list as $key => $item)
        {
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->field('id,name,tag,icon')->find();
            $currency['icon'] = tomedia($currency['icon']);
            $list[$key]['currency'] = $currency;
            $users_count = \think\Db::name('red_collection')->where(['redgroup_id' => $item['id']])->count();
            $list[$key]['user_count'] = $users_count;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        $this->success($list);
    }

    /** 获取奖励 */
    public function Get_rewardAction()
    {
        $this->checkLogin();
        $res = RewardModel::sendRegisterReward($this->uid, 0);
        if ($res['result'] == 'success')
        {
            $data['reward'] = true;
            $data['message'] = $res['msg'];
        } else
        {
            $data['reward'] = false;
        }

        $this->success($data);

    }


    /** 项目方 */
    public function Post_cooperationAction()
    {

       $this->checkLogin();
       $money = \think\Db::name('wallet')->where(['uid' => $this->uid, 'currency_id' => 1])->value('total');
       if ($money < 200)
       {
           $this->warning('申请币种账号必须包含200USDT');
       }

       $where['uid'] = $this->uid;
       $where['name'] = input('name');
       $where['name_lessen'] = input('name_lessen');
       $where['contract'] = input('contract');
       $where['email'] = input('email');
       $where['logo'] = Upload::uploadImg();
       $where['create_time'] = time();
       $where['update_time'] = time();

       $res = \think\Db::name('proposal')->insert($where);

       if (empty($res))
       {
           $this->toast('申请失败');
       }

        $this->success('申请成功');

    }



}
