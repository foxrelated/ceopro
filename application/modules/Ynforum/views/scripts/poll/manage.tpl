<?php
	$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/core.js');   
       ?>
<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_header.tpl', 'ynforum', array('forum'=>$this -> forum, 'linkedCategories' => $this -> linkedCategories, 'navigationForums' => $this -> navigationForums, 'moderators' => $this -> moderators))?>  
	<div class="tabs navigation-over-list ynforum_intergrate_menus" >
	    <ul >	
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo $this->forum->getHref()?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>	    
		    <li >
	        	<a class="buttonlink icon_forum_all_polls" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_poll', true)?>"><?php echo $this -> translate("All Polls"); ?></a>
		    </li>	
		    <?php if($this -> forum -> checkPermission($this -> viewer ,'forum', 'fpoll.edit')):?>    
		    <li class="active">
		        <a class="buttonlink icon_forum_my_polls" href="<?php echo $this -> url(array('action' => 'manage', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_poll', true)?>"><?php echo $this -> translate("My Polls"); ?></a>
		    </li>
		    <?php endif; ?>
		    <?php if($this -> forum -> checkPermission($this -> viewer ,'forum', 'fpoll.create')):?>
		    	<li>
		        	<a class="buttonlink icon_forum_create_poll" href="<?php echo $this -> url(array('action' => 'create', 'forum_id' => $this -> forum -> getIdentity()), 'ynforum_poll', true)?>"><?php echo $this -> translate("Create Polls"); ?></a>
		    	</li>
		    <?php endif; ?>
		</ul>
	</div>
	<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="polls_browse">
      <?php foreach( $this->paginator as $poll ): ?>
      <li id="ynforum_poll_item_<?php echo $poll->poll_id ?>">
        <?php echo $this->htmlLink($poll->getHref(), $this->itemPhoto($poll->getOwner(), 'thumb.icon'), array('class' => 'polls_browse_photo')) ?>
        <div class="polls_browse_options">
        	<?php if (($this->viewer() && $poll->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fpoll.edit')): ?>
	          <?php echo $this->htmlLink(array(
	            'route' => 'ynforum_poll',
	            'action' => 'edit',
	            'poll_id' => $poll->poll_id,
	            'forum_id' => $this->forum->getIdentity(),
	            'reset' => true,
	            'format' => 'smoothbox',
	          ), $this->translate('Edit Privacy'), array(
	            'class' => 'buttonlink icon_poll_edit smoothbox'
	          )) ?>
          	<?php endif;?>
			
			<?php if (($this->viewer() && $poll->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fpoll.edit')): ?>
	          <?php if( !$poll->closed ): ?>
	            <?php echo $this->htmlLink(array(
	              'route' => 'ynforum_poll',
	              'action' => 'close',
	              'poll_id' => $poll->getIdentity(),
	              'closed' => 1,
	              'forum_id' => $this->forum->getIdentity(),
	              'format' => 'smoothbox',	              
	            ), $this->translate('Close Poll'), array(
	              'class' => 'buttonlink icon_poll_close smoothbox'
	            )) ?>
	          <?php else: ?>
	            <?php echo $this->htmlLink(array(
	              'route' => 'ynforum_poll',
	              'action' => 'close',
	              'poll_id' => $poll->getIdentity(),
	              'closed' => 0,	
	              'forum_id' => $this->forum->getIdentity(),     
	              'format' => 'smoothbox',         
	            ), $this->translate('Open Poll'), array(
	              'class' => 'buttonlink icon_poll_open smoothbox'
	            )) ?>
	          <?php endif; ?>
	         <?php endif;?>
	         
			<?php if (($this->viewer() && $poll->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fpoll.delete')): ?>
	          <?php echo $this->htmlLink(array(
	            'route' => 'ynforum_poll',
	            'action' => 'delete',
	            'poll_id' => $poll->getIdentity(),
	            'format' => 'smoothbox',
	            'forum_id' => $this->forum->getIdentity(),
	          ), $this->translate('Delete Poll'), array(
	            'class' => 'buttonlink smoothbox icon_poll_delete smoothbox'
	          )) ?>
          
          	<?php endif;?>
          	
          	<?php if (($this->viewer() && $poll->isOwner($this->viewer())) || $this->forum -> checkPermission($this->viewer, 'forum', 'fpoll.hlight')): ?>
	        	<?php
	            echo $this->htmlLink(array(
	                'route' => 'ynforum_poll',
	                'action' => 'highlight',
	                'poll_id' => $poll->getIdentity(),
	                'forum_id' => $this -> forum ->getIdentity()
	                ), $this -> forum -> checkEventHighlight($poll -> getIdentity(), 'poll') ? $this->translate('Un-highlight') : $this->translate('Highlight'), 
	                array(
	              'class' => $this -> forum -> checkEventHighlight($poll -> getIdentity(), 'poll')?'buttonlink ynforum_poll_highlight icon_ynforum_poll_unhighlight':'buttonlink ynforum_poll_highlight icon_ynforum_poll_highlight'
	            ));
				?>          
          	<?php endif;?>
          	
        </div>
        <div class="polls_browse_info">
          <h3 class="polls_browse_info_title">
            <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
            <?php if( $poll->closed ): ?>
			  <div><i class="fa fa-lock" aria-hidden="true"></i> <?php echo $this->translate('Closed') ?></div>
            <?php endif ?>
          </h3>
          <div class="polls_browse_info_date">
              <?php echo $this->translate('Posted by %s', $this->htmlLink($poll->getOwner(), $poll->getOwner()->getTitle())) ?>
              <?php echo $this->timestamp($poll->creation_date); ?>
              -
              <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
              -
              <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
          </div>
          <?php if( '' != ($description = $poll->getDescription()) ): ?>
            <div class="polls_browse_info_desc">
              <?php echo $description ?>
            </div>
          <?php endif; ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any polls.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array()); ?>
</div>
