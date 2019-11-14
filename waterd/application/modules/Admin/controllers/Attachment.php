<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

use think\Db;

include "Base.php";

class AttachmentController extends Base
{

    /** 列表 */
    public function listAction()
    {

        $page = input('page');
        $page = $page ? $page : 1;
        $limit = 16;

        $list = \think\Db::name('attachment')->where([])->page($page, $limit)->order('id desc')->select();
        foreach ($list as $key => $item)
        {
            $list[$key]['backpath'] = tomedia($item['attachment']);
            $list[$key]['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            list($width, $height) = getimagesize(APP_ATTACHMENT.$item['attachment']);
            $list[$key]['size']['width'] = $width;
            $list[$key]['size']['height'] = $height;
        }
        $total = \think\Db::name('attachment')->where([])->count();
        $data['list'] = $list;
        $data['total'] = $total;
        $this->success($data);


    }



}
