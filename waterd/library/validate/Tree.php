<?php
/**
 * Date: 2018\4\1 0001 1:32
 */

namespace validate;
class Tree extends \Validate
{
    //验证规则
    protected $rule = [
        'currency_id' => 'require',
        'name' => 'require',
        'level' => 'require',
        'limit' => 'require',
        'money' => 'require',
        'growth_ratio' => 'require',
        'tolevel' => 'require',
        'id' => 'require',
        'tree_id' => 'require',
        'invite_code' => 'require',
        'page' => 'require|number',
    ];

    //验证消息
    protected $message = [
        'currency_id.require' => '币种不能为空',
        'level.require' => '层级不能为空',
        'name.require' => '名称不能为空',
        'limit.require' => '推广限制不能为空',
        'money.require' => '第一层金额不能为空',
        'growth_ratio.require' => '增长比率不能为空',
        'id.require' => '财富树ID不能为空',
        'invite_code.require' => '邀请码不能为空',
    ];

    //验证场景
    protected $scene = [
        //新增场景
        'add' => ['currency_id', 'name', 'level', 'limit', 'money', 'growth_ratio'],
        'upgrade' => ['tolevel',  'tree_id'],
        'join' => ['id'],
        'logmore' => ['id',  'page'],
    ];

}
