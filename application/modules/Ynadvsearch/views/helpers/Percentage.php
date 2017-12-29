<?php

class Ynadvsearch_View_Helper_Percentage extends Zend_View_Helper_Abstract {
	public function percentage($value) {
		return Zend_Locale_Format::toNumber($value, array('precision'=>2,'locale'=>'en_US')) . '%';
	}
}
?>