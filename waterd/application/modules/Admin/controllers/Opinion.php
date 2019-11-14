<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class OpinionController extends AdminApi {

    /** 列表 */
    public function listAction(){

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = input('limit') ? input('limit') :　10;

        $where = [];
        if (input('title'))
        {
            $where[] = ['title', 'like', input('title').'%'];
        }

        $list = \think\Db::name('opinion')->where($where)->page($page, $limit)->order('create_time asc')->select();
        foreach ($list as $key => $item)
        {
            if (empty($item['uid']))
            {
                $list[$key]['username'] = '无';

            } else
            {
                $username = \think\Db::name('user')->where('uid', $item['uid'])->value('username');
                $list[$key]['username'] = $username;
            }
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $total = \think\Db::name('opinion')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

}
