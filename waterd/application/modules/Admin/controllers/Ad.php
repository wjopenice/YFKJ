<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class AdController extends AdminApi {

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

        $list = \think\Db::name('ad')->where($where)->field('id,order_id,type,pic,content,create_time')->page($page, $limit)->order('create_time asc')->select();
        foreach ($list as $key => $item)
        {
            $list[$key]['pic'] = tomedia($item['pic']);
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }
        $total = \think\Db::name('ad')->where($where)->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);
    }

    /** 编辑更新 */
    public function createAction()
    {
        $data = $_POST;

        if ($data['id'])
        {

            $data['update_time'] = time();
            $result = \think\Db::name('ad')->update($data);

        } else
        {
            $data['create_time'] = time();
            $data['update_time'] = time();
            $result = \think\Db::name('ad')->insert($data);
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

        if (\think\Db::name('ad')->where('id', $id)->delete())
        {
            $this->success();
        }
        $this->error();
    }
}
