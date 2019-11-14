<?php
namespace app\core;
class Image
{
    public static $w;
    public static $h;
    public static function code($width,$height,$size=20,$x=20,$start=30,$y=30,$font="c:\\windows\\fonts\\msyhbd.ttc",$path = null){
        self::$w = $width;
        self::$h = $height;
        //创建画板
        //$pic = "timg.png";
        //$img =  imagecreatefrompng($pic);
        $img = imagecreate(self::$w,self::$h);
        //$img = imagecreatetruecolor(self::$w,self::$h);
        //添加画板颜色
        $bg = imagecolorallocate($img,255,255,255);
        //imagefill($img,0,0,$bg);
        $strColor = imagecolorallocate($img,rand(0,100),rand(0,100),rand(0,100));
        $pixelColor = imagecolorallocate($img,rand(0,100),rand(0,100),rand(0,100));
        $lineColor = imagecolorallocate($img,rand(0,100),rand(0,100),rand(0,100));
        //添加随机字符串
        $arr1 = range("a","z");
        shuffle($arr1);
        $arr2 = range("0","9");
        shuffle($arr2);
        $arr3 = range("A","Z");
        shuffle($arr3);
        $arr4 = range("0","9");
        shuffle($arr4);
        $newArr = array($arr1[0],$arr2[0],$arr3[0],$arr4[0]);
        shuffle($newArr);
        //$strData = implode($newArr);
        //随机字符串输出到画板
        $str = "";
        for($i=0;$i<count($newArr);$i++){
            $strData =  $newArr[$i];
            $str .= $strData;
            imagefttext($img,$size,rand(-45,45),$x,$y,$strColor,$font,$strData);
            $x += $start;
        }
//           //干扰点
//           for($j=0;$j<500;$j++){
//               imagesetpixel($img,rand(0,self::$w),rand(0,self::$h),$pixelColor);
//           }
//           //干扰线
//           for($j=0;$j<5;$j++){
//               imageline($img,rand(0,self::$w),rand(0,self::$h),rand(0,self::$w),rand(0,self::$h),$lineColor);
//           }
        //存数据
        $_SESSION['yzm'] = strtolower($str);
        //输出画板
        imagepng($img,$path);
        //销毁画板
        imagedestroy($img);
    }
    //图片压缩
    public static function zoom($picname,$size = 1,$path=null){
        $arr = getimagesize($picname);
        header("content-type:".$arr['mime']);
        //获取原资源
        $imgRes = "";
        switch ($arr['mime']){
            case "image/png": $imgRes = imagecreatefrompng($picname);break;
            case "image/gif": $imgRes = imagecreatefromgif($picname);break;
            case "image/jpeg":$imgRes = imagecreatefromjpeg($picname);break;
        }
        //目标资源
        $desRes = imagecreatetruecolor($arr[0]*$size,$arr[1]*$size);
        //压缩图片
        imagecopyresampled($desRes,$imgRes,0,0,0,0,$arr[0]*$size,$arr[1]*$size,$arr[0],$arr[1]);
        //输出图片
        switch ($arr['mime']){
            case "image/png": imagepng($desRes,$path);break;
            case "image/gif": imagegif($desRes,$path);break;
            case "image/jpeg":imagejpeg($desRes,$path);break;
        }
        //销毁图片
        imagedestroy($desRes);
        imagedestroy($imgRes);
    }
    //文字水印
    public static function logoS($picname,$strData = "",$path=null){
        header("conent-type:image/png");
        $arr = getimagesize($picname);
        $res = imagecreatefrompng($picname);
        $color = imagecolorallocatealpha($res,rand(0,255),rand(0,255),rand(0,255),rand(0,127));
        imagefttext($res,25,0,$arr[0]*(3/4),$arr[1]-20,$color,"c:\\windows\\fonts\\msyhbd.ttc",$strData);
        imagepng($res,$path);
        imagedestroy($res);
    }
    //图片水印
    public static function logoP($picname,$srcname,$path=null){
        header("conent-type:image/png");
        $arr1 = getimagesize($picname);
        $res1 = imagecreatefrompng($picname);
        $arr2 = getimagesize($srcname);
        $res2 = imagecreatefrompng($srcname);
        imagecopymerge($res1,$res2,$arr1[0]/2-$arr2[0]/2,$arr1[1]/2-$arr2[1]/2,0,0,$arr2[0],$arr2[1],rand(100,100));
        if($path == null){
            imagepng($res1);
        }else{
            imagepng($res1,$path);
        }
        imagedestroy($res1);
        imagedestroy($res2);
    }
}