<?php
namespace Api\controller;
use Admin\model\User_model;
use \ext\Controller;
use \ext\CodeConfig;
use \Website\model\Shop_user;
class Payeth extends Controller{


    public function curl_post($url, $data_string)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data_string)
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        return $result;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    protected function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    protected function createLinkstringUrlencode($para)
    {
        $arg = "";
        foreach ($para as $k=>$v){
            $arg .= $k . "=" . urlencode($v) . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, strlen($arg) - 1);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }

    public function callback(){
        if(empty($_POST)){ exit; }
        $db = new User_model();
        if($_POST['direction'] == 'in'){
            $data = json_encode($_POST,320);
            file_put_contents(APP_PATH."/log/log1.txt","充值成功:".$data."\r\n",FILE_APPEND);
            echo json_encode(['code'=>0]);
            $type = "";
            if(strpos($_POST['coin_type'], 'DYX') !== false){
                $type = "DYX";
            }else if(strpos($_POST['coin_type'], 'USDT') !== false){
                $type = "USDT";
            }else if(strpos($_POST['coin_type'], 'ETH') !== false){
                $type = "ETH";
            }
            $res['id'] = $_POST['blockNumber'];
            $res['cointype'] = $type;
            $res['hash'] = $_POST['hash'];
            $res['address'] = $_POST['address'];
            $res['num'] = $_POST['num'];
            $res['create_time'] = $_POST['createtime'];
            $res['status'] = 1;
            $res['m_id'] = 0;
            $db->action($db->insertSql("recharge",$res));
            //广播商户
            $this->televise($data);
            exit;
        }
        if($_POST['direction'] == 'out'){
            $data = json_encode($_POST,320);
            file_put_contents(APP_PATH."/log/log2.txt","提现成功:".$data."\r\n",FILE_APPEND);
            echo json_encode(['code'=>0]);
            $res['send_time'] = $_POST['createtime'];
            $res['status'] = 1;
            $res['txid'] = $_POST['txid'];
            $db->action($db->updateSql("withdraw",$res,"uniquekey = '{$_POST['uniquekey']}'"));
            //查询是否为商户提现
            $result = $db->field("*")->table("w_withdraw")->where("uniquekey = '{$_POST['uniquekey']}'")->find();
            if(!empty($result['order_on'])){
                $_POST['serialNumber'] = $result['order_on'];
                $data2 = json_encode($_POST,320);
            }else{
                $data2 = $data;
            }
            //广播商户
            $this->televise($data2);
            exit;
        }
        
//充值
//  $data1 =' 充值成功:{"address":"0x7ee430a530d598d40868311bd6e62433a5963975","num":"0.00100000","createtime":"1572921422","blockNumber":"8871036","hash":"0x3c39897f2bee05e403dea392ccb27c1449febdf2afa043906347cf5d42a87c83","coin_type":"ETH_ETH_0x7ee430a530d598d40868311bd6e62433a5963975","direction":"in"}';
//提现
//  $data1 = '提现成功:{"serialNumber":"2019102910eDu189","uniquekey":"2019102910eDu189","coinname":"usdt","createtime":"1572958148","num":"0.00100000","txid":"0x73efbaf56b8d3d17390506f756cbcd13541a2482afa8a330dfa16706ddef3c6f","address":"0x37f30e211e1b8e02fd8c70f05476d36ee443a358","coin_type":"ETH_USDT_0x7ee430a530d598d40868311bd6e62433a5963975","direction":"out"}';
        


    }

    public function test(){
        $data = '{"address":"0x7ee430a530d598d40868311bd6e62433a5963975","num":"3.00000000","createtime":"1572956394","blockNumber":"8877504","hash":"0xbb9353592cde728cc2ebee29d138335439535af6ccfc42c57628a7134015c50d","coin_type":"ETH_USDT_0x7ee430a530d598d40868311bd6e62433a5963975","direction":"in"}';
        $this->televise($data);
        $data1 = '{"serialNumber":"2019102910eDu189","uniquekey":"2019102910eDu189","coinname":"dyx","createtime":"1572954927","num":"0.00100000","txid":"0x8f97c91fc440fbdd051f8077fcde7611e2ce9aeb9ded74d3b44886f5186eb174","address":"0x37f30e211e1b8e02fd8c70f05476d36ee443a358","coin_type":"ETH_DYX_0x7ee430a530d598d40868311bd6e62433a5963975","direction":"out"}';
        $this->televise($data1);
        exit;
    }

    public function televise($data){
        $url = "http://shuidihuan.name/notify/callback";
        $result = $this->curl_post($url,$data);
        file_put_contents(APP_PATH."/log/a.txt","广播成功：".$data."===>".$result."\r\n",FILE_APPEND);
    }
}

