<?php
class Advgroup_WikiController extends Core_Controller_Action_Standard
{
  public function init()
  {
    
  }

  public function listAction(){
   //Checking Ynvideo Plugin - View privacy
   $wiki_enable = Engine_Api::_()->advgroup()->checkYouNetPlugin('ynwiki');
   if(!$wiki_enable){
     return $this->_helper->requireSubject->forward();
   }
	 if (!Engine_Api::_() -> core() -> hasSubject()) {
			return $this -> _helper -> requireSubject -> forward();
   }
	
   //Get viewer, group, search form
   $viewer = Engine_Api::_() -> user() -> getViewer();
   $this -> view -> group = $group = Engine_Api::_() -> core() -> getSubject();
	 $this -> view -> form = $form = new Advgroup_Form_Wiki_Search;
  
   if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'view') -> isValid()) {
			return;
	 }
   // Check create wiki authorization
   $canCreate = $group -> authorization() -> isAllowed($viewer, 'wiki');
   $levelCreate = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynwiki_page', $viewer, 'create');

   if ($canCreate && $levelCreate) {
     $this -> view -> canCreate = true;
   } else {
     $this -> view -> canCreate = false;
   }

   $params = $this->_getAllParams();
   $params['parent_type']= $group->getType();
   $params['parent_id']= $group->getIdentity();
   $form->populate($params);
   $this->view->formValues = $form->getValues();
   $this->view->pages = $paginator = Engine_Api::_()->ynwiki()->getPagesPaginator($params);

   $items_per_page = Engine_Api::_()->getApi('settings', 'core')->ynwiki_page;
   $paginator->setItemCountPerPage($items_per_page);
   if(key_exists('page', $params))
   {
     $paginator->setCurrentPageNumber($params['page']);
   }
  }
}
?>
