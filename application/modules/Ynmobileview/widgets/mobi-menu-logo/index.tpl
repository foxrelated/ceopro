<span id="ynmobile_logo">
<?php $title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
	$title = $this -> string() -> truncate($title, 13);
	$logo  = $this->logo;
	$route = $this->viewer()->getIdentity()
				 ? array('route'=>'user_general', 'action'=>'home')
				 : array('route'=>'default');
	
	echo ($logo)
		 ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title)))
		 : $this->htmlLink($route, $title);
 ?>
</span>