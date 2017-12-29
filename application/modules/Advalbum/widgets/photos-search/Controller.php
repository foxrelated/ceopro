<?php
class Advalbum_Widget_PhotosSearchController extends Engine_Content_Widget_Abstract
{

	public function indexAction()
	{
		$request = Zend_Controller_Front::getInstance() -> getRequest();
		$params = $this -> _getAllParams();
		$session = new Zend_Session_Namespace('mobile');
		if ($session -> mobile)
		{
			if ($params['nomobile'] == 1)
			{
				return $this -> setNoRender();
			}
		}
		$search_form = $this -> view -> search_form = new Advalbum_Form_Photo_Search();
		$p = Zend_Controller_Front::getInstance() -> getRequest() -> getParams();
		if ($p['color'] != "")
		{
			$this -> view -> color = $p['color'];
		}
		if (isset($p['search']))
			$search_form -> getElement('search') -> setValue($p['search']);
		if (isset($p['sort']))
			$search_form -> getElement('sort') -> setValue($p['sort']);
		if (isset($p['category_id']))
			$search_form -> getElement('category_id') -> setValue($p['category_id']);
		$search_form -> isValid($p);
		
		$colorTbl = Engine_Api::_() -> getDbTable("colors", "advalbum");
		$this -> view -> colors = $colors = $colorTbl -> fetchAll($colorTbl -> select());
	}

}
