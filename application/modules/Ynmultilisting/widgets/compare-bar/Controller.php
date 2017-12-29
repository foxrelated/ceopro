<?php

class Ynmultilisting_Widget_CompareBarController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $this->view->categories = $categories = Engine_Api::_()->ynmultilisting()->getAvailableCategories();
        if (!count($categories)) {
            $this->setNoRender();
        }
    }

}
