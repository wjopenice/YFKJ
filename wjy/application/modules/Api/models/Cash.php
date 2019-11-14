<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

class CashModel extends Model
{

    const CASHSTATE = [
        1 => "待审核",
        2 => "待入账",
        3 => "提币失败",
        4 => "提币成功"
    ];

    static public function cashLog($where)
    {
        $page = $where['page'];

        $list = self::where(['uid' => $where['uid'], 'currency_id' => $where['currency_id']])->field('id,currency_id,order_no,money,total,state,create_time')->page($page,10)->order('create_time desc')->select();
        foreach ($list as $key => $item)
        {
            /** 获取币种名称 */
            $currency_name = \think\Db::name('currency')->where('id', $item['currency_id'])->value('name');
            $list[$key]['currency'] = $currency_name;
            $list[$key]['state'] = self::CASHSTATE[$item['state']];
        }

        return $list;
    }
}
