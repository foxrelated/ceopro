<?php
class Ynforum_Model_Icon extends Core_Model_Item_Abstract
{
  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else 
    {
    }
    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => 'ynforum_icon',
      'parent_id' => $this->getIdentity()
    );
    // Save
    $storage = Engine_Api::_()->storage();
	// Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();
    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
          ->resize(34, 34)
          ->write($path.'/in_'.$name)
          ->destroy();
    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x    = ($image->width - $size) / 2;
    $y    = ($image->height - $size) / 2;
    $image->resample($x, $y, $size, $size, 16, 16)
          ->write($path.'/is_'.$name)
          ->destroy();
    // Store
    $iMain       = $storage->create($path.'/m_'.$name,  $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare     = $storage->create($path.'/is_'.$name, $params);
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare,     'thumb.icon');
    // Update row
    $this->photo_id      = $iMain->getIdentity();
    $this->save();
    return $this;
  } 
}