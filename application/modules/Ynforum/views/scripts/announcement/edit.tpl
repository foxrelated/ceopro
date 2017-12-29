<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<div class="tabs navigation-over-list" style="margin-bottom: 15px">
	    <ul class="navigation">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>
		    <li class="active">
		        <a class="buttonlink icon_forum_manage_announcement" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Manage Announcements");?></a>
		    </li>
		    <li>
		        <a class="buttonlink icon_forum_create_announcement" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_announcement', true)?>"><?php echo $this -> translate("Create Announcements");?></a>
		    </li>
		</ul>
	</div>
	<br />
	 <?php echo $this->form->render($this) ?>
</div>