<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class TreeController extends AdminApi {

    /** 财富树列表 */
    public function treeListAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('number'))
        {
            $where[] = ['room_number', '=', input('number')];
        }
        /** 创建人 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }

        $list = \think\Db::name('tree')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            /** 搜索财富树人数 */
            $user_count = \think\Db::name('tree_user')->where('tree_id', $item['id'])->count();
            $list[$key]['user_count'] = $user_count;

            /** 成长级 */
            $grow_up = \think\Db::name('tree_user')->where('tree_id', $item['id'])->max('level');
            $list[$key]['grow_up'] = $grow_up;

            /** 贡献收益 */
            $money = \think\Db::name('tree_log')->where(['tree_id' => $item['id'], 'uid' => 1])->sum('money');
            $list[$key]['tree_money'] = $money;
        }
        $total = \think\Db::name('tree')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 财富树用户 */
    public function userListAction()
    {
        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('number'))
        {
            $tree_id = \think\Db::name('tree')->where('room_number', input('number'))->value('id');
            $where[] = ['tree_id', '=', $tree_id ? $tree_id : ''];
        }
        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 楼主 */
        if (input('isgroup') == 'true')
        {
            $where[] = ['level', '=', 0];
        }
        $list = \think\Db::name('tree_user')->where($where)->page($page, $limit)->order('create_time desc')->select();
//        print_r($list);die();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $room_number = \think\Db::name('tree')->where('id', $item['tree_id'])->value('room_number');
            $list[$key]['room_number'] = $room_number;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

        }
        $total = \think\Db::name('tree_user')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 财富树流水 */
    public function logListAction()
    {
        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('number'))
        {
            $tree_id = \think\Db::name('tree')->where('room_number', input('number'))->value('id');
            $where[] = ['tree_id', '=', $tree_id ? $tree_id : ''];
        }
        /** 用户 */
        if (input('name'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('name'))->value('uid');
            $where[] = ['uid', '=', $uid ?  $uid : ''];

        }
        /** 用户 */
        if (input('fromname'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('fromname'))->value('uid');
            $where[] = ['from_uid', '=', $uid ? $uid : ''];
        }

        $list = \think\Db::name('tree_log')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            if ($item['from_uid'] != 0)
            {
                $fusername = \think\Db::name('user')->where('uid', $item['from_uid'])->value('nickname');
                $list[$key]['fromusername'] = $fusername;

            } else
            {
                $list[$key]['fromusername'] = '本人';
            }

            $tree_info = \think\Db::name('tree')->where('id', $item['tree_id'])->field('room_number,currency_id')->find();
            $list[$key]['room_number'] = $tree_info['room_number'];
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $currency = \think\Db::name('currency')->where('id', $tree_info['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;

        }
        $total = \think\Db::name('tree_log')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }



}
