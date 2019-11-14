<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class RedController extends AdminApi {

    /** 红包群列表 */
    public function redgroupListAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('number'))
        {
            $where[] = ['room_number', '=', input('number')];
        }
        /** 群名称 */
        if (input('name'))
        {
            $where[] = ['name', '=', input('name')];
        }
        /** 创建人 */
        if (input('username'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('username'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }

        $list = \think\Db::name('redgroup')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $total = \think\Db::name('redgroup')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 红包列表 */
    public function redpacketListAction()
    {
        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];

        /** 房间号 */
        if (input('number'))
        {
            $redgroup_id = \think\Db::name('redgroup')->where('room_number', input('number'))->value('id');
            $where[] = ['redgroup_id', '=', $redgroup_id ? $redgroup_id : ''];
        }
        /** 用户 */
        if (input('username'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('username'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }

        $list = \think\Db::name('redpacket')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $username = \think\Db::name('user')->where('uid', $item['uid'])->value('nickname');
            $list[$key]['username'] = $username;
            $redgroup = \think\Db::name('redgroup')->where('id', $item['redgroup_id'])->field('id,currency_id,room_number')->find();
            $currency = \think\Db::name('currency')->where('id', $redgroup['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['room_number'] = $redgroup['room_number'];
            /** 获取剩余级可用数量 */
            $surplus = \think\Db::name('redpacket_log')->where(['redpacket_id' => $item['id'], 'uid' => 0])->count();
            $list[$key]['surplus'] = $surplus;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);

        }
        $total = \think\Db::name('redpacket')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 红包明细 */
    public function redpacketLogListAction()
    {
        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];
        $id = input('id');
        if (empty($id))
        {
            $this->error('缺少关键信息');
        }

        $where[] = ['redpacket_id', '=', $id];
        $where[] = ['uid', '>', 0];
        $list = \think\Db::name('redpacket_log')->where($where)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $userInfo = \think\Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $list[$key]['username'] = $userInfo['nickname'];
            $list[$key]['avatar'] = tomedia($userInfo['avatar']);
            $list[$key]['update_time'] = date('Y-m-d H:i:s', $item['update_time']);
        }

        $data['list'] = $list;
        $this->success($data);
    }

    /** 红包流水 */
    public function redLogListAction()
    {

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;
        $where = [];
        /** 房间号 */
        if (input('number'))
        {
            $redgroup_id = \think\Db::name('redgroup')->where('room_number', input('number'))->value('id');
//            $redpackts = \think\Db::name('redpacket')->where('redgroup_id', $redgroup_id)->field('id')->select();
//            $redpackt_ids = [];
//            foreach ($redpackts as $item)
//            {
//                $redpackt_ids[] = $item['id'];
//            }

            $where[] = ['redgroup_id', '=', $redgroup_id];
        }
        /** 用户 */
        if (input('username'))
        {
            $uid = \think\Db::name('user')->where('nickname', input('username'))->value('uid');
            $where[] = ['uid', '=', $uid ? $uid : ''];
        }
        /** 红包ID */
        if (input('redpacketId'))
        {
            $where[] = ['redpacket_id', '=', input('redpacketId')];
        }
        $list = \think\Db::name('red_log')->where($where)->page($page, $limit)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            $userInfo = \think\Db::name('user')->where('uid', $item['uid'])->field('nickname,avatar')->find();
            $list[$key]['username'] = $userInfo['nickname'];
            $currency = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency;
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $redgroup_id = \think\Db::name('redpacket')->where('id', $item['redpacket_id'])->value('redgroup_id');
            $room_number = \think\Db::name('redgroup')->where('id', $redgroup_id)->value('room_number');
            $list[$key]['room_number'] = $room_number;
        }

        $total = \think\Db::name('red_log')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

}
