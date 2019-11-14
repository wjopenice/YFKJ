<?php
if(!function_exists("replace_rsastr_pub")){
    function replace_rsastr_pub($data){
        $newstr = str_replace("-----BEGIN PUBLIC KEY-----","-----BEGIN PUBLIC KEY-----<br>",$data);
        $locastr = str_replace("-----END PUBLIC KEY-----","<br>-----END PUBLIC KEY-----",$newstr);
        return $locastr;
    }
}

if(!function_exists("replace_rsastr_pri")){
    function replace_rsastr_pri($data){
        $newstr = str_replace("-----BEGIN PRIVATE KEY-----","-----BEGIN PRIVATE KEY-----<br>",$data);
        $locastr = str_replace("-----END PRIVATE KEY-----","<br>-----END PRIVATE KEY-----",$newstr);
        return $locastr;
    }
}
