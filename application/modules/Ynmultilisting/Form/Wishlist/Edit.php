<?php
class Ynmultilisting_Form_Wishlist_Edit extends Ynmultilisting_Form_Wishlist_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Wish List');
        $this->submit_btn->setLabel('Edit');
    }
}