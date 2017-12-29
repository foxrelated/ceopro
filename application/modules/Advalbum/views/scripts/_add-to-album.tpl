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
	$album = Engine_Api::_ ()->getItem('advalbum_album', $this->album_id ); 
?>
    <div class="ynadvalbum_addTo_frame" id="ynadvalbum_addTo_list">
        <?php if ($this->is_login) : ?>
        	<?php if (!$album->virtual) :?>
            <div class="ynadvalbum_addTo_managephotos">
					<?php echo $this->htmlLink(array(
							'route' => 'album_specific',
							'action' => 'editphotos',
							'album_id' => $this->album_id),
							$this->translate('Manage Photos'), array(
					      'class' => 'ynadvalbum_add_to_menu_item'
					    )) ?>
            </div>
             <?php endif;?>
            <div class="ynadvalbum_addTo_managephotos">
				<?php echo $this->htmlLink(array(
						'route' => 'album_specific',
						'action' => 'edit',
						'album_id' => $this->album_id),
						$this->translate('Edit Settings'), array(
      						'class' => 'ynadvalbum_add_to_menu_item'
    					)) ?>
            </div>
            
            <div class="ynadvalbum_addTo_managephotos" id='ynadvalbum_delete_album'>
				<?php echo $this->htmlLink(array(
						'route' => 'album_specific',
						'action' => 'delete',
						'album_id' => $this->album_id,
						'format' => 'smoothbox'),
						$this->translate('Delete Album'), array(
      						'class' => 'ynadvalbum_add_to_menu_item smoothbox',
    			)) ?>
            </div>
           
        <?php else : ?>
            <div class="ynadvalbum_addTo_result_block">
                <?php
                echo $this->htmlLink(
                        array('route' => 'user_login'), 'Sign In') . ' ' . $this->translate('to manage your albums')
                ?>
            </div>
        <?php endif; ?>
    </div>