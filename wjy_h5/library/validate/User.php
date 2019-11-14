<?php
/**
 * Date: 2018\4\1 0001 1:32
 */

namespace validate;
class User extends \Validate
{
    //验证规则
    protected $rule = [
        'username' => 'require|max:25',
        'password' => 'require',
        'nickname' => 'require',
    ];

    //验证消息
    protected $message = [
        'username.require' => 'username can not be empty',
        'username.max' => 'username max Less than 25',
        'password.require' => 'password can not be empty',
        'nickname.require' => 'nickname can not be empty',
    ];

    //验证场景
    protected $scene = [
        //新增场景
        'add' => ['username','password'],
        //编辑时场景
        'edit' =>['nickname']
    ];

}