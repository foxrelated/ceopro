<?php

class Ynwiki_Form_HelpPage_Admin_Edit extends Ynwiki_Form_HelpPage_Admin_Create{
	
	public function init(){
		parent::init();
		$this -> setTitle('Edit Help Page') -> setDescription('');
	}
}
