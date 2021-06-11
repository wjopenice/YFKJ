<?php
namespace app\core;
class Ethapi{
    //代币的智能合约地址
    public $contractaddress = "0xe4412afb08242465eac96103";
    //ETH接口地址
    public $api = "https://api.etherscan.io/api?";
    //请求限制
    public $apikey = "XYT";
    //单用户信息查询
    public function first_user(string $user_addr,$tag = 'latest'){
         $url = $this->api."module=account&action=balance&address=".$user_addr."&tag=".$tag."&apikey=".$this->apikey;
         $result = $this->curl_get_eth($url);
         return $result;
    }
    //多用户信息查询
    public function list_user(array $user_addr,$tag = 'latest'){
        if(is_array($user_addr)){
            $user_addr_str = substr(implode(",",$user_addr),0,-1);
            $url = $this->api."module=account&action=balancemulti&address=".$user_addr_str."&tag=".$tag."&apikey=".$this->apikey;
            $result = $this->curl_get_eth($url);
            return $result;
        }else{
            return "request data is array and string";
        }
    }
    //获取”正常”交易记录
    public function pub_transaction_record($user_addr,$start=0,$end=1000,$page=1,$showpage=10,$sort='asc'){
        $url = $this->api."module=account&action=txlist&address=".$user_addr."&startblock=".$start."&endblock=".$end."&page=".$page."&offset=".$showpage."&sort=".$sort."&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
    }
    //获取”内部”交易记录
    public function pro_transaction_record($user_addr,$start=0,$end=1000,$page=1,$showpage=10,$sort='asc'){
        $url = $this->api."module=account&action=txlistinternal&address=".$user_addr."&startblock=".$start."&endblock=".$end."&page=".$page."&offset=".$showpage."&sort=".$sort."&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
    }
    //通过交易 hash 查看交易的详情
    public function hash_transaction_record($hash){
        $url = $this->api."module=account&action=txlistinternal&txhash=".$hash."&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
    }

    //获取 ERC20 代币交易事件记录
    public function get_erc20_event($user_addr,$showpage = 10){
        $url = $this->api."module=account&action=tokentx&contractaddress=".$this->contractaddress."&address=".$user_addr."&page=1&offset=".$showpage."&sort=asc&apikey=&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
    }

    //获取已开采的区块列表
    public function get_list_mined_blocks($user_addr,$showpage = 10){
        $url = $this->api."module=account&action=getminedblocks&address=".$user_addr."&blocktype=blocks&page=1&offset=".$showpage."&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
    }

    //获取智能合约接口
    public function aiapi(){
        $url = $this->api."module=account&action=getabi&address=".$this->contractaddress."&apikey=".$this->apikey;
        $result = $this->curl_get_eth($url);
        return $result;
        https://api.etherscan.io/api?module=contract&action=getabi&address=0xe4412afb082b51b185acf2b421842465eac96103&apikey=XYT
    }

    public function curl_get_eth($url){
        $curl= curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
        if(curl_exec($curl) === false){
            return "error code:".curl_getinfo($curl, CURLINFO_HTTP_CODE).',error message:'.curl_error($curl);
        }
        $strData = curl_exec($curl);
        curl_close($curl);
        $arrData = json_decode($strData);
        return $arrData;
    }


}
