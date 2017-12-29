<?php foreach( $this->notifications as $notification ): 
 ob_start();
   try { 
  		$subject = $notification->getSubject(); ?>
            <li <?php if( !$notification->read ): ?> class = "ynadvmenu_Contentlist_unread"<?php $this->hasunread = true; ?> <?php endif; ?> value="<?php echo $notification->getIdentity();?>" >
				<a><?php echo $this->itemPhoto($subject, 'thumb.icon');?></a>   
				<div class="ynadvmenu_ContentlistInfo">
					<div class="ynadvmenu_NameUser" id="content_<?php echo $notification->getIdentity();?>">
						 <?php echo $notification->__toString() ?>
					</div>
					<div class="ynadvmenu_postIcon activity_icon_status notification_type_<?php echo $notification->type ?>"> 
						<span class="timestamp"> <?php echo $this->timestamp($notification->date)?> </span> 
					</div>
				</div>
			</li>
<?php } catch( Exception $e ) {
            ob_end_clean();
            if( APPLICATION_ENV === 'development' ) 
            {
              //echo $e->__toString();
            }
            continue;
          }
          ob_end_flush();
endforeach; ?>