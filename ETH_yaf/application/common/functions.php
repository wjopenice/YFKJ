<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/20
 * Time: 13:21
 */
function isMobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
function isWechat() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('MicroMessenger');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}
function isiphone() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('iphone','ipod');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/***
 * @3位内数字转中文数字
 * @param $data int
 * @return string
 */
function conversion($data){
    if($data == 0){
        return "X";
    }else{
        $strData = (string)$data;
        $mun = ['0','1','2','3','4','5','6','7','8','9'];
        $zhcn = ['十','一','二','三','四','五','六','七','八','九'];
        if(strlen($strData) == 3 && $strData[1] <> 0 && $strData[2] <> 0){
            return str_replace($mun, $zhcn, $strData[0]) . "百" . str_replace($mun, $zhcn, $strData[1]) . "十" . str_replace($mun, $zhcn, $strData[2]);
        }elseif(strlen($strData) == 3 && $strData[1] == 0 && $strData[2] <> 0){
            return str_replace($mun, $zhcn, $strData[0]) . "百零" . str_replace($mun, $zhcn, $strData[2]);
        }elseif(strlen($strData) == 3  && $strData[1] <> 0 && $strData[2] == 0){
            return str_replace($mun, $zhcn, $strData[0]) . "百" . str_replace($mun, $zhcn, $strData[1]) . "十";
        }elseif(strlen($strData) == 3 && $strData[1] == 0 && $strData[2] == 0){
            return str_replace($mun, $zhcn, $strData[0]) . "百";
        }elseif(strlen($strData) == 2 && $data <> 10 && $strData[1] <> 0){
            return str_replace($mun,$zhcn,$strData[0])."十".str_replace($mun,$zhcn,$strData[1]);
        }elseif(strlen($strData) == 2 && $data <> 10 && $strData[1] == 0){
            return str_replace($mun,$zhcn,$strData[0])."十";
        }elseif($strData == 10){
            return "十";
        }elseif(strlen($strData) == 1 &&  $data > 0){
            return str_replace($mun,$zhcn,$strData);
        }
    }
}


