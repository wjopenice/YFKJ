<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/9/11
 * Time: 20:28
 */

class Upload
{

    static function uploadImage($uid, $type)
    {

        if ((($_FILES["file"]["type"] == "image/gif")
                || ($_FILES["file"]["type"] == "image/jpg")
                || ($_FILES["file"]["type"] == "image/png")
                || ($_FILES["file"]["type"] == "image/jpeg")))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                return error("Return Code: " . $_FILES["file"]["error"]);
            }
            else
            {
                $filename = $uid.'_'.$type.'.png';
                $filepath =  "/avatar/" . $filename;
                if ( move_uploaded_file($_FILES["file"]["tmp_name"],
                    APP_ATTACHMENT.$filepath))
                {
                    return $filepath;
                } else
                {
                    return error("upload fail");
                }

            }
        }
        else
        {
            return error("Invalid file");
        }
    }

    static function uploadImg()
    {

        if ((($_FILES["file"]["type"] == "image/gif")
            || ($_FILES["file"]["type"] == "image/jpg")
            || ($_FILES["file"]["type"] == "image/png")
            || ($_FILES["file"]["type"] == "image/jpeg")))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                return error("Return Code: " . $_FILES["file"]["error"]);
            }
            else
            {

                $filename = time().'.png';
                $filepath =  "/upload/".$filename;
                mkdirs(APP_ATTACHMENT."/upload/");
                if ( move_uploaded_file($_FILES["file"]["tmp_name"],
                    APP_ATTACHMENT.$filepath))
                {
                    return $filepath;
                } else
                {
                    return error("upload fail");
                }

            }
        }
        else
        {
            return error("Invalid file");
        }
    }

}
