<div class="ynforum_announcement_item">
		<b><?php echo $this -> translate("Announcement");
			if($this -> announcement) echo ":";?></b>
		<?php if($this -> announcement):?>
		 <b> <?php echo $this -> announcement -> title;?></b>
			 <p>
		        <?php echo $this->htmlLink($this -> announcement->getOwner()->getHref(), $this -> announcement->getOwner()->getTitle());?>
	            <?php if($this -> announcement->getOwner()):
	           		$level = Engine_Api::_()->getItem('authorization_level', $this -> announcement->getOwner() -> level_id);
					echo "(".$level -> title.")";
				endif;           	
				?>
		    </p>
		    <p>
		      <?php echo $this -> announcement->body?>
		    </p>
   		<?php endif;?>
    <ul class="ynforum_announcement_item_navigation">
    	<?php if($this -> forum -> checkPermission($this -> viewer ,'forum', 'ynannoun.edit')):?>
	    <li>
	        <a class="buttonlink icon_forum_manage_announcement" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
	    </li>
	    <?php endif;?>
	    <?php if($this -> forum -> checkPermission($this -> viewer ,'forum', 'ynannoun.create')):?>
	    <li>
	        <a class="buttonlink icon_forum_create_announcement"  href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Create Announcements");?></a>
	    </li>
	    <?php endif;?>
	</ul>
</div>