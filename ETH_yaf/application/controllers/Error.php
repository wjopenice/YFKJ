<?php
class ErrorController extends Yaf\Controller_Abstract{
//    public function init() {
//        \Yaf\Dispatcher::getInstance()->disableView();
//    }
//    public function errorAction($exception) {
//        if ($exception->getCode() > 100000) {
//            //这里可以捕获到应用内抛出的异常
//            $code= $exception->getCode();
//            $codeConfig  = \Error\CodeConfigModel::getCodeConfig();
//            if (empty($codeConfig[$code])) {
//                throw new \Exception('错误码' . $code . '的相应提示信息没有设置');
//            }
//            $message = $codeConfig[$code];
//            echo $message;
//            echo $exception->getCode();
//            echo $exception->getMessage();
//            return;
//        }
//        switch ($exception->getCode()) {
//            case 404://404
//            case 515:
//            case 516:
//            case 517:
//                header(\Our\Common::getHttpStatusCode(404));//输出404
//                echo '404';
//                exit();
//                break;
//            default :
//                break;
//        }
//        throw $exception;
//    }
//    public function errorAction() {
//        $exception = $this->getRequest()->getException();
//        try {
//            throw $exception;
//        } catch (Yaf\Exception\LoadFailed $e) {
//            //加载失败
//            $this->getView()->assign("code", $exception->getCode());
//            $this->getView()->assign("message", $exception->getMessage());
//        } catch (Yaf\Exception $e) {
//            //其他错误
//            $this->getView()->assign("code", $exception->getCode());
//            $this->getView()->assign("message", $exception->getMessage());
//        }
//    }
    public function errorAction($exception)
    {
        switch ($exception->getCode()) {
            case YAF_ERR_LOADFAILD:;break;
            case YAF_ERR_LOADFAILD_MODULE:;break;
            case YAF_ERR_LOADFAILD_CONTROLLER:;break;
            case YAF_ERR_LOADFAILD_ACTION:;break;
            case CUSTOM_ERROR_CODE:;break;
        }
        $this->getView()->assign("code", $exception->getCode());
    }
}