function alertText($data,$url) {
    echo "<script>
    var divNode = document.createElement('div');
    divNode.setAttribute('id','msg');
    divNode.style.position = 'fixed';
    divNode.style.top = '50%';
    divNode.style.width = '400px';
    divNode.style.left = '50%';
    divNode.style.marginLeft = '-220px';
    divNode.style.height = '30px';
    divNode.style.lineHeight = '30px';
    divNode.style.marginTop = '-35px';
    var pNode = document.createElement('p');
    pNode.style.background = 'rgba(0,0,0,0.6)';
    pNode.style.width = '300px';
    pNode.style.color = '#fff';
    pNode.style.textAlign = 'center';
    pNode.style.padding = '20px';
    pNode.style.margin = '0 auto';
    pNode.style.fontSize = '16px';
    pNode.style.borderRadius = '4px';
    pNode.innerText = '".$data."';
    divNode.appendChild(pNode);
    var htmlNode = document.documentElement;
    htmlNode.style.background = 'rgba(0,0,0,0)';
    htmlNode.appendChild(divNode);
    var t = setTimeout(next,2000);
    function next(){
        htmlNode.removeChild(divNode);
        window.location.href='".$url."';
    }
    </script>";
}
function success($msg,$url){
    echo "<script>alert('".$msg."');window.location.href='".$url."';</script>";
}
function error($msg){
    echo "<script>alert('".$msg."');window.history.back();</script>";
}
function statusUrl($bool,string $success_msg, string $success_url,string $error_msg){
    if($bool){
        success($success_msg,$success_url);
    }else{
        error($error_msg);
    }
}
function server($data = null){
    if(is_null($data)){
        return $_SERVER;
    }else{
        $key = strtoupper($data);
        return $_SERVER[$key];
    }
}
function request($data = null){
    if(is_null($data)){
        return $_REQUEST;
    }else{
        return $_REQUEST[$data];
    }
}
function post($data = null){
    if(is_null($data)){
        return $_POST;
    }else{
        return $_POST[$data];
    }
}
function get($data = null){
    if(is_null($data)){
        return $_GET;
    }else{
        return $_GET[$data];
    }
}
function files($data = null){
    if(is_null($data)){
        return $_FILES;
    }else{
        return $_FILES[$data];
    }
}
function load_view($filename=null){
    include_once APP_PATH."/application/views/{$filename}.phtml";
}
function p($data){
    if(is_bool($data) || is_null($data)){
        var_dump($data);
    }
    if(is_array($data) || is_object($data) || is_resource($data)){
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }
    if(is_int($data) || is_string($data) || is_float($data)){
        echo $data;
    }
    exit;
}
function dump($data){
    switch (true){
        case is_string($data) || is_int($data) || is_float($data): echo $data ; break; exit;
        case is_array($data) || is_object($data) : echo "<pre>";print_r($data);echo "</pre>"; break;exit;
        case is_bool($data) || is_null($data) : var_dump($data) ; break;exit;
        default: var_dump($data) ;break;exit;
    }
    exit;
}
//写一个数据类型的检测
function dataType($data){
    if(is_string($data)){
        echo "这是字符串";
    }else if(is_int($data)){
        echo "这是整型";
    }else if(is_object($data)){
        echo "这是对象";
    }else if(is_float($data)){
        echo "这是浮点类型";
    }else if(is_bool($data)){
        echo "这是布尔类型";
    }else if(is_null($data)){
        echo "这是NULL";
    }else if(is_array($data)){
        echo "这是数组";
    }else{
        echo "这是资源类型";
    }
    exit;
}
//删除文件
function file_delete($filename=null,$mktime=null){
    if(file_exists($filename)){
        $t1 = fileatime($filename);//获取上一次访问时间
        $t2 = time(); //获取本次访问时间
        $t3 = $t2-$t1;//时间差
        $t4 = $mktime;// 过期时间秒
        if($t3 >= $t4){//过期
            unlink($filename); //删除文件
        }
    }else{
        die($filename." not file code：404");
    }
}
//强化readfile函数安全
function Exreadfile($fileName = null,$tags=true){
    if($tags){
        ob_start();//打开输出缓冲
        readfile($fileName);  //写数据到输出缓冲
        $strData = ob_get_flush();//提前输出缓冲数据和关闭
        ob_clean();//清空输出缓冲里面的内容
        return htmlspecialchars($strData);
    }else{
        ob_start();//打开输出缓冲
        readfile($fileName);  //写数据到输出缓冲
        $strData = ob_get_flush();//提前输出缓冲数据和关闭
        ob_clean();//清空输出缓冲里面的内容
        return $strData;
    }
}
//点击率
function file_addclick($fileName = null){
    $L = filesize($fileName)+1;
    $fileRes1 = fopen($fileName,"r");
    $str = fread($fileRes1,$L);
    $str+=1;
    $fileRes2 = fopen($fileName,"w+");
    fwrite($fileRes2,$str);
    rewind($fileRes2);
    return fread($fileRes2,$L);
}
//PHP生成日历
function datetime(){
    $y = isset($_GET['y'])?$_GET['y']:date("Y"); //当前年
    $m = isset($_GET['m'])?$_GET['m']:date("m"); //当前月
    $d = isset($_GET['d'])?$_GET['d']:date("d"); //当前日
    $days = date("t",mktime(0,0,0,$m,$d,$y));//获取当月的天数
    $statweek = date("w",mktime(0,0,0,$m,1,$y));//获取当月的第一天是星期几
    $str = "";
    $str .="<table border='1' align='center'>";
    $str .="<caption>当前为{$y}年{$m}月</caption>";
    $str .="<tr><th>星期天</th><th>星期一</th><th>星期二</th><th>星期三</th><th>星期四</th><th>星期五</th><th>星期六</th></tr>";
    $str .="<tr>";
    for($i=0;$i<$statweek;$i++){
        $str .="<td>&nbsp;</td>";
    }
    for($j=1;$j<=$days;$j++){
        $i++;
        if($j == $d){
            $str .="<td bgcolor='cyan'>{$j}</td>";
        }else{
            $str .="<td>{$j}</td>";
        }
        if($i % 7 == 0){
            $str .="</tr><tr>";
        }
    }
    while($i % 7 !== 0){
        $str .="<td>&nbsp;</td>";
        $i++;
    }
    $str .="</tr>";
    $str .="</table>";
    return $str;
}
//转静态化
function static_page($url,$descname){
    set_time_limit(0);
    //实现HTML静态化
    $data = base64_encode(file_get_contents($url));
    file_put_contents($descname,$data);  //W+
    $strData = base64_decode(file_get_contents($descname));
    return $strData;
}
//文件下载
function apkdownload($file){
    if(file_exists($file)){
        header("Content-type:application/vnd.android.package-archive");
        $filename = basename($file);
        header("Content-Disposition:attachment;filename = ".$filename);
        header("Accept-ranges:bytes");
        header("Accept-length:".filesize($file));
        readfile($file);
    }else{
        echo "<script>alert('文件不存在')</script>";
    }
}
function StrX_shuffle($str=null){
    $a1 = range("a","z");
    shuffle($a1);
    $a2 = range("a","z");
    shuffle($a2);
    $a3 = range("a","z");
    shuffle($a3);
    $a4 = range("a","z");
    shuffle($a4);
    $a5 = range("a","z");
    shuffle($a5);
    $a6 = range("a","z");
    shuffle($a6);
    $strData = $str.$a1[0].$a2[0].$a3[0].$a4[0].$a5[0].$a6[0];
    return $strData;
}
//随机字符串
function Mer_shuffle($string,$maxlen = 20){
    $int_arr = range(0,9);
    $str_arr = range("a","z");
    $str1 = mb_splitchar($string);
    $new_arr = array_merge($int_arr,$str_arr);
    shuffle($new_arr);
    $strData = $str1.date("YmdHi",time()).implode($new_arr);
    $new_str = substr($strData,0,$maxlen);
    //file_put_contents("./c.html",$new_str);
    return $new_str;
}
//订单生成
function build_order_no(){
    return date('Ymd').substr(implode(array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
//获取单个汉字拼音首字母。注意:此处不要纠结。汉字拼音是没有以U和V开头的
function getfirstchar($s0){
    $fchar = ord($s0{0});
    if($fchar >= ord("A") and $fchar <= ord("z") )return strtoupper($s0{0});
    $s1 = iconv("UTF-8","gb2312", $s0);
    $s2 = iconv("gb2312","UTF-8", $s1);
    if($s2 == $s0){$s = $s1;}else{$s = $s0;}
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if($asc >= -20319 and $asc <= -20284) return "A";
    if($asc >= -20283 and $asc <= -19776) return "B";
    if($asc >= -19775 and $asc <= -19219) return "C";
    if($asc >= -19218 and $asc <= -18711) return "D";
    if($asc >= -18710 and $asc <= -18527) return "E";
    if($asc >= -18526 and $asc <= -18240) return "F";
    if($asc >= -18239 and $asc <= -17923) return "G";
    if($asc >= -17922 and $asc <= -17418) return "H";
    if($asc >= -17922 and $asc <= -17418) return "I";
    if($asc >= -17417 and $asc <= -16475) return "J";
    if($asc >= -16474 and $asc <= -16213) return "K";
    if($asc >= -16212 and $asc <= -15641) return "L";
    if($asc >= -15640 and $asc <= -15166) return "M";
    if($asc >= -15165 and $asc <= -14923) return "N";
    if($asc >= -14922 and $asc <= -14915) return "O";
    if($asc >= -14914 and $asc <= -14631) return "P";
    if($asc >= -14630 and $asc <= -14150) return "Q";
    if($asc >= -14149 and $asc <= -14091) return "R";
    if($asc >= -14090 and $asc <= -13319) return "S";
    if($asc >= -13318 and $asc <= -12839) return "T";
    if($asc >= -12838 and $asc <= -12557) return "W";
    if($asc >= -12556 and $asc <= -11848) return "X";
    if($asc >= -11847 and $asc <= -11056) return "Y";
    if($asc >= -11055 and $asc <= -10247) return "Z";
    return NULL;
}
//获取整条字符串汉字拼音首字母
function mb_splitchar($str){
    $strX = "";
    for($i=0;$i<mb_strlen($str);$i++){
        $strData = mb_substr($str,$i,1);
        if(ord($strData) > 160){
            $strX .= getfirstchar($strData);
        }else{
            $strX .= $strData;
        }
    }
    return $strX;
}
//获取ip
function getIp() {
    $arr_ip_header = array(
        'HTTP_CDN_SRC_IP',
        'HTTP_PROXY_CLIENT_IP',
        'HTTP_WL_PROXY_CLIENT_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
    );
    $client_ip = 'unknown';
    foreach ($arr_ip_header as $key)
    {
        if (!empty($_SERVER[$key]) && strtolower($_SERVER[$key]) != 'unknown')
        {
            $client_ip = $_SERVER[$key];
            break;
        }
    }
    return $client_ip;
}
//获取具体错误信息
function getE($num="") {
    switch($num) {
        case -1:  $error = '用户名长度必须在6-30个字符以内！'; break;
        case -2:  $error = '用户名被禁止注册！'; break;
        case -3:  $error = '用户名被占用！'; break;
        case -4:  $error = '密码长度不合法'; break;
        case -5:  $error = '邮箱格式不正确！'; break;
        case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
        case -7:  $error = '邮箱被禁止注册！'; break;
        case -8:  $error = '邮箱被占用！'; break;
        case -9:  $error = '手机格式不正确！'; break;
        case -10: $error = '手机被禁止注册！'; break;
        case -11: $error = '手机号被占用！'; break;
        case -12: $error = '手机号码必须由11位数字组成';break;
        case -13: $error = '手机号已被其他账号绑定';break;
        case -20: $error = '请填写正确的姓名';break;
        case -21: $error = '用户名必须由字母、数字或下划线组成,以字母开头';break;
        case -22: $error = '用户名必须由6~30位数字、字母或下划线组成';break;
        case -31: $error = '密码错误';break;
        case -32: $error = '用户不存在或被禁用';break;
        case -41: $error = '身份证无效';break;
        default:  $error = '未知错误';
    }
    return $error;
}
//获取CURD请求类型
function Get_method(){
    $method = $_SERVER['REQUEST_METHOD'];
    return $method;
}
//获取CURD请求数据
function Resp_curl(){
    parse_str(file_get_contents('php://input'), $data);
    $data = array_merge($_GET, $_POST, $data);
    return $data;
}
//建立CURD请求模式
function Rest_curl($url,$type='GET',$data="",$bool=false,array $headers=["content-type: application/x-www-form-urlencoded;charset=UTF-8"]){
    //post 新增  get查询  put修改  delete删除
    $curl= curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL,$url);
    if($bool == true){
        curl_setopt($curl, CURLOPT_HEADER, $bool);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    switch ($type){
        case "GET":break;
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "DELETE":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        default:break;
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
    if(curl_exec($curl) === false){
        return "error code:".curl_getinfo($curl, CURLINFO_HTTP_CODE).',error message:'.curl_error($curl);
    }
    $strData = curl_exec($curl);
    curl_close($curl);
    return $strData;
}

function curl_get($url){
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

function get_new_user($user){
    $db = new dbModel();
    $result = $db->field("new_status")->table("y_user")->where(" username = '{$user}'")->find();
    return $result['new_status'];
}

function get_user_scale($u_l,$g_l){
    $total = 1;
    $db = new dbModel();
    $bulidranking = $db->field("*")->table("y_bulidranking")->find();
    $user_scale = $total*($bulidranking['personal']/100);
    $group_scale =  $total*($bulidranking['team']/100);
    //求个人排名总和
    $user_num = $db->zscount("user","id","total","new_status = 1");
    $user_leval_total = sums($user_num);
    //求团队排名总和
    //$group_num = $db->zscount("group","id");
    $group_num = $user_num;
    $group_leval_total = sums($group_num);
    //求个人排名占比总和
    $user_leval_scale_total = 0;
    for($i=1;$i<=$user_num;$i++){
        $user_leval_scale_total += (floor($user_leval_total/$i*100)/100);    //sprintf("%0.3f",$user_leval_total/$i)
    }
    //求团队排名占比总和
    $group_leval_scale_total = 0;
    for($j=1;$j<=$user_num;$j++){
        $group_leval_scale_total += (floor($group_leval_total/$j*100)/100);  //bcdiv($group_leval_total,$j,3)
    }
    //个人30% 币
    $user_leval = $user_leval_total/$u_l/$user_leval_scale_total * $user_scale;
    //团队30%
    $group_leval = $group_leval_total/$g_l/$group_leval_scale_total * $group_scale;
    $total2 = ($user_leval+$group_leval)/$total*100;
    if($total2 == 0){
        $total2 = "暂无数据";
    }else{
        $total2 = (floor($total2*100)/100)."%";
    }
    return $total2;
}

function user_group_level_name($user_id){
    $db = new dbModel();
    $sql = "SELECT guid, sum(token_available_balance) as money_total FROM `y_group` AS a LEFT JOIN `y_user` AS b
ON  FIND_IN_SET(b.id, a.users) GROUP BY guid ORDER BY money_total DESC";
    $topRes = $db->action($sql);
    $data = [];
    foreach($topRes as $k1=>$v1){
        if($user_id == $v1['guid']){
             $data['level'] = "第".($k1 + 1)."位";
             $data['money_total'] = $v1['money_total']."DYX";
             break;
        }
    }
    if(!empty($data)){
        return $data['money_total']."，".$data['level'];
    }else{
        $data['level'] = "暂无排名";
        $data['money_total'] = "暂无数据";
        return $data['money_total']."，".$data['level'];
    }
}

function get_user_order($userid){
    $db = new dbModel();
    $result = $db->field("*")
        ->table("y_user")
        ->order("token_available_balance desc")
        ->select();
    $data = [];
    foreach($result as $k=>$v){
        if($v['id'] == $userid){
            $data['order'] = $k+1;
            $data['dyx'] = $v['token_available_balance'];
            $data['tel'] = $v['tel'];
            break;
        }
    }
    if(!empty($data)){
        return $data;
    }else{
        $data['order'] = "暂无数据";
        $data['dyx'] = "暂无数据";
        $data['tel'] = "暂无数据";
        return $data;
    }
}

function sums($n)
{
    $x = (int)$n;
    return (1+$x)*$x/2;
}

function user_discount_price($dis_id)
{
    $db = new dbModel();
    $result = $db->field("(sum(dis_price)+sum(link_price)) as total")->table("y_user_discount")->where(" dis_id = '{$dis_id}'")->find();
    return $result['total'];
}

function user_id_name($user){
    $db = new dbModel();
    $result = $db->field("username")->table("y_user")->where(" id = '{$user}'")->find();
    return $result['username'];
}

function user_id($user){
    $db = new dbModel();
    $result = $db->field("id")->table("y_user")->where(" username = '{$user}'")->find();
    return $result['id'];
}

//判断时间在今天、昨天、前天、几天前几点
function get_time($targetTime)
{
    // 今天最大时间
    $todayLast   = strtotime(date('Y-m-d 23:59:59'));
    $agoTimeTrue = time() - $targetTime;
    $agoTime     = $todayLast - $targetTime;
    $agoDay      = floor($agoTime / 86400);
    if ($agoTimeTrue < 60) {
        $result = '刚刚';
    } elseif ($agoTimeTrue < 3600) {
        $result = (ceil($agoTimeTrue / 60)) . '分钟前';
    } elseif ($agoTimeTrue < 3600 * 12) {
        $result = (ceil($agoTimeTrue / 3600)) . '小时前';
    } elseif ($agoDay == 0) {
        $result = '今天 ' ;
    } elseif ($agoDay == 1) {
        $result = '昨天 ' ;
    } elseif ($agoDay == 2) {
        $result = '前天 ';
    } elseif ($agoDay > 2 && $agoDay < 16) {
        $result = $agoDay . '天前 ';
    } else {
        $format = date('Y') != date('Y', $targetTime) ? "Y-m-d H:i" : "m-d H:i";
        $result = date($format, $targetTime);
    }
    return $result;
}
/*
if(!function_exists('showPage')){
    function showPage($page,$url){
        $start = ($page->current>2)?($page->current-2):1;
        $end = ($page->current+1)>$page->total_pages?($page->total_pages-1):$page->current+1;
        $strpage = "";
        $strpage .= "<div id='pages'>";
        $strpage .= \Phalcon\Tag::linkTo($url."?page=".$page->first, "首页");
        if($page->current == $page->first){
            $strpage .= \Phalcon\Tag::linkTo([$url.'?page='.$page->before, '<i class="layui-icon"></i>','class'=>'layui-disabled']);
        }else{
            $strpage .= \Phalcon\Tag::linkTo($url.'?page='.$page->before, '<i class="layui-icon"></i>');
        }
        for ($i=$start;$i<=$end;$i++){
            if($i == $page->current){
                $strpage .= \Phalcon\Tag::linkTo([$url.'?page='.$i, $i,'class' => 'active']);
            }else{
                $strpage .= \Phalcon\Tag::linkTo($url.'?page='.$i, $i);
            }
        }
        if($page->current == $page->total_pages){
            $strpage .= \Phalcon\Tag::linkTo([$url.'?page='.$page->next, '<i class="layui-icon"></i>','class'=>'layui-disabled']);
        }else{
            $strpage .= \Phalcon\Tag::linkTo($url.'?page='.$page->next, '<i class="layui-icon"></i>');
        }
        $strpage .= \Phalcon\Tag::linkTo($url.'?page='.$page->last, '末页');
        $strpage .= "共".$page->total_items."条总共".$page->current." / ".$page->total_pages;
        $strpage .= "</div>";
        return $strpage;
    }
}
*/
//数据库备份
//function mysqldump($tableName){
//    $username = Yii::$app->params['user'];//你的MYSQL用户名
//    $password = Yii::$app->params['pass'];;//密码
//    $hostname = Yii::$app->params['host'];;//MYSQL服务器地址
//    $dbname   = Yii::$app->params['dbname'];;//数据库名
//    $port   = Yii::$app->params['port'];;//数据库端口
//    $dumpfname = $tableName . "_" . date("YmdHi").".sql";
//    $path = dirname(dirname(__FILE__))."/data/".$dumpfname;
//    $command = "mysqldump -P{$port} -h{$hostname} -u{$username} -p{$password} {$dbname} {$tableName} > {$path}";
//    system($command,$retval);
//    exit;
//}
//
////数据库备份
//function mysqldumpall($tableName){
//    $username = Yii::$app->params['user'];//你的MYSQL用户名
//    $password = Yii::$app->params['pass'];;//密码
//    $hostname = Yii::$app->params['host'];;//MYSQL服务器地址
//    $dbname   = Yii::$app->params['dbname'];;//数据库名
//    $port   = Yii::$app->params['port'];;//数据库端口
//    $dumpfname =  "localhost_" . date("YmdHi").".sql";
//    $path = dirname(dirname(__FILE__))."/data/".$dumpfname;
//    $command = "mysqldump -P{$port} -h{$hostname} -u{$username} -p{$password} {$dbname} {$tableName} > {$path}";
//    system($command,$retval);
//    $zipfname = "localhost_" . date("YmdHi").".zip";
//    $zippath = dirname(dirname(__FILE__))."/data/".$zipfname;
//    $zip = new \ZipArchive();
//    if($zip->open($zippath,ZIPARCHIVE::CREATE))
//    {
//        $zip->addFile($path,$path);
//        $zip->close();
//    }
//    if (file_exists($zippath)) {
//        header('Content-Description: File Transfer');
//        header('Content-Type: application/octet-stream');
//        header('Content-Disposition: attachment; filename='.basename($zippath));
//        flush();
//        readfile($zippath);
//        exit;
//    }
//}