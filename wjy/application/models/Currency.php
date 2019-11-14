<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

class CurrencyModel extends Model
{
    /**
     * 查询列表
     * return  array
     */
    public function getList($name = '')
    {
        $where = [];
        if (!empty($name)) {

            $where[] = ['name', 'like', $name.'%'];
        }
        return $this->where($where)->field('id,name,tag')->order('order_id, id')->select();
    }


}