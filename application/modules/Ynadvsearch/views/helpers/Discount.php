<?php

class Ynadvsearch_View_Helper_Discount extends Zend_View_Helper_Abstract {
	public function discount($value) {
		return Zend_Locale_Format::toNumber($value) . '%';
	}
}
?>