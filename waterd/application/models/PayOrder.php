<?php
/**
 * 用户模型
 *
 * 写法请参考 ThinkPHP 5.1的数据模型
 */



class PayOrderModel extends Model
{
    /**
     * 创建订单
     * return  array
     */
    public function createOrder($uid, $currency, $money, $type)
    {

        $order_no = $this->create_order_no();
        if (is_error($order_no))
        {
            return $order_no;
        }

        /** 生成订单 改变金额 */
        $pay_order['order_no'] = $order_no;
        $pay_order['uid'] = $uid;
        $pay_order['currency_id'] = $currency;
        $pay_order['money'] = $money;
        $pay_order['state'] = 1;
        $pay_order['type'] = $type;
        $pay_order['pay_time'] = time();
        $pay_order['create_time'] = time();

        $res = $this->insert($pay_order);
        if (empty($res))
        {
            logs('获取订单号失败', '', 'orderno');
            return error('create order fail');
        }

        return ['order_no' => $order_no];

    }

    /** 获取订单号 */
    public function create_order_no($i = 0)
    {

          if ($i > 50)
          {
              logs('获取订单号失败', '', 'orderno');
              return error('订单号获取失败');
          }

          $order_no = create_order_no();
          $res = $this->where(['order_no' => $order_no])->find();
          if (empty($res))
          {
              return $order_no;
          }

          return $this->create_order_no($i + 1);
    }


}