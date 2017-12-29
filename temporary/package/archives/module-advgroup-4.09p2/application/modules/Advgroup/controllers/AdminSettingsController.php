<?php
class Advgroup_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_settings');

    $this->view->form = $form = new Advgroup_Form_Admin_Global();

    if( $this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
      $form->addNotice('Your changes have been saved.');
    }
  }

  public function categoriesAction()
  {
    $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
                'application/modules/Advgroup/externals/scripts/collapsible.js');
    
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_categories');

    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'advgroup')->getParentCategories();
  }
  
  public function levelAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('advgroup_admin_main', array(), 'advgroup_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }
    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Advgroup_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    
    $form->populate($permissionsTable->getAllowed('group', $id, array_keys($form->getValues())));
    
    //Re-corect number field loading
    if($level->type != 'public')
    {
        if($form->numberSubgroup){
          $form->numberSubgroup->setValue(Engine_Api::_()->advgroup()->getNumberValue('group',$id,'numberSubgroup'));
        }
        if($form->numberPhoto){
          $form->numberPhoto->setValue(Engine_Api::_()->advgroup()->getNumberValue('group',$id,'numberPhoto'));
        }
        if($form->numberAlbum){
          $form->numberAlbum->setValue(Engine_Api::_()->advgroup()->getNumberValue('group',$id,'numberAlbum'));
        }
    }
    
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

   // Check validitiy
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process

    $values = $form->getValues();

    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
      // Set permissions
      if( isset($values['auth_comment']) ) {
        $values['auth_view'] = (array) @$values['auth_view'];
        $values['auth_comment'] = (array) @$values['auth_comment'];
        $values['auth_photo'] = (array) @$values['auth_photo'];
        $values['auth_poll'] =  (array) @$values['auth_poll'];
        $values['auth_sub_group'] =  (array) @$values['auth_sub_group'];
        $values['auth_video'] =  (array) @$values['auth_video'];
        $values['auth_music'] =  (array) @$values['auth_music'];
        $values['auth_folder'] =  (array) @$values['auth_folder'];
        $values['auth_file_upload'] =  (array) @$values['auth_file_upload'];
        $values['auth_file_down'] =  (array) @$values['auth_file_down'];
        $values['auth_listing'] =  (array) @$values['auth_listing'];
      }
      $permissionsTable->setAllowed('group', $level_id, $values);
      
      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Advgroup_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'advgroup');

        // insert the category into the database
        $row = $table->createRow();
        $row->title = $values["label"];
        $row->save();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }

    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function deleteCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->category_id = $id;
    
    $groupTable = Engine_Api::_()->getDbtable('groups', 'advgroup');
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'advgroup');
    $category = $categoryTable->find($id)->current();

    if(count($category->getSubCategories())>0){
      $this->view->canDelete = false;
    }
    else {
      $this->view->canDelete = true;
    }
      // Check post
      if( $this->getRequest()->isPost() ) {
        $db = $categoryTable->getAdapter();
        $db->beginTransaction();

        try {
          // go through logs and see which groups used this category id and set it to ZERO
          $groupTable->update(array(
            'category_id' => 0,
          ), array(
            'category_id = ?' => $category->getIdentity(),
          ));

          $category->delete();

          $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        return $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh'=> 10,
            'messages' => array('')
        ));
    }

    // Output
    $this->renderScript('admin-settings/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Must have an id
    if( !($id = $this->_getParam('id')) ) {
      die('No identifier specified');
    }
    $categoryTable = Engine_Api::_()->getDbtable('categories', 'advgroup');
    $category = $categoryTable->find($id)->current();
    if($category->parent_id != 0){
      $form = $this->view->form = new Advgroup_Form_Admin_Subcategory();
      $parent_categories = Engine_Api::_()->getDbTable('categories','advgroup')->getParentCategoriesAssoc();
      $form->parent_id->setMultiOptions($parent_categories);
    }
    else{
      $form = $this->view->form = new Advgroup_Form_Admin_Category();
    }
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $form->setField($category);
    
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        $category->title = $values["label"];
        if(array_key_exists("parent_id", $values)) $category->parent_id = $values["parent_id"];
        $category->save();
        
        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
      
      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    
    // Output
    $this->renderScript('admin-settings/form.tpl');
  }

  public function addSubCategoryAction(){
     // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Advgroup_Form_Admin_Subcategory();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $parent_categories  = Engine_Api::_()->getDbtable('categories', 'advgroup')->getParentCategoriesAssoc();
    $form->parent_id->setMultiOptions($parent_categories);

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getDbtable('categories', 'advgroup');

        // insert the category into the database
        $row = $table->createRow();
        $row->title = $values["label"];
        $row->parent_id = $values["parent_id"];
        $row->save();

        $db->commit();
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
  }
  // Output
    $this->renderScript('admin-settings/form.tpl');
  }
}