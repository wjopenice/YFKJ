<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class AdminController extends AdminApi {

    /** 列表 */
    public function listAction(){

       $page = input('page');
       $page = $page ? $page : 1;
       $limit = input('limit') ? input('limit') :　10;

       $list = \think\Db::name('admin_user')->where([])->field('id,username,realname,avatar,create_time,status')->page($page, $limit)->order('id desc')->select();
       foreach ($list as $key => $item)
       {
           $list[$key]['avatar'] = tomedia($item['avatar']);
           $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
       }
       $total = \think\Db::name('admin_user')->where([])->count();
       $data['list'] = $list;
       $data['total'] = $total;
       $this->success($data);
    }

    /** 编辑更新 */
    public function createAction()
    {
        $data['username'] = input('username');
        $data['realname'] = input('realname');
        $password = input('password');
        if ($password)
        {
            $data['password'] = md5($password);
        }
        $id = input('id');
        if ($id)
        {

            $data['update_time'] = time();
            $data['id'] = $id;
            $result = \think\Db::name('admin_user')->update($data);

        } else
        {
            $data['create_time'] = time();
            $data['update_time'] = time();
            $result = \think\Db::name('admin_user')->insert($data);
        }

        $result ? $this->success() : $this->error();
    }

    /** 删除 */
    public function deleteAction()
    {
        $id = input('id');
        if (empty($id))
        {
            $this->error('请选择删除对象');
        }

        if (\think\Db::name('admin_user')->where('id', $id)->delete())
        {
            $this->success();
        }
        $this->error();
    }
}
