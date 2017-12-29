<?php
class Ynforum_Form_Announcement_Edit extends Ynforum_Form_Announcement_Create
{
  public function init()
  {
  	 parent::init();
   	 $this->setTitle('Edit Announcement');    
     $this-> submit -> setLabel('Edit Announcement');  
  }
}