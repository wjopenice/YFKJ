<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

use think\Db;
class UserController extends AdminApi {

    /** 列表 */
    public function listAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];
        $whereor = [];
        /** 姓名 */
        if (input('name'))
        {
            $whereor[] = ['nickname', 'like', input('name').'%'];
        }
        /** 账号 */
        if (input('name'))
        {
            $whereor[] = ['username', 'like', input('name').'%'];
        }
        /** 推荐人姓名 */
        if (input('puser'))
        {
            $puid = \think\Db::name('user')->where('nickname', 'like', input('puser').'%')->value('uid');
            $where[] = ['puid', '=', $puid ? $puid : ''];
        }
        $list = \think\Db::name('user')->where($where)->whereOr($whereor)->field('uid,username,nickname,avatar,invite_code,puid,create_time')->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $pusername = \think\Db::name('user')->where('uid', $item['puid'])->value('nickname');
            $list[$key]['puser'] = $pusername;
            $list[$key]['avatar'] = tomedia($item['avatar']);
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }


        $total = \think\Db::name('user')->where($where)->whereOr($whereor)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 钱包 */
    public function walletAction()
    {
        $uid = input('uid');

        if (empty($uid))
        {
           $this->error('缺少关键词');
        }
        $list = \think\Db::name('wallet')->where('uid', $uid)->field('uid,currency_id,total,free,lock')->select();
        foreach ($list as $key => $item)
        {
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency_name'] = $currency;
        }

        $this->success($list);
    }

    /** 充值 */
    public function rechargeAction()
    {

         $uid = input('uid');
         $currency_id = input('currency_id');
         $money = input('money');
         if (empty($uid) || empty($currency_id) || empty($money))
         {
             $this->error('缺少关键词');
         }

        /** 获取当前币种 */
        $wallet =  \think\Db::name('wallet')->where(['uid' => $uid, 'currency_id' => $currency_id])->field(['id', 'total', 'free', 'lock', 'consume'])->find();

        $wallet_up['total'] = $wallet['total'] + $money;
        $wallet_up['free'] = $wallet['free'] + $money;
        $wallet_up['update_time'] = time();


        /** 账单记录 */
        $bill_add['uid'] = $uid;
        $bill_add['currency_id'] = $currency_id;
        $bill_add['order_no'] = '0';
        $bill_add['money'] = $money;
        $bill_add['remark'] = '后台操作';
        $bill_add['create_time'] = time();

        Db::startTrans();
        try {

            Db::name('wallet')->where(['id' => $wallet['id']])->update($wallet_up);
            Db::name('bill')->insert($bill_add);
            Db::commit();
            $this->success();

        } catch (\Exception $e) {
            Db::rollback();
            $this->error();
        }

    }

}
