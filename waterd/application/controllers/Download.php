<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

class DownloadController extends Controller
{

    public function indexAction()
    {

        /** 获取群组信息 */
        $this->getView('index');

    }


}
