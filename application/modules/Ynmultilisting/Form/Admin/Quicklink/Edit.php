<?php
class Ynmultilisting_Form_Admin_Quicklink_Edit extends Ynmultilisting_Form_Admin_Quicklink_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Quick Link');
        $this->submit_btn->setLabel('Edit Quick Link');
    }
}