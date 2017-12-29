<?php 

class Ynadvsearch_View_Helper_AddThis extends Zend_View_Helper_Abstract{
	public function addThis(){
		$str = '		
		<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
		<a class="addthis_button_preferred_1"></a>
		<a class="addthis_button_preferred_2"></a>
		<a class="addthis_button_preferred_3"></a>
		<a class="addthis_button_preferred_4"></a>
		<a class="addthis_button_compact"></a>
		<a class="addthis_counter addthis_bubble_style"></a>
		<a class="groupbuy_addthis_button_google_plusone"></a>
		</div>
		<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e36581a18c65bea"></script>
		';
		return $str;
	}
} 