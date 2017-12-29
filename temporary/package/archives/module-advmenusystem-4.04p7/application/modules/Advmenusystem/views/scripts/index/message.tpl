
<?php 
  $coreVersion = Engine_Api::_()->getDbtable('modules', 'core')->select()
    ->from('engine4_core_modules', 'version')
    ->where('name = ?', 'core')
    ->query()
    ->fetchColumn();
 ?>
<?php if (count($this->messages)):?>
<script type="text/javascript">
function makeRead(actionid,href)
{
    window.location = href;
}
</script>
<?php endif;?>
<?php if (count($this->messages)): ?>
<div class="ynadvmenu_dropdownHaveContent">
	<ul class="ynadvmenu_Contentlist">
	<?php foreach ($this->messages as $item):
	        $message = $item->getInboxMessage($this->viewer());
	        $recipient = $item->getRecipientInfo($this->viewer());
			$resource = "";
			$sender   = "";
			$conversation = $item;
			if( $conversation->hasResource() &&
				($resource = $conversation->getResource()) ) {
				$sender = $resource;
			} else if( $conversation->recipients > 1 ) {
				$sender = $this->viewer();
			} else {
				foreach( $conversation->getRecipients() as $tmpUser ) {
					if( $tmpUser->getIdentity() != $this->viewer()->getIdentity() ) {
						$sender = $tmpUser;
					}
				}
			}
			if( (!isset($sender) || !$sender) && $this->viewer()->getIdentity() !== $conversation->user_id ){
				$sender = Engine_Api::_()->user()->getUser($conversation->user_id);
			}
			if( !isset($sender) || !$sender ) {
				//continue;
				$sender = new User_Model_User(array());
			}
		?>
		<li <?php if( !$recipient->inbox_read ): echo 'class = "ynadvmenu_Contentlist_unread"'; endif; ?>" onclick="makeRead(<?php echo $item->conversation_id;?>,'<?php echo $item->getHref();?>')" >
		<a onclick="makeRead(<?php echo $item->conversation_id;?>,'<?php echo  $item->getHref();?>')" class="fb_txt" href="javascript:;">
			<?php echo $this->itemPhoto($sender, 'thumb.icon');?>
		</a>
		<div class="ynadvmenu_ContentlistInfo">
			<div class="ynadvmenu_NameUser">
				<a style="text-decoration: none" onclick="makeRead(<?php echo $item->conversation_id;?>,'<?php echo $item->getHref();?>')" href="javascript:;"><?php echo $sender->getTitle()?></a>
			</div>
			<div class="ynadvmenu_MessDescription">
				<?php  ( '' != ($title = trim($message->getTitle())) ||
	                  '' != ($title = trim($item->getTitle())));
	                $title = html_entity_decode(strip_tags($title));
	                echo strlen($title)>50?substr($title,0,50).'...':$title;
	                ?>
	         </div>
	         <div class="timestamp ynadvmenu_postIcon notification_type_message_new <?php if (version_compare($coreVersion,'4.9.0' , '>=')) echo "ynse49"; ?>">
	         	<?php echo $this->timestamp($message->date)?>
	         </div>
	    </div> 
		</li>
	<?php endforeach;?>
	</ul>
</div>
<?php else:?>
<div class="ynadvmenu_dropdownNoContent">
	<?php echo $this->translate("You have no new messages.") ?>
</div>
<?php endif;?>
</ul>