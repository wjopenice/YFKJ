<?php
/**
 * 后台控制器首页
 * Date: 2018\2\20 0020 15:51
 */

include "AdminApi.php";

class UploadController extends AdminApi {

    /** 红包群列表 */
    public function indexAction(){


        if ((($_FILES["file"]["type"] == "image/gif")
            || ($_FILES["file"]["type"] == "image/jpg")
            || ($_FILES["file"]["type"] == "image/png")
            || ($_FILES["file"]["type"] == "image/jpeg")))
        {

            if ($_FILES["file"]["error"] > 0)
            {
                $this->error("Return Code: " . $_FILES["file"]["error"]);
            }
            else
            {

                $datepath = date("Y/m/d");
                $filename = time().'.png';
                $filepath =  "/images/" .$datepath.'/'. $filename;
                mkdirs(APP_ATTACHMENT."/images/".$datepath.'/');
                if (move_uploaded_file($_FILES["file"]["tmp_name"],
                    APP_ATTACHMENT.$filepath))
                {
                    /** 添加记录 */
                    $add_attachment['filename'] = $_FILES["file"]["name"];
                    $add_attachment['attachment'] = $filepath;
                    $add_attachment['type'] = 1;
                    $add_attachment['createtime'] = time();
                    \think\Db::name('attachment')->insert($add_attachment);
                   $this->success(['url' => tomedia($filepath)]);



                } else
                {
                    $this->error("upload fail");
                }

            }
        }
        else
        {
            $this->error("Invalid file");
        }
    }



}
