<?php

class Advalbum_Widget_AlbumsMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$params = $this -> _getAllParams();
	$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
  }

}