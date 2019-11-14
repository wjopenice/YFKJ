<?php
/**
 * Date: 2018\4\1 0001 1:32
 */

namespace validate;
class Redgroup extends \Validate
{
    //验证规则
    protected $rule = [
        'name' => 'require',
        'currency_id' => 'require',
        'money' => 'require|number',
        'count' => 'require',
        'send_rule' => 'require',
        'redgroup_id' => 'require',
        'content' => 'require|max:100',
    ];

    //验证消息
    protected $message = [
        'name.require' => 'group name can not be empty',
        'currency_id.max' => 'currency can not be empty',
        'money.require' => 'money can not be empty',
        'money.number' => 'money must is number',
        'count.require' => 'count can not be empty',
        'send_rule.require' => 'send_rule can not be empty',
        'redgroup_id.require' => 'redgroup can not be empty',
        'content.require' => 'content can not be empty',
        'content.max' => 'content cannot exceed 100 characters ',
    ];

    //验证场景
    protected $scene = [
        //新增场景
        'create' => ['name','currency_id','money','count','send_rule'],
        //编辑时场景
        'comment' =>['redgroup_id','content']
    ];

}