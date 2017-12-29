<?php
class Ynblog_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_settings');

    $this->view->form  = $form = new Ynblog_Form_Admin_Global();

    if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
       $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
  }

  public function categoriesAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_categories');

    $this->view->maxCategoryLevel = 3;
    $this->view->category = $cat = Engine_Api::_()->getItem('blog_category', $this->_getParam('parent_id', 0));
    $categories = Engine_Api::_()->getItemTable('blog_category')->getCategories(array('parent_id' => $this->_getParam('parent_id', 0)));
    $this->view->categories = $categories;
  }


  public function addCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');

    // Generate and assign form
    $form = $this->view->form = new Ynblog_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));
    $parentId = $this->_getParam('parent_id', 0);
    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // we will add the category
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // add category to the database
        // Transaction
        $table = Engine_Api::_()->getItemTable('blog_category');
        $parent = $table ->getCategory($parentId);
        // insert the blog entry into the database
        $row = $table->createRow();
        $row->user_id   =  1;
        $row->category_name = $values["label"];
        $row->level = ($parent->level + 1);
        $row->parent_id = is_numeric($parentId) ? $parentId : 0;
        $row->save();

        // change the category of all the blogs using that category

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category was added successfully.'))
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
    $this->view->moveNodeID=$id;
    $this->view->moveNode = $category = Engine_Api::_()->getItem('blog_category', $id);
    $this->view->hasBlogs = $hasBlogs = $category -> checkHasBlogs();

    $tableCat = Engine_Api::_() -> getItemTable('blog_category');
    if ($hasBlogs) {
        $this->view->moveCates = $tableCat->getAllCategories();
    }
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // go through logs and see which blog used this $id and set it to ZERO
          if ($hasBlogs) {
              $blogs = $tableCat -> getAllChildrenBlogsByCategory($category);
              foreach ($blogs as $items) {
                  foreach ($items as $blogItem) {
                      try {
                          $db -> beginTransaction();
                          $blogItem -> category_id = $this->_getParam('move_category', 0);
                          $blogItem -> save();

                          $db -> commit();
                      } catch(Exception $e) {
                          $db -> rollBack();
                          throw $e;
                      }
                  }
              }
          }

          $list_categories = array();
          Engine_Api::_()->getItemTable('blog_category') -> appendChildToTree($category, $list_categories);
          foreach($list_categories as $category)
          {
              if($category)
                  $category->delete();
          }


        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category was deleted successfully.'))
      ));
    }

    // Output
    $this->renderScript('admin-settings/delete.tpl');
  }

  public function editCategoryAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $form = $this->view->form = new Ynblog_Form_Admin_Category();
    $form->setAction($this->getFrontController()->getRouter()->assemble(array()));

    // Check post
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      // Ok, we're good to add field
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        // edit category in the database
        // Transaction
        $row = Engine_Api::_()->getItemTable('blog_category')->getCategory($values["id"]);

        $row->category_name = $values["label"];
        $row->save();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('The category was modified successfully.'))
      ));
    }

    // Must have an id
    if( !($id = $this->_getParam('id')) )
    {
      die(Zend_Registry::get('Zend_Translate')->_('No identifier specified'));
    }

    // Generate and assign form
    $category = Engine_Api::_()->getItemTable('blog_category')->getCategory($id);
    $form->setField($category);

    // Output
    $this->renderScript('admin-settings/form.tpl');

  }
}