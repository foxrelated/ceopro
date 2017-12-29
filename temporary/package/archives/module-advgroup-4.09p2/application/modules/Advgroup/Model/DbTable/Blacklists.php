<?php
class Advgroup_Model_DbTable_Blacklists extends Engine_Db_Table
{
  protected $_name = 'advgroup_blacklists';


	 public function getBlackListMembers($group_id, $search = null)
	 {
		 	try
		 	{
			 	$ids =  array();
			    $select = $this->select()
								->where('group_id = ?', $group_id);
				
				foreach( $this->fetchAll($select) as $row )
			    {
			  	
			      $ids[] = $row->user_id;
			    }
				
				$user_table = Engine_Api::_()->getItemTable('user');
				$select = $user_table->select()->where('user_id IN (?)',$ids)->order('displayname ASC');
				if($search){
					$select -> where('displayname LIKE ?', '%' . $search . '%');
				}
				return $user_table->fetchAll($select);
			}
			catch(Exception $e)
			{
				return false;
			}
	 }
	 
	 public function checkBlackListMembers($group_id, $user_id)
	 {
	 	$select = $this->select()
								->where('group_id = ?', $group_id)
								->where('user_id = ?', $user_id);
		$count = 0;
		foreach( $this->fetchAll($select) as $row )
		{ 	
			$count++;
		}
		if($count == 0){
			return "false";
		}
		else{
			return "true";
		}
	 }
}
  
 
