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
    public function getList($name = '', $uid = '')
    {
        $where = [];
        if (!empty($name)) {

            $where[] = ['name', 'like', $name.'%'];
        }
        $list = $this->where($where)->field('id,name,tag,icon,state')->order('order_id, id')->select();
        foreach ($list as $key => $item)
        {
            $list[$key]['icon'] = tomedia($item['icon']);
            if (!empty($uid))
            {
                $total = \think\Db::name('wallet')->where(['uid' => $uid, 'currency_id' => $item['id']])->value('total');     $list[$key]['balance'] = $total;

            } else
            {
                $list[$key]['balance'] = 0;
            }
        }
        return  $list;
    }

    /** 获取第一个币种 */
    public function getFirst()
    {
        $currency = $this->where([])->field('id,name,tag,icon')->order('order_id, id')->find();
        $currency['icon'] = tomedia($currency['icon']);
        return $currency;
    }

    /** 获取某币信息 */
    public function getCurrencyById($id)
    {
        $currency = $this->where(['id' => $id])->field('id,name,tag,icon')->find();
        $currency['icon'] = tomedia($currency['icon']);
        return $currency;
    }

    /** 获取币种标识 */
    public function getTage($id)
    {
        return $this->where('id', $id)->value('tag');
    }

    /** 获取币种名称 */
    public function getName($id)
    {
        return $this->where('id', $id)->value('name');
    }

    /** 获取币种名称 */
    static function getCurrencyName($id)
    {
        return self::where('id', $id)->value('name');
    }

    /** 币种的提现信息 */
    static public function cashInfo($id)
    {
        $cash_info = self::where('id', $id)->field('name,icon,tag,cash_service_ratio,cash_service_max,cash_min,cash_max,cash_service_max,cash_review')->find();
        $cash_info['icon'] = tomedia($cash_info['icon']);
        return $cash_info;
    }
}
