<?php
class Advgroup_Widget_OverallStatisticController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $groupTable = Engine_Api::_()->getItemTable('group');

    $select = new Zend_Db_Select($groupTable->getAdapter());
    $select -> from($groupTable->info('name'), 'COUNT(*) AS count')
            -> where('group_id > 0');
    $this->view->count_groups =  $select->query()->fetchColumn(0);

    $albumTable = Engine_Api::_()->getItemTable('advgroup_album');
    $select = new Zend_Db_Select($albumTable->getAdapter());
    $select->from($albumTable->info('name'), 'COUNT(*) AS count')
            -> where('album_id > 0');;
    $this->view->count_albums =  $select->query()->fetchColumn(0);

    $photoTable = Engine_Api::_()->getItemTable('advgroup_photo');
    $select = new Zend_Db_Select($photoTable->getAdapter());
    $select->from($photoTable->info('name'), 'COUNT(*) AS count')
            -> where('photo_id > 0');;
    $this->view->count_photos =  $select->query()->fetchColumn(0);
    
    $topicTable = Engine_Api::_()->getItemTable('advgroup_topic');
    $select = new Zend_Db_Select($topicTable->getAdapter());
    $select->from($topicTable->info('name'), 'COUNT(*) AS count')
           ->where('topic_id > 0');;
    $this->view->count_topics =  $select->query()->fetchColumn(0);
  }
}