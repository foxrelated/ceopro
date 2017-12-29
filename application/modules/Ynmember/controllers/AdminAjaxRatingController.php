<?php

class Ynmember_AdminAjaxRatingController extends Core_Controller_Action_Admin
{
	
	public function addRatingFieldAction() 
	{
         $this->_helper->layout->disableLayout();
         $this->_helper->viewRenderer->setNoRender(true);
         $value = $this->_getParam('value');
         $type = Engine_Api::_() -> getItemTable('ynmember_ratingtype') -> createRow();
		 $type-> title = $value;
	     $type->save();
		 echo Zend_Json::encode(array('id' => $type -> getIdentity(), 'title' => $value));
    }
	
	public function deleteRatingFieldAction() 
	{
         $this->_helper->layout->disableLayout();
         $this->_helper->viewRenderer->setNoRender(true);
         $id = $this->_getParam('id');
         $type = Engine_Api::_() -> getItem('ynmember_ratingtype', $id);
	     $type->delete();
		 $tableRating = Engine_Api::_() -> getItemTable('ynmember_rating');
		 $ratings = $tableRating -> fetchAll($tableRating -> select() -> where('rating_type = ?', $id));
		 foreach($ratings as $item)
		 {
		 	$item -> delete();
		 }
		 echo Zend_Json::encode(array('result' => 'done'));
    }
	
	public function editRatingFieldAction() 
	{
         $this->_helper->layout->disableLayout();
         $this->_helper->viewRenderer->setNoRender(true);
         $id = $this->_getParam('id');
		 $title = $this->_getParam('title');
         $type = Engine_Api::_() -> getItem('ynmember_ratingtype', $id);
		 $type -> title = $title;
	     $type->save();
    }
}
