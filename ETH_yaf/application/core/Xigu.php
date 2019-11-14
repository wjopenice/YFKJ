<?php
namespace app\core;
use \Exception;
use AliyunMNS\Client;
use AliyunMNS\Topic;
use AliyunMNS\Constants;
use AliyunMNS\Model\MailAttributes;
use AliyunMNS\Model\SmsAttributes;
use AliyunMNS\Model\BatchSmsAttributes;
use AliyunMNS\Model\MessageAttributes;
use AliyunMNS\Exception\MnsException;
use AliyunMNS\Requests\PublishMessageRequest;
class Xigu  {
    /**
     * API请求地址
     */
    const BaseUrl = "http://yun.vlcms.com/index.php";
    /**
     *
     * 用户账号ID。由32个英文字母和阿拉伯数字组成的开发者账号唯一标识符。
     */
    private $accountid;
    /**
     *
     * 时间戳
     */
    private $timestamp;
    /**
     * @param $options 数组参数必填
     * $options = array(
     *
     * )
     * @throws Exception
     */
    public function  __construct($accountid)
    {
        if (!empty($accountid)) {
            //$this->accountid = isset($accountid) ? $accountid : '';
            $this->accountid = "MDAwMDAwMDAwMK62sG1_enZnf7HJmLHc";
            $this->timestamp = date("YmdHis") + 7200;
        } else {
            throw new Exception("非法参数");
        }
    }
    /**
     * @return string
     * 包头验证信息,使用Base64编码（账户Id:时间戳）
     */
    private function getAuthorization()
    {
        $data = $this->accountid.":".$this->timestamp;
        return trim(base64_encode($data));
    }
    /**
     * @return string
     * 验证参数,URL后必须带有sig参数，sig= MD5（账户Id +  时间戳，共32位）(注:转成大写)
     */
    private function getSigParameter()
    {
        $sig = $this->accountid.$this->timestamp;
        return strtoupper(md5($sig));
    }
    /**
     * @param $url
     * @param string $type
     * @return mixed|string
     */
    private function getResult($url, $body = null, $type = 'json',$method)
    {
        $data = $this->connection($url,$body,$type,$method);
        if (isset($data) && !empty($data)) {
            $result = $data;
        } else {
            $result = '没有返回数据';
        }
        return $result;
    }
    /**
     * @param $url
     * @param $type
     * @param $body  post数据
     * @param $method post或get
     * @return mixed|string
     */
    private function connection($url, $body, $type,$method)
    {
        if ($type == 'json') {
            $mine = 'application/json';
        } else {
            $mine = 'application/xml';
        }
        if (function_exists("curl_init")) {
            $header = array(
                'Accept:' . $mine,
                'Content-Type:' . $mine . ';charset=utf-8',
                'Authorization:' . $this->getAuthorization(),
            );
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            if($method == 'post'){
                curl_setopt($ch,CURLOPT_POST,1);
                curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = array();
            $opts['http'] = array();
            $headers = array(
                "method" => strtoupper($method),
            );
            $headers[]= 'Accept:'.$mine;
            $headers['header'] = array();
            $headers['header'][] = "Authorization: ".$this->getAuthorization();
            $headers['header'][]= 'Content-Type:'.$mine.';charset=utf-8';
            if(!empty($body)) {
                $headers['header'][]= 'Content-Length:'.strlen($body);
                $headers['content']= $body;
            }
            $opts['http'] = $headers;
            $result = file_get_contents($url, false, stream_context_create($opts));
        }
        return $result;
    }
    /**
     * @param $appId
     * @param $verifyCode
     * @param $to
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function voiceCode($appId,$verifyCode,$to,$type = 'json'){
        $url = self::BaseUrl .  '?s=/SendCode/voice/accountid/' . $this->accountid . '/sig/' . $this->getSigParameter();
        if($type == 'json'){
            $body_json = array('voiceCode'=>array(
                'appId'=>$appId,
                'verifyCode'=>$verifyCode,
                'to'=>$to
            ));
            $body = json_encode($body_json);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <voiceCode>
                            <verifyCode>'.$verifyCode.'</clientNumber>
                            <to>'.$to.'</charge>
                            <appId>'.$appId.'</appId>
                        </voiceCode>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
    /**
     * @param $appId
     * @param $to
     * @param $templateId
     * @param null $param
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function sendSM($appId,$to,$templateId,$param=null,$type = 'json'){
        $url = self::BaseUrl.'?s=/SendCode/send/accountid/'.$this->accountid .'/sig/'.$this->getSigParameter();
        if($type == 'json'){
            $url="http://api.vlpush.com/sms/send_sms";
            $smsconfig = [
                'smtp' => 'MDAwMDAwMDAwMK62sG1_enZnf7HJmLHc',
                'smtp_account' => 'MDAwMDAwMDAwMLq5qLB_oIJnf4u73bDc',
                'smtp_password' => '273',
                'smtp_port' => '25615'
            ];
            $post_data['appid']= $smsconfig['smtp'];
            $post_data['apikey']= $smsconfig['smtp_account'];
            $post_data['templateid']=$templateId;
            $post_data['phone']=$to;
            $post_data['param']=$param;
            $o = "";
            foreach ( $post_data as $k => $v )
            {
                $o.= "$k=" . urlencode( $v ). "&" ;
            }
            $post_data = substr($o,0,-1);
            $res = $this->request_post($url, $post_data);
            $code=json_decode($res,true);
            if($code['code']==200){
                $code['send_status']='000000';
            }else{
                $code['send_status']='111111';
            }
            $code['smsId'] = $this->getSigParameter();
            return json_encode($code);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <sendSM>
                            <templateId>'.$templateId.'</templateId>
                            <to>'.$to.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$appId.'</appId>
                        </sendSM>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
    /**
     * @param $appId
     * @param $to
     * @param $templateId
     * @param null $param
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function sendSmsNotice($appId,$to,$templateId,$param=null,$type = 'json'){
        $url = self::BaseUrl .  '?s=/SendCode/send/accountid/' . $this->accountid . '/sig/' . $this->getSigParameter();
        if($type == 'json'){
            $url="http://api.vlpush.com/sms/send_sms";
            $smsconfig = [
                'smtp' => 'MDAwMDAwMDAwMK62sG1_enZnf7HJmLHc',
                'smtp_account' => 'MDAwMDAwMDAwMLq5qLB_oIJnf4u73bDc',
                'smtp_password' => '273',
                'smtp_port' => '25615',
            ];
            $post_data['appid']= $smsconfig['smtp'];
            $post_data['apikey']= $smsconfig['smtp_account'];
            $post_data['templateid']=$templateId;
            $post_data['phone']=$to;
            $post_data['param']=$param;
            $o = "";
            foreach ( $post_data as $k => $v )
            {
                $o.= "$k=" . urlencode( $v ). "&" ;
            }
            $post_data = substr($o,0,-1);
            $res = $this->request_post($url, $post_data);
            $code=json_decode($res,true);
            if($code['code']==200){
                $code['send_status']='000000';
            }else{
                $code['send_status']='111111';
            }
            return json_encode($code);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <sendSM>
                            <templateId>'.$templateId.'</templateId>
                            <to>'.$to.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$appId.'</appId>
                        </sendSM>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
    /**
     * @param $appId
     * @param $to
     * @param $templateId
     * @param null $param
     * @param string $type
     * @return mixed|string
     * @throws Exception
     */
    public function sendSmsQun($appId,$to,$templateId,$param=null,$type = 'json'){
        $url = self::BaseUrl .  '?s=/SendCode/send/accountid/' . $this->accountid . '/sig/' . $this->getSigParameter();
        if($type == 'json'){
            $url="http://api.vlpush.com/sms/send_sms_batch";
            $smsconfig = [
                'smtp' => 'MDAwMDAwMDAwMK62sG1_enZnf7HJmLHc',
                'smtp_account' => 'MDAwMDAwMDAwMLq5qLB_oIJnf4u73bDc',
                'smtp_password' => '273',
                'smtp_port' => '25615',
            ];
            $post_data['appid']= $smsconfig['smtp'];
            $post_data['apikey']= $smsconfig['smtp_account'];
            $post_data['templateid']=$templateId;
            $post_data['phone']=$to;
            $post_data['param']=$param;
            $o = "";
            foreach ( $post_data as $k => $v )
            {
                $o.= "$k=" . urlencode( $v ). "&" ;
            }
            $post_data = substr($o,0,-1);
            $res = $this->request_post($url, $post_data);
            $code=json_decode($res,true);
            if($code['code']==200){
                $code['send_status']='000000';
            }else{
                $code['send_status']='111111';
            }
            return json_encode($code);
        }elseif($type == 'xml'){
            $body_xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <sendSM>
                            <templateId>'.$templateId.'</templateId>
                            <to>'.$to.'</to>
                            <param>'.$param.'</param>
                            <appId>'.$appId.'</appId>
                        </sendSM>';
            $body = trim($body_xml);
        }else {
            throw new Exception("只能json或xml，默认为json");
        }
        $data = $this->getResult($url, $body, $type,'post');
        return $data;
    }
    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    public function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        return $data;
    }
}