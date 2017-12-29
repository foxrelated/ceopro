<?php
class Ynmultilisting_Form_Admin_Faqs_Edit extends Ynmultilisting_Form_Admin_Faqs_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit FAQ');
        $this->submit_btn->setLabel('Edit FAQ');
    }
}