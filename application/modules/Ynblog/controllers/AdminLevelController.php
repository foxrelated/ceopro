<?php
class Ynblog_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynblog_admin_main', array(), 'ynblog_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Ynblog_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('blog', $id, array_keys($form->getValues())));
    // get max allow
    $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $msselect = $mtable->select()
                ->where("type = 'blog'")
                ->where("level_id = ?",$id)
                ->where("name = 'max'");
    $mallow = $mtable->fetchRow($msselect);
    if (!empty($mallow))
        $max = $mallow['value'];
    if($id < 5)
    {
        $max_get = $form->max->getValue();
        if ($max_get < 1)
        	$form->max->setValue($max);
    }
    // Check post
    if( !$this->getRequest()->isPost() ) {
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
      $permissionsTable->setAllowed('blog', $id, $values);

      // Commit
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
     $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
  }

}