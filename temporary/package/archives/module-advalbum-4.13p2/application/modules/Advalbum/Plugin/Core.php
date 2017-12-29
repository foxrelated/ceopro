<?php
class Advalbum_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('photos', 'advalbum');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'photo');
  }

  public function onUserProfilePhotoUpload($event)
  {
    $payload = $event->getPayload();
    if( empty($payload['user']) || !($payload['user'] instanceof Core_Model_Item_Abstract) )
    {
      return;
    }
    if( empty($payload['file']) || !($payload['file'] instanceof Storage_Model_File) )
    {
      return;
    }

    $viewer = $payload['user'];
    $file = $payload['file'];

    // Get album
    $table = Engine_Api::_()->getDbtable('albums', 'advalbum');
    $album = $table->getSpecialAlbum($viewer, 'profile');

    $photo = Engine_Api::_()->advalbum()->createPhoto(array(
        'owner_type' => 'user',
        'owner_id' => Engine_Api::_()->user()->getViewer()->getIdentity()
        ), $file);


    $photo->album_id = $album->album_id;
    $photo->save();

    if( !$album->photo_id )
    {
      $album->photo_id = $photo->getIdentity();
      $album->save();
    }

    $auth      = Engine_Api::_()->authorization()->context;
    $auth->setAllowed($photo, 'everyone', 'view',    true);
    $auth->setAllowed($photo, 'everyone', 'comment', true);
    $auth->setAllowed($album, 'everyone', 'view',    true);
    $auth->setAllowed($album, 'everyone', 'comment', true);
    
    $event->addResponse($photo);
  }

  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['identity'];
    $table   = Engine_Api::_()->getDbTable('albums', 'advalbum');
    $select = $table->select()->where('owner_id = ?', $user_id);
    $select = $select->where('owner_type = ?', 'user');
    $rows = $table->fetchAll($select);
    foreach ($rows as $row)
    {
      $row->delete();
    }
    $table   = Engine_Api::_()->getDbTable('photos', 'advalbum');
    $select = $table->select()->where('owner_id = ?', $user_id);
    $select = $select->where('owner_type = ?', 'user');
    $rows = $table->fetchAll($select);
    foreach ($rows as $row)
    {
      $row->delete();
    }
  }

  public function onActivityActionUpdateAfter($event)
  {
    $payload = $event->getPayload();

    if (!($payload instanceof Activity_Model_Action)) {
      return;
    }

    // get action
    $action = $payload;

    // get current attachment
    $attachmentsTable = Engine_Api::_()->getDbtable('attachments', 'activity');
    $select = $attachmentsTable->select()
        ->where('action_id = ?', $action->action_id);
    $attachment = $attachmentsTable->fetchRow($select);

    // check attachment condition
    if (!$attachment || !isset($attachment->type) || !($attachment->type == 'advalbum_photo') || !isset($attachment->mode) || !((int) $attachment->mode == 1))
    {
      return;
    }

    // get photo batch
    $main_photo_id = $attachment->id;
    $photo_ids = array();
    if (isset($_SESSION['advalbum_attachment_' . $main_photo_id]))
      $photo_ids = explode(' ', $_SESSION['advalbum_attachment_' . $main_photo_id]);
    if (!$action->body) {
      $actionTable = $action->getTable();
      $totalPhotos = count($photo_ids) + 1;
      $translate = Zend_Registry::get('Zend_Translate');
      $body = vsprintf($translate->_(array('added a new photo', 'added %s new photos', $totalPhotos)),
          Zend_Locale_Format::toNumber($totalPhotos)
      );
      $actionTable->update(array(
          'body' => $body
      ),array(
          'action_id = ?' => $action->action_id
      ));
    }
    if (empty($photo_ids)) {
      return;
    }

    // modify main attachment to set it to attach multi, prevent looping this action
    $attachmentsTable->update(array(
      'mode' => Activity_Model_Action::ATTACH_MULTI
    ),array(
      'attachment_id = ?' => $attachment->attachment_id
    ));

    // attach remain photos
    $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
    foreach ($photo_ids as $photo_id) {
      $photo = Engine_Api::_()->getItem('advalbum_photo', $photo_id);
      if ($photo) {
        $activityApi->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
      }
    }

    unset($_SESSION['advalbum_attachment_' . $main_photo_id]);
  }
}
