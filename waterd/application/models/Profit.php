<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */

class ProfitModel extends Model
{

    public function addProfit($rel_id, $money, $currency_id, $scene)
    {
        $addProfit['rel_id'] = $rel_id;
        $addProfit['money'] = $money;
        $addProfit['currency_id'] = $currency_id;
        $addProfit['scene'] = $scene;
        $addProfit['create_time'] = time();
        return $this->save($addProfit);
    }
}
