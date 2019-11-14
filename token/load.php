<?php
spl_autoload_register(function($className = null){
    //检测这个文件是否存在
    $fileName = $className.".class.php";
    $result = str_replace("\\","/",$fileName);
    if(file_exists($result)){
        include_once $result;
    }else{
        die($result." not file code：404");
    }
});


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

function MY_readfile($fileName = null,$tags=true){
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
};

function dump($data){
    switch (true){
        case is_string($data) || is_int($data) || is_float($data): echo $data ; break;
        case is_array($data) || is_object($data) : echo "<pre>";print_r($data);echo "</pre>"; break;
        case is_bool($data) || is_null($data) : var_dump($data) ; break;
        default: var_dump($data) ;break;
    }
}

function my_copy($url,$descname){
    set_time_limit(0);
    //实现HTML静态化
    $data = base64_encode(file_get_contents($url));
    file_put_contents($descname,$data);  //W+
    $strData = base64_decode(file_get_contents($descname));
    return $strData;
}

function download($file){
    $mime = mime_content_type($file);
    $size = filesize($file);
    // 下载文件mime类型
        header('Content-type: '.$mime);
    // 下载文件保存
        header("Content-Disposition: attachment; filename=".$file);
    //下载文件大小显示
        header("Content-Length:".$size);
    //读取下载文件
        readfile($file);
}

function view($mod,$dir,$file,array &$data=null){
    include_once $mod."/view/".$dir."/".$file.".php";
}

function viewS($mod,$dir,$file,array $data=null){
    if(!empty($data)){
        extract($data);
        include_once $mod."/view/".$dir."/".$file.".php";
    }else{
        include_once $mod."/view/".$dir."/".$file.".php";
    }
}

