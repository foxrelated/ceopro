<?php
class Ynmultilisting_Form_Admin_Module_Edit extends Ynmultilisting_Form_Admin_Module_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Module');
        $this->setDescription('Edit modules your users import listing from.');
        $this->submit_btn->setLabel('Edit Module');
    }
}