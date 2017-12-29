<?php
class Advalbum_Plugin_Composer extends Core_Plugin_Abstract
{
  public function onAttachPhoto($data)
  {
    if( !is_array($data) || empty($data['photo_id']) ) {
      return;
    }
    $photo_ids_str = trim($data['photo_id']);
    $photo_ids = explode(' ', $photo_ids_str);
    $main_photo_id = $photo_ids[0];
    $main_photo = Engine_Api::_()->getItem('advalbum_photo', $main_photo_id);
    $photo_ids_str = trim(str_replace($main_photo_id, '', $photo_ids_str));

    if (!empty($photo_ids_str)) {
      $_SESSION['advalbum_attachment_' . $main_photo_id] = $photo_ids_str;
    }

    return $main_photo;
  }
}