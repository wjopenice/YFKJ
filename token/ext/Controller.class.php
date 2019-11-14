<?php
namespace ext;
abstract class Controller{
    public function success($data = '操作成功',$url='index01.php'){
        echo "<script>alert('".$data."');window.location.href='".$url."';</script>";
    }
    public function error($data = '操作失败'){
        echo "<script>alert('".$data."');window.history.back();</script>";
    }
    public function DataType($data=null){

        if(is_string($data) || is_float($data) || is_int($data)){
            echo $data;
        }
        if(is_array($data) || is_object($data)){
            echo "<pre>";
            print_r($data);
            echo "</pre>";
        }
        if(is_bool($data) || is_null($data) || is_resource($data)){
            var_dump($data);
        }
        exit;
    }
    /**
     * Curl get请求方法
     */
    public function curlGet($url)
    {
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url);//请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  //是否需要手动拿到数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //启动SSL协议==>https协议
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //启动SSL协议==>https协议

        if ( ! curl_exec($ch)) //执行url地址
        {
            $data = '';
        }
        else
        {
            $data = curl_multi_getcontent($ch); //获取数据
        }
        curl_close($ch);  //关闭

        return $data;
    }

    //提交POST数据
    public function curlPost($url, $postData)
    {
        $ch = curl_init();//初始化
        curl_setopt($ch, CURLOPT_URL, $url); //请求地址
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //是否需要手动拿到数据
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //启动SSL协议==>https协议
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //启动SSL协议==>https协议
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //过期时间
        curl_setopt($ch, CURLOPT_POST, 1);  //启动POST请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);  //POST请求参数

        if ( ! curl_exec($ch)) //执行
        {
            $data = '';
        }
        else
        {
            $data = curl_multi_getcontent($ch); //获取数据
        }
        curl_close($ch); //关闭

        return $data;
    }


    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $type AJAX返回数据格式
     * @return void
     */
    public function ajaxReturn($data,$type='JSON') {
        switch (strtoupper($type)){
            case 'JSON' :
                // 返回JSON数据格式到客户端 包含状态信息
                header('Content-Type:application/json; charset=utf-8');
                exit(json_encode($data));
            case 'XML'  :
                // 返回xml格式数据
                header('Content-Type:text/xml; charset=utf-8');
                exit($this->xml_encode($data));
            case 'EVAL' :
                // 返回可执行的js脚本
                header('Content-Type:text/html; charset=utf-8');
                exit($data);
        }
    }
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root='openice', $item='item', $attr='', $id='id', $encoding='utf-8') {

        if(is_array($attr)){
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr   = trim($attr);
        $attr   = empty($attr) ? '' : " {$attr}";
        $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml   .= "<{$root}{$attr}>";
        $xml   .= $this->data_to_xml($data, $item, $id);
        $xml   .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed  $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id   数字索引key转换为的属性名
     * @return string
     */
    public function data_to_xml($data, $item='item', $id='id') {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if(is_numeric($key)){
                $id && $attr = " {$id}=\"{$key}\"";
                $key  = $item;
            }
            $xml    .=  "<{$key}{$attr}>";
            $xml    .=  (is_array($val) || is_object($val)) ? $this->data_to_xml($val, $item, $id) : $val;
            $xml    .=  "</{$key}>";
        }
        return $xml;
    }



    public function __set($name, $value)
    {
        echo "没有".$name."变量,不可以赋值为".$value."<hr>";
    }
    public function __get($name)
    {
        echo "没有".$name."变量"."<hr>";
    }
    public function __isset($name)
    {
        echo "没有".$name."变量不用检查了"."<hr>";
    }
    public function __unset($name)
    {
        echo "没有".$name."变量不用删除了"."<hr>";
    }
    public function __call($name, $arguments)
    {
        echo "没有".$name."方法"."<hr>";
    }
    public static function __callStatic($name, $arguments)
    {
        echo "没有".$name."静态方法"."<hr>";
    }
    //    public function __clone()
    //    {
    //        echo "你在克隆我的代码<hr>";
    //    }
    public function __toString()
    {
        return "你在打印我的类";
    }
    //防止克隆
    private function __clone()
    {
        echo "你在克隆我的代码<hr>";
    }

     public function is_Get(){
        if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['REQUEST_METHOD'] == "GET"){
            return true;
        }else{
            return false;
        }
     }
     public function is_Post(){
         if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['REQUEST_METHOD'] == "POST"){
             return true;
         }else{
             return false;
         }
     }
     public function is_Ajax(){
         if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"){
             return true;
         }else{
             return false;
         }
     }

}