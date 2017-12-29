<?php

class Ynwiki_Form_Faqs_Admin_Edit extends Ynwiki_Form_Faqs_Admin_Create{
	
	public function init(){
		parent::init();
		$this -> setTitle('Edit FAQ') -> setDescription('');
	}
}
