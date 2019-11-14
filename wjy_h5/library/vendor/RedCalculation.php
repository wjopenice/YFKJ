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

    static function makeRandom($total, $num, $min = 0.01)
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
}