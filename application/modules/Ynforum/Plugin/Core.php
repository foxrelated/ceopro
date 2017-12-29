<?php
class Ynforum_Plugin_Core {

    public function onStatistics($event) {
        $table = Engine_Api::_()->getDbTable('topics', 'ynforum');
        $select = new Zend_Db_Select($table->getAdapter());
        $select->from($table->info('name'), 'COUNT(*) AS count');
        $event->addResponse($select->query()->fetchColumn(0), 'forum topic');
    }

    public function onUserDeleteAfter($event) {
        $payload = $event->getPayload();
        $user_id = $payload['identity'];

        // Signatures
        $table = Engine_Api::_()->getDbTable('signatures', 'ynforum');
        $table->delete(array(
            'user_id = ?' => $user_id,
        ));

        // Moderators
        $table = Engine_Api::_()->getDbTable('listItems', 'ynforum');
        $select = $table->select()->where('child_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }
        
        $table = Engine_Api::_()->getDbTable('categoryListItems', 'ynforum');
        $select = $table->select()->where('child_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            $row->delete();
        }

        // Topics
        $table = Engine_Api::_()->getDbTable('topics', 'ynforum');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            //$row->delete();
        }

        // Posts
        $table = Engine_Api::_()->getDbTable('posts', 'ynforum');
        $select = $table->select()->where('user_id = ?', $user_id);
        $rows = $table->fetchAll($select);
        foreach ($rows as $row) {
            //$row->delete();
        }

        // Topic views
        $table = Engine_Api::_()->getDbTable('topicviews', 'ynforum');
        $table->delete(array(
            'user_id = ?' => $user_id,
        ));
        
        // Thanks
        $table = Engine_Api::_()->getDbTable('thanks', 'ynforum');
        $table->delete(array(
            'user_id = ?' => $user_id,
        ));
    }

    public function addActivity($event) {
        $payload = $event->getPayload();
        $object = $payload['object'];

        // Only for object=forum
        if ($object instanceof Ynforum_Model_Topic) {

            $content = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.content', 'everyone');
            $allowTable = Engine_Api::_()->getDbtable('allow', 'authorization');

            // Everyone
            if ($content == 'everyone' && $allowTable->isAllowed($object->getAuthorizationItem(), 'everyone', 'view')) {
                $event->addResponse(array(
                    'type' => 'everyone',
                    'identity' => 0
                ));
            }
        }
     }
	public function onItemCreateAfter($event)
	{				
		$item = $event -> getPayload();

		// get item type
		$item_type = $item -> getType();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		if(!$request)
			return;
		
		$view  = Zend_Registry::get('Zend_View');
		
		if(isset($_SESSION['ynforum']) && $_SESSION['ynforum']['parent_id'])
		{
			$url_callback = "";
			
			switch ($item_type) 
			{
				case 'event':
					try {
                        if ($item instanceof Event_Model_Event) {
                            $item->refresh();
                            $item -> parent_type = 'forum';
                            $item -> parent_id = $_SESSION['ynforum']['parent_id'];
                            $item -> save();
                        }
					}
					catch (Exception $e) {
					}	
					if($request->getParam('module') == 'ynevent')
					{
						$key = 'forum_predispatch_url:'.$request->getParam('module').'.index.manage';
					}
					else{
						$key = 'forum_predispatch_url:'.$request->getParam('module').'.profile.index';
					}
					$url_callback = $view->url(array('action' => 'invite', 'forum_id' => $_SESSION['ynforum']['parent_id']), 'ynforum_event', true);
					$url_callback = $url_callback."/event_id/".$item->getIdentity();
					$flag = true;		
					break;
				case 'poll':
				    $table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
					
					try {
						$row = $table -> createRow();
					    $row -> setFromArray(array(
					       'forum_id' => $_SESSION['ynforum']['parent_id'],
					       'item_id' => $item -> getIdentity(),
					       'user_id' => $item -> user_id,				       
					       'type' => 'poll',
					       'creation_date' => date('Y-m-d H:i:s'),
					       'modified_date' => date('Y-m-d H:i:s'),
					       ));
					    $row -> save();	
					}
					catch (Exception $e) {
					}	
					$key = 'forum_predispatch_url:'.$request->getParam('module').'.poll.view';
					$flag = true;	
					$url_callback = $view->url(array('action' => 'manage', 'forum_id' => $_SESSION['ynforum']['parent_id']), 'ynforum_poll', true);	
					break;								
				
				case 'group':
					$table = Engine_Api::_() -> getDbTable('highlights', 'ynforum');
					try {
						$row = $table -> createRow();
					    $row -> setFromArray(array(
					       'forum_id' => $_SESSION['ynforum']['parent_id'],
					       'item_id' => $item -> getIdentity(),
					       'user_id' => $item -> user_id,				       
					       'type' => 'group',
					       'creation_date' => date('Y-m-d H:i:s'),
					       'modified_date' => date('Y-m-d H:i:s'),
					       ));
					    $row -> save();
					}
					catch (Exception $e) {
					}
					$key = 'forum_predispatch_url:'.$request->getParam('module').'.profile.index';
					$flag = true;	
					$url_callback = $view->url(array('action' => 'invite', 'forum_id' => $_SESSION['ynforum']['parent_id']), 'ynforum_group', true);
					$url_callback = $url_callback."/group_id/".$item->getIdentity();
					break;
			}
			if($flag){
				$_SESSION[$key]= $url_callback;
			}			
		}		
	}
}