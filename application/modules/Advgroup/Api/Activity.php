<?php
class Advgroup_Api_Activity extends Core_Api_Abstract
{
  protected $actionTypeModuleMap = array(
      'advgroup_create' => 'advgroup',
      'advgroup_join' => 'advgroup',
      'advgroup_promote' => 'advgroup',
      'advgroup_transfer' => 'advgroup',
      'advgroup_topic_create' => 'advgroup',
      'advgroup_topic_reply' => 'advgroup',
      'advgroup_photo_upload' => 'advgroup',
      'advgroup_poll_new' => 'poll',
      'advgroup_sub_create' => 'advgroup',
      'advgroup_video_create' => 'video,ynvideo,ynultimatevideo',
      'advgroup_listing_create' => 'ynlistings',
      'advgroup_wiki_create' => 'ynwiki',
      'advgroup_wiki_update'  => 'ynwiki',
      'advgroup_ynmusic_song_create' => 'ynmusic',
      'advgroup_ynmusic_album_create' => 'ynmusic',
      'advgroup_wiki_move' => 'ynwiki',
  );
  /**
   * @param <array> $params
   * @return Activity_Model_Action_RowSet
   */
  public function getActionsByObject($params)
  {
    $action_table = Engine_Api::_()->getDbtable('actions', 'activity');
    $action_name = $action_table -> info('name');
    $action_type_table = Engine_Api::_()->getDbTable('publicActivities','advgroup');
    $type_name = $action_type_table->info('name');

    $select = $action_table -> select()->from($action_name)
              ->setIntegrityCheck(false)
              ->join($type_name,"$action_name.object_id = $type_name.group_id AND $action_name.type = $type_name.public_types")
              -> where('object_type = ?', 'group');

    /**
     * @todo filter select with some condition here
     */
    if(isset($params['order'])){
      $select->order($action_name.".".$params['order']." DESC");
    }
    if(isset($params['limit'])){
      $select->limit($params['limit']);
    }
    if(isset($params['minId'])){
      $select->where($action_name.'.action_id >= ?',$params['minId']);
    }
    if(isset($params['maxId'])){
      $select->where($action_name.'.action_id <= ?',$params['maxId']);
    }
    return $action_table->fetchAll($select);
  }

  /**
   * Checking if the plugin is enable in the system
   * @param string $name - name of module
   * @return boolean
   */
  public function moduleCheck($names)
  {
    //Checking specific module according to module name
    $module_table = Engine_Api::_() -> getDbTable('modules', 'core');
    $module_table->getModules();
    $module_enabled = false;
    foreach (explode(',', $names) as $name) {
    if($module_table->hasModule($name) && $module_table->isModuleEnabled($name))
    {
        $module_enabled = true;
    }
    }
    return $module_enabled;
  }

  public function getActionTypesAssoc(){
    // Get available types
    $result = array('post'=> Zend_Registry::get('Zend_Translate')->_('group_activity_post'));
    $actionType_table = Engine_Api::_()->getDbTable('actionTypes','activity');
    $select = $actionType_table->select()->where('enabled = ?',1);

    if($this->moduleCheck('advgroup'))
    {
      $select->where('module = ?','advgroup');
    }
    elseif($this->moduleCheck('group'))
    {
      $select->where('module = ?','group');
    }

    $types = $actionType_table->fetchAll($select);
    if($types)
    {
      foreach($types as $type)
      {
        if ($this->moduleCheck($this->actionTypeModuleMap[$type->type]))
        $result[$type->type] = 'group_activity_'.$type->type;
      }
    }
    return $result;
  }


  public function getGroupActionTypes($group_id){
    if(empty ($group_id))
    {
        return array();
    }
    $table = Engine_Api::_()->getDbTable('publicActivities','advgroup');
    $select =$table->select()->where('group_id = ?',$group_id);
    $types = $table->fetchAll($select);
    $result = array();
    if($types)
    {
       foreach($types as $type)
       {
         $result[] = $type->public_types;
       }
    }
    return $result;
  }
}
?>
