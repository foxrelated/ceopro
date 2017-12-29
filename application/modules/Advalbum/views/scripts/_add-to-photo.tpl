<?php
/**
 * YouNetCo
 *
 * @category   Application_Extensions
 * @package    ynadvalbum
 * @author     YouNetCo
 */
?>
<?php

?>
<?php
$photo = Engine_Api::_ ()->getItem('advalbum_photo', $this->photo_id );

?>
<div class="ynadvalbum_addTo_frame" id="ynadvalbum_addTo_list">
 	<?php if (!$this->is_virtual) :?>
	 	<?php if ($this->has_virtual_album):?>
	 	<div class="ynadvalbum_addTo_managephotos">
	            <?php echo $this->htmlLink(array(
						'route' => 'album_photo_specific',
						'action' => 'add-to-virtual',
						'album_id' => $this->album_id,
				        'photo_id' => $this->photo_id,
				        'format' => 'smoothbox'),
						$this->translate('Add to Virtual Album'), array(
				      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
				    )) ?>
	    </div>
	    <?php endif;?>
    <?php else: ?>
    <?php if ($photo->isOwner(Engine_Api::_() -> user() -> getViewer())):?>			
    	<div class="ynadvalbum_addTo_managephotos">
	            <?php echo $this->htmlLink(array(
						'route' => 'album_photo_specific',
						'action' => 'delete-virtual-photo',
						'album_id' => $this->album_id,
				        'photo_id' => $this->photo_id,
				        'format' => 'smoothbox'),
						$this->translate('Remove photo'), array(
				      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
				    )) ?>
	    </div>
	 <?php endif;?>   
    <?php endif;?>   
<?php if ($this->is_login): ?>
    <?php if (!$this->is_virtual) :?>
    <div class="ynadvalbum_addTo_managephotos ynadvalbum_border">
            <?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'delete-photo',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
			        'format' => 'smoothbox'),
					$this->translate('Delete Photo'), array(
			      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
			    )) ?>
    </div>
    <?php endif;?>
    <?php if (!$this->is_virtual) :?>
    <div class="ynadvalbum_addTo_managephotos " id='ynadvalbum_photo_edit_title'>
			<?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'edit-title',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
			        'type' => 'title',
			        'format' => 'smoothbox'),
					$this->translate('Edit Title'), array(
			      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
			    )) ?>
    </div>
    <?php endif;?>
    <?php if (!$this->is_virtual) :?>
    <div class="ynadvalbum_addTo_managephotos">
			<?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'edit-title',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
			        'type' => 'taken_date',
			        'format' => 'smoothbox'),
					$this->translate('Change Date'), array(
			      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
			    )) ?>
    </div>
    <?php endif;?>
    <?php if (!$this->is_virtual) :?>
    <div class="ynadvalbum_addTo_managephotos ynadvalbum_border">
    	<?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'change-location',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
			        'format' => 'smoothbox'),
					$this->translate('Change Location'), array(
			      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
			    )) ?>
    </div>
    <?php endif;?>
    <?php if (!$this->is_virtual) :?>
    <div class="ynadvalbum_addTo_managephotos">
            <?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'set-album-cover',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
			        'format' => 'smoothbox'),
					$this->translate('Set Album Cover'), array(
			      'class' => 'ynadvalbum_add_to_menu_item smoothbox'
			    )) ?>
    </div>
    <?php endif;?>
<?php endif; ?>
    <div class="ynadvalbum_addTo_managephotos ynadvalbum_border" id='ynadvalbum_photo_profile_cover'>
          <?php echo $this->htmlLink(array('route' => 'user_extended',
                  'module' => 'user',
                  'controller' => 'edit',
                  'action' => 'external-photo',
                  'photo' => $photo->getGuid(),
                  'format' => 'smoothbox'),
                  $this->translate('Make Profile Photo'),
                  array('class' => 'ynadvalbum_add_to_menu_item smoothbox')
           ) ?>
    </div>
    <div class="ynadvalbum_addTo_managephotos">
           <?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'download-photo',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
                   'photo_type' => 'profile'
                    ),
					$this->translate('Download Resized Photo'), array(
			      'class' => 'ynadvalbum_add_to_menu_item',
                  'id' => 'ynadvalbum_addTo_downloadresizephoto'
			    )) ?>
    </div>
    <div class="ynadvalbum_addTo_managephotos">
            <?php echo $this->htmlLink(array(
					'route' => 'album_photo_specific',
					'action' => 'download-photo',
					'album_id' => $this->album_id,
			        'photo_id' => $this->photo_id,
                    ),
					$this->translate('Download Full Size Photo'), array(
			      'class' => 'ynadvalbum_add_to_menu_item',
                  'id' => 'ynadvalbum_addTo_downloadfullphoto'
			    )) ?>
    </div>
</div>