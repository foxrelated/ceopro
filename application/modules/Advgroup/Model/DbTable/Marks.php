<?php
class Advgroup_Model_DbTable_Marks extends Engine_Db_Table
{
  	protected $_name = 'advgroup_announcement_marks';	
	
	public function markAnnouncement($params = array()){
		$db = Engine_Api::_()->getDbtable('marks', 'advgroup')->getAdapter();
	    $db->beginTransaction();
	
	    try {
	      // Create group
	      $table = Engine_Api::_()->getDbtable('marks', 'advgroup');
	      $group = $table->createRow();
	      $group->setFromArray($params);
	      $group->save();
	      // Commit
	      $db->commit();
		}
		catch(exception $e)
		{
			return "false";
		}
		return "true";
	}
}