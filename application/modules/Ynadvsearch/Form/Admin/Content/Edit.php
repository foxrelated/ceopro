<?php
class Ynadvsearch_Form_Admin_Content_Edit extends Ynadvsearch_Form_Admin_Content_Create
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Content Type');
    $this->submit->setLabel('Edit');
  }
}