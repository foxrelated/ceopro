<?php
class Advgroup_Model_DbTable_PublicActivities extends Engine_Db_Table
{
    protected $_name  = 'advgroup_public_activities';

    public function deleteGroupActionTypes($group_id)
    {
      // Check input
      if(!isset($group_id))
      {
        return;
      }

      // Process
      $select = $this->select()->where('group_id = ?',$group_id);
      $types = $this->fetchAll($select);
      if($types)
      {
        foreach ($types as $type)
        {
          $type->delete();
        }
      }
    }

    public function updateGroupActionTypes($group_id,$types)
    {
      // Check input
      if(!isset($group_id))
      {
        return;
      }
      if(!isset($types)||!is_array($types))
      {
        return;
      }

      //Process
      foreach($types as $type)
      {
        $row = $this->createRow();
        $row -> group_id = $group_id;
        $row -> public_types = $type;
        $row -> save();
      }
    }
}
?>
