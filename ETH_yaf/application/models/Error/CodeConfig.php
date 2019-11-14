<?php
namespace Error;
/**
 * 错误码设置类
 */
class CodeConfigModel {
    /**
     * 获取错误码配置
     */
    public static function getCodeConfig() {
        return array(
            //100xxx：用户
            "100110" => "测试输出错误",
            "111111"=>'用户名不存在',
        );
    }
}