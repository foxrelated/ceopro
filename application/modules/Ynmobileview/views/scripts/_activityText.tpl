<?php
if( empty($this->actions) ) 
{
  echo $this->translate("The action you are looking for does not exist.");
  return;
} 
else 
{
   $actions = $this->actions;
} ?>

<?php $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmobileview/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js');
 ?>
<?php if( !$this->getUpdate ): ?>
<ul class='feed ynmb_homeFeed' id="activity-feed">
<?php endif ?>
  
<?php
  foreach( $actions as $action ): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
      
      ob_start();
    ?>
  <?php if( !$this->noList ): ?><li id="activity-item-<?php echo $action->action_id ?>" data-activity-feed-item="<?php echo $action->action_id ?>"><?php endif; ?>
    <?php // User's profile photo ?>
    <div class='feed_item_photo'><?php echo $this->htmlLink($action->getSubject()->getHref(),
      $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())
    ) ?></div>

    <div class='feed_item_body'>
      <?php // Main Content ?>
      <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
        <?php echo $action->getContent()?>
      </span>
	  <?php
        $icon_type = 'activity_icon_'.$action->type;
		$icon_type = 'activity_icon_'.$action->type;
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
	  ?>
	  <div class="ynmb_statusTime feed_item_icon <?php echo $icon_type ?>">
		<?php echo $this->timestamp($action->getTimeValue()) ?>
	  </div>
	  <?php if(empty($action->getTypeInfo()->is_generated) || $action->type == 'share' || $action->type == 'advgroup_link_new'):?>
		<?php $body = $action->body;
		if(!$body) 
		{
			$pos = strpos($action->getContent(), 'feed_item_bodytext');
			if($pos)
			{
				$body = substr($action->getContent(), $pos);
				$body = str_replace('</span>', '', $body);
				$body = str_replace('feed_item_bodytext">', '', $body);
			}
		}
		?>
		  <?php if($body):?>
			  <div class="ynmb_item_body">
			  		<?php echo $body?>
			  </div>
		  <?php endif;?>
	  <?php endif;?>
      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments <?php if(count($action->getAttachments()) > 1 && in_array(current($action->getAttachments())->item->getType(), array('album_photo', 'advalbum_photo', 'advgroup_photo', 'event_photo', 'ynevent_photo', 'group_photo'))) echo "ynmb_feed_manyphotos";?>' >
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>                    
              <?php 
              if(current($action->getAttachments())->item->gettype() == 'music_playlist_song' || current($action->getAttachments())->item->gettype() == 'music_playlist'):
              	$detail_url = $this->url(array('action_id' => $action->action_id, 'play' => 'on'),'mobi_feed', true);?>
              	<?php if(current($action->getAttachments())->item->gettype() == 'music_playlist' && count(current($action->getAttachments())->item->getSongs()) <= 0):?>
              		<div class="tip">
				      <span>
				        <?php echo $this->translate('There are no songs uploaded yet.') ?>
				      </span>
				    </div>
              	<?php else:?>
	              	<div class = "mp3music_album_thumb">
						<a href="<?php echo $detail_url?>">
						<?php $album = current($action->getAttachments())->item;
						if(current($action->getAttachments())->item->gettype() == 'music_playlist_song')
							$album = $album->getParent();	
						echo $this->itemPhoto($album, 'main') ?>
						<span class = "mp3music_play_thumb"></span></a>
					</div>
				<?php endif;?>
			  <?php else:
              	echo $richContent;
				endif; ?>
            <?php else: ?>
              <?php $count = 0; //count attachments for mobile
              $thumb_main = 'thumb.main';
              foreach( $action->getAttachments() as $attachment ):
				$count ++;
				if($count == 2 && count($action->getAttachments()) > 1 && in_array($attachment->meta->type, array('album_photo', 'event_photo', 'advgroup_photo', 'advalbum_photo', 'ynevent_photo', 'group_photo')))
				{
					$thumb_main = 'thumb.normal';
				}
              	if($count <= 3): 
              	?>
	                <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
	                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
	                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
	                  <div>
	                    <?php 
	                      if ($attachment->item->getType() == "core_link")
	                      {
	                        $attribs = Array('target'=>'_blank');
	                      }
	                      else
	                      {
	                        $attribs = Array();
	                      } 
	                    ?>
	                    <?php if(!$attachment->item->getPhotoUrl() && (in_array($attachment->item->getType(), array("core_link","advgroup_topic", 'blog', 'forum_topic'))) ):
	                    	else:?>
		                    <?php if( $attachment->item->getPhotoUrl() )
		                    	$attribs['class'] = 'ynmb_item_onephoto_full'; 
												?>
		                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, $thumb_main, $attachment->item->getTitle()), $attribs) ?>
		                    <?php $attribs['class'] = ''; 
		                endif;?>
	                    <div>
	                      <div class='feed_item_link_title'>
	                        <?php
	                          echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
	                        ?>
	                      </div>
	                      <div class='feed_item_link_desc'>
	                        <?php echo $this->viewMore($attachment->item->getDescription()) ?>
	                      </div>
	                    </div>
	                  </div>
	                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
	                  <div class="feed_attachment_photo">
	                    <?php
	                    echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, $thumb_main, $attachment->item->getTitle()), array('class' => 'feed_item_thumb ynmb_item_onephoto_full')) ?>
	                  </div>
	                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
	                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
	                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
	                <?php endif; ?>
	                </span>
              <?php endif;
              endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      
      <div id='comment-likes-activity-item-<?php echo $action->action_id ?>'>

      <?php // Icon, time since, action links ?>
      <?php
      	$detail_url = $this->url(array('action_id' => $action->action_id, 'comment' => 'on'),'mobi_feed', true);
        $canComment = ( $action->getTypeInfo()->commentable &&
            $this->viewer()->getIdentity() &&
            Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));
      ?>
      <div class='feed_item_date'>
        <a class="ynmb_static_likeComment" href="<?php echo $detail_url?>">
	        <span id="total_likes_activity_item_<?php echo $action->action_id ?>">
	        <?php if($action -> likes() -> getLikeCount()):?>
	        		<?php echo $this->translate(array('%s Like',' %s Likes', $action -> likes() -> getLikeCount()), $action -> likes() -> getLikeCount());?>
		        	 <?php if($action -> comments() -> getCommentCount() && $action -> likes() -> getLikeCount()):?>
		        		<span class="ynmb_dot">·</span>
		        	<?php endif;?>
	        <?php endif;?>
	        </span>
	       
	        <?php if($action -> comments() -> getCommentCount()):?>
	        <span id="total_comments_activity_item_<?php echo $action->action_id ?>">
	        	<?php echo $this->translate(array('%s Comment','%s Comments', $action -> comments() -> getCommentCount()), $action -> comments() -> getCommentCount());?>
	        </span>
	        <?php endif;?>
        </a>
        <ul>
          <?php if( $canComment ): ?>
            <?php if( $action->likes()->isLike($this->viewer()) ): ?>		  
			  <li class="feed_item_option_unlike" id = 'feed_item_option_like_<?php echo $action->action_id ?>'>
                <a href="javascript:void(0);" onclick="javascript:ynmobileview.unlike('<?php echo $action->action_id ?>');">
					<i class="feed_item_option_icon"> </i><strong><?php echo $this->translate("Like")?></strong>
				</a>
              </li>
            <?php else: ?>	  
			  <li class="feed_item_option_like" id = 'feed_item_option_like_<?php echo $action->action_id ?>'>
                <a href="javascript:void(0);" onclick="javascript:ynmobileview.like('<?php echo $action->action_id ?>');">
					<i class="feed_item_option_icon"> </i><strong><?php echo $this->translate("Like")?></strong>
				</a>
              </li>
			  
            <?php endif; ?>
            <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
              <li class="feed_item_option_comment">              	
                <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), '<i class="feed_item_option_icon"> </i>
					<strong>'. $this->translate("Comment").' </strong>', array(
                  'class'=>'smoothbox',
                )) ?>
              </li>		  
            <?php else: ?>
              <li class="feed_item_option_comment">              	
                <a href="<?php echo $detail_url?>">
					<i class="feed_item_option_icon"> </i><strong> <?php echo $this->translate("Comment")?> </strong>
				</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>
          <?php // Share ?>
          <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity()): ?>
            <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) && $attachment->item->getType() != 'music_playlist_song'): ?>
              <li class="feed_item_option_share">                
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), '<i class="feed_item_option_icon"> </i> <strong>'. $this->translate("Share").' </strong>', array('class' => 'smoothbox')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 2 && $subject->getType() != 'music_playlist_song'): ?>
              <li class="feed_item_option_share">                
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), '<i class="feed_item_option_icon"> </i> <strong>'. $this->translate("Share").' </strong>', array('class' => 'smoothbox')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 3 && $subject->getType() != 'music_playlist_song'): ?>
              <li class="feed_item_option_share">                
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), '<i class="feed_item_option_icon"> </i> <strong>'. $this->translate("Share").' </strong>', array('class' => 'smoothbox')) ?>
              </li>
            <?php elseif( $action->getTypeInfo()->shareable == 4 && $action->getType() != 'music_playlist_song'): ?>
              <li class="feed_item_option_share">                
                <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), '<i class="feed_item_option_icon"> </i> <strong>'. $this->translate("Share").' </strong>', array('class' => 'smoothbox')) ?>
              </li>
            <?php endif; ?>
          <?php endif; ?>
        </ul>
      </div>
      <!--</div> End of Comment-Likes -->
     <!-- Delete post-->
     <?php if( $this->viewer()->getIdentity() && (
        $this->activity_moderate || (
        ($this->viewer()->getIdentity() == $this->activity_group) || (
          $this->allow_delete && (
            ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
            ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
          )
        )
       )
		) ): ?>  
	  
		<a class="ynmb_optionDelete smoothbox" href="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->action_id),'default', true) ?>">
			<i class=""> <u><?php $this->translate('Delete') ?></u> </i>
		</a>
	  <?php endif; ?>
	  
	  
    </div>
  <?php if( !$this->noList ): ?></li><?php endif; ?>

<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>

<?php if( !$this->getUpdate ): ?>
</ul>
<?php endif ?>