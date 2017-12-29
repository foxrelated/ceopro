<?php
/**
 * YouNet Company
*
* @category   Application_Extensions
* @package    Advgroup
* @author     YouNet Company
*/

class Advgroup_Model_Sponsor extends Core_Model_Item_Abstract {
	protected $_searchTriggers = false;
	
	public function setPhoto($photo) {
          if ($photo instanceof Zend_Form_Element_File) {
               $file = $photo->getFileName();
          } else if (is_array($photo) && !empty($photo['tmp_name'])) {
               $file = $photo['tmp_name'];
          } else if (is_string($photo) && file_exists($photo)) {
               $file = $photo;
          } else {
               throw new Event_Model_Exception('invalid argument passed to setPhoto');
          }

          $name = basename($file);
          $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
          $params = array(
              'parent_id' => $this->getIdentity(),
              'parent_type' => 'event_sponsor'
          );

          // Save
          $storage = Engine_Api::_()->storage();

          // Resize image (main)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(720, 720)
                  ->write($path . '/m_' . $name)
                  ->destroy();

          // Resize image (profile)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(200, 400)
                  ->write($path . '/p_' . $name)
                  ->destroy();

          // Resize image (normal)
          $image = Engine_Image::factory();
          $image->open($file)
                  ->resize(140, 160)
                  ->write($path . '/in_' . $name)
                  ->destroy();

          // Resize image (icon)
          $image = Engine_Image::factory();
          $image->open($file);

          $size = min($image->height, $image->width);
          $x = ($image->width - $size) / 2;
          $y = ($image->height - $size) / 2;

          $image->resample($x, $y, $size, $size, 48, 48)
                  ->write($path . '/is_' . $name)
                  ->destroy();

          // Store
          $iMain = $storage->create($path . '/m_' . $name, $params);
          $iProfile = $storage->create($path . '/p_' . $name, $params);
          $iIconNormal = $storage->create($path . '/in_' . $name, $params);
          $iSquare = $storage->create($path . '/is_' . $name, $params);

          $iMain->bridge($iProfile, 'thumb.profile');
          $iMain->bridge($iIconNormal, 'thumb.normal');
          $iMain->bridge($iSquare, 'thumb.icon');

          // Remove temp files
          @unlink($path . '/p_' . $name);
          @unlink($path . '/m_' . $name);
          @unlink($path . '/in_' . $name);
          @unlink($path . '/is_' . $name);

          // Update row
          $this->photo_id = $iMain->file_id;
          $this->save();
          return $this;
     }
}