<?php
class Ynmember_Widget_ProfileFieldsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
  	$settings = Engine_Api::_()->getApi('settings', 'core');
    // Don't render this if not authorized
    $this -> view -> viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
	
    // Get subject and check auth
    $this ->view -> subject = $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');


    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    if( count($fieldStructure) <= 1 ) { // @todo figure out right logic
      return $this->setNoRender();
    }
	
	//Workplace, living place, study place
	 $tableWork = Engine_Api::_() -> getItemTable('ynmember_workplace');
	 $tableLive = Engine_Api::_() -> getItemTable('ynmember_liveplace');
	 $tableStudy = Engine_Api::_() -> getItemTable('ynmember_studyplace');
	 $this -> view -> studyplaces = $studyplaces = $tableStudy -> getStudyPlacesByUserId($subject -> getIdentity());
	 $this -> view -> workplaces = $workplaces = $tableWork -> getWorkPlacesByUserId($subject -> getIdentity());
  	 $this -> view -> currentliveplaces = $currentliveplaces = $tableLive -> getLiveCurrentPlacesByUserId($subject -> getIdentity());
  	 $this -> view -> pastliveplaces = $pastliveplaces = $tableLive -> getLivePastPlacesByUserId($subject -> getIdentity());
  	
	//Get Feature
	 $tableFeature = Engine_Api::_() -> getItemTable('ynmember_feature');
	 $this -> view -> row_feature = $row_feature = $tableFeature -> getFeatureRowByUserId($subject -> getIdentity());
	 
	 $relationshipTbl = Engine_Api::_()->getItemTable('ynmember_relationship');
	 $relationshipTblName = $relationshipTbl -> info ('name');
	 
	 $linkageTbl = Engine_Api::_()->getItemTable('ynmember_linkage');
	 $linkageTblName = $linkageTbl -> info('name');
	 $select = $linkageTbl 
	 -> select () -> setIntegrityCheck(false)
	 -> from ($linkageTblName)
	 -> joinLeft($relationshipTblName, "{$relationshipTblName}.relationship_id = {$linkageTblName}.relationship_id")
	 -> where("{$linkageTblName}.user_id = ? ", $subject->getIdentity())
	 -> where("{$linkageTblName}.user_approved = ? ", '1')
	 -> order("{$linkageTblName}.linkage_id DESC ")
	 -> limit(1);
	 ;
	 
	 $this -> view -> linkage = $linkage = $linkageTbl -> fetchRow($select);
	 $this -> view -> can_add_place = $can_add_place =  $settings->getSetting('ynmember_allow_add_workplace', 1);
  	 $this -> view -> allow_update_relationship = $allow_update_relationship = $settings->getSetting('ynmember_allow_update_relationship', 1);
}
}