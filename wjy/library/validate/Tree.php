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
        'currency_id.require' => 'currency can not be empty',
        'level.require' => 'level can not be empty',
        'name.require' => 'name can not be empty',
        'limit.require' => 'limit can not be empty',
        'money.require' => 'money can not be empty',
        'growth_ratio.require' => 'ratio can not be empty',
        'tolevel.require' => 'upgrade level can not be empty',
        'tolevel.require' => 'id level can not be empty',
        'invite_code.require' => 'invite code level can not be empty',
        'page.require' => 'page can not be empty',
        'page.number' => 'page must is number',
    ];

    //验证场景
    protected $scene = [
        //新增场景
        'add' => ['currency_id', 'name', 'level', 'limit', 'money', 'growth_ratio'],
        'upgrade' => ['tolevel',  'tree_id'],
        'join' => ['id',  'invite_code'],
        'logmore' => ['id',  'page'],
    ];

}
