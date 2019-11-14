<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

use think\Db;

class SettingController extends AdminApi {

    /** 获取 */
    public function getConfigAction(){

        $where = [];
        if (input('group'))
        {
            $where['group'] = input('group');
        }

        $data = \think\Db::name('config')->where($where)->select();
        $config = [];
        foreach ($data as $key => $item)
        {
            $config[$item['name']] = $item['value'];
        }

        $this->success($config);
    }

    /** 设置 */
    public function setConfigAction(){

       $data = $_POST;
       $group = input('group');

        Db::startTrans();
        try {
            foreach ($data as $key => $item)
            {
               \think\Db::name('config')->where(['type' => $group, 'name' => $key])->setField('value', $item);
            }
            Db::commit();
            $this->success();

        } catch (\Exception $e) {
            Db::rollback();
           $this->error();
        }


    }
}
