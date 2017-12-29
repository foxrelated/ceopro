<?php
class Ynmember_Form_Admin_Relationship_Edit extends Ynmember_Form_Admin_Relationship_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Status');
    $this->submit->setLabel('Edit');
  }
}