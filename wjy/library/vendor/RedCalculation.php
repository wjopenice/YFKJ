<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/22
 * Time: 19:29
 */
namespace vendor;

class RedCalculation
{

    static function makeEqual($total, $num)
    {

        $redData = array();
        if ($total <= 0 || $num <= 0)
        {
            return $redData;
        }
        $everymoney = ($total * 100 / $num) / 100;

        for ($i = 1; $i <= $num; $i++)
        {

            $redData[] = $everymoney;
        }

        return $redData;


    }

    static function makeRandom1($total, $num, $min = 0.01)
    {

        $redData = array();
        if ($total <= 0 || $num <= 0)
        {
            return $redData;
        }

        for ($i = 1; $i < $num; $i++)
        {
            //随机安全上限
            $safe_total = ($total - ($num - $i) * $min) / ($num - $i);
            $money = mt_rand($min * 100, $safe_total * 100) / 100;
            $total = $total - $money;
            $redData[] = $money;

        }

        $redData[] = $total;
        return $redData;
    }

    /**
     *
     * 随机红包
     * @param float $money 发放金额
     * @param number $num 红包个数
     * @return number[]
     */
    static function makeRandom2($money, $num)
    {
        $result = array();
        $index = 0;
        // 水平分割
        for ($i = 0; $i < $money * 100; $i++) {
            isset($result[$index]) ? $result[$index]++ : $result[$index] = 1;
            if ($index < $num - 1) {
                $index++;
            } else {
                $index = 0;
            }
        }
        // 随机分配金额
        for ($i = 0; $i < $num * 10; $i++) {
            $r1 = rand(0, $num - 1);
            $r2 = rand(0, $num - 1);
            $per = rand(15, 65) / 100;
            // 随机金额
            $mon =  $result[$r1] - floor($result[$r1] * $per);
            if ($result[$r1] - $mon > 0) {
                // 减去随机金额
                $result[$r1] = $result[$r1] - $mon;
                // 添加随机金额
                $result[$r2] = $result[$r2] + $mon;
            }
        }
        foreach ($result as $key => $item)
        {
            $result[$key] = $item / 100;
        }

        return $result;
    }

    /**
     *
     * 随机红包
     * @param float $total 发放金额
     * @param number $num 红包个数
     * @return number[]
     */
    static function makeRandom($total, $num)
    {
        /** 最小值 这里设置一个平均值的一半 */
        $min = bcdiv($total, $num) * 0.2;
        $overPlus = $total - $num * $min; // 剩余待发钱数
        $base = 0; // 总基数
        // 存放所有人数据
        $container = array();
        // 每个人保底
        for ($i = 0; $i < $num; $i++) {
            // 计算比重
            $weight = round(lcg_value() * 1000);
            $container[$i]['weight'] = $weight; // 权重
            $container[$i]['money'] = $min; // 最小值都塞进去
            $base += $weight; // 总基数
        }

        $len = $num - 1; // 下面要计算总人数-1的数据,
        for ($i = 0; $i < $len; $i++) {
            $money = floor($container[$i]['weight'] / $base * $overPlus * 100) / 100; // 向下取整,否则会超出
            $container[$i]['money'] += $money;
        }

        // 弹出最后一个元素
        array_pop($container);
        $result = array_column($container, 'money');
        $last_one = round($total - array_sum($result), 2);
        array_push($result, $last_one);
        return $result;
    }



}
