<?php

class Ynadvsearch_View_Helper_Price extends Zend_View_Helper_Abstract {
		
	/**
	 * @var Zend_Currency
	 */
	protected static $_currency;
	
	/**
	 * @return  Zend_Currency 
	 */
	public static function getCurrency(){
		if(null === self::$_currency){
			self::$_currency = new Zend_Currency('en_US');
		}
		return self::$_currency;
	}
	
	/**
	 * magic function call
	 * @param   decimal   $value
	 * @param   int       $precision
	 * @return  string    
	 * @throws  Zend_Currency_Exception   IF INVALID VALUE 
	 */
	public function price($value, $precision=2) {
		$string = self::getCurrency()->toCurrency($value);
		
		// allow trim
		//$string =  rtrim(rtrim(rtrim($string,'0'),','),'.');
		return $string;	
	}
}