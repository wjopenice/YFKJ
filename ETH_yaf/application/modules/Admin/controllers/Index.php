<?php
class IndexController extends Yaf\Controller_Abstract {
    public function indexAction() {//默认Action
        $this->getView()->assign("content", "Hello31 World");
    }

    public function testAction() {//默认Action
        $this->getView()->assign("content", "Hello21 World");
    }
}
?>



