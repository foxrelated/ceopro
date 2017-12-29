<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<!-- Event menus-->
	<div class="tabs navigation-over-list ynforum_intergrate_menus">
	    <ul class="navigation">
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>
		    <li>
		        <a class="buttonlink icon_forum_all_events" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("All Events");?></a>
		    </li>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fevent.edit')): ?>
		    <li>
		        <a class="buttonlink icon_forum_my_events" href="<?php echo $this -> url(array('action' => 'manage', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("My Events");?></a>
		    </li>
		     <?php endif;?>
		    <?php if ($this->forum -> checkPermission($this->viewer, 'forum', 'fevent.create')): ?>
		    <li>
		        <a class="buttonlink icon_forum_create_event" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><?php echo $this -> translate("Create Event");?></a>
		    </li>
		    <?php endif;?>
		</ul>
	</div>
	<?php if( $this->count > 0 ): ?>
	  <script type="text/javascript">
	    en4.core.runonce.add(function(){
	      $('selectall').addEvent('click', function(event) {
	        var el = $(event.target);
	        $$('input[type=checkbox]').set('checked', el.get('checked'));
	      });
	    });
	  </script>
	  <?php echo $this->form->render($this) ?>
	<?php else: ?>
		<h3 style="clear: both"><?php echo $this -> translate("Invite Forum Participators")?></h3>
	  <div style="margin-bottom: 20px">
	    <?php echo $this->translate('You have no members you can invite.');?>
	  </div>
	  <a href="<?php echo $this -> url(array('action' => 'manage', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_event', true)?>"><button><?php echo $this -> translate("Finish");?></button></a>
	<?php endif; ?>
</div>