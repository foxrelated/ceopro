<?php

class Ynadvsearch_View_Helper_Number extends Zend_View_Helper_Abstract {
	public function number($value) {
		return Zend_Locale_Format::toNumber($value);
	}
}
?>