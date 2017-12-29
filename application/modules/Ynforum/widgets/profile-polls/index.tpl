<div class="ynforum_poll_item">
	<?php if ($this->paginator->getTotalItemCount()>0):?>	
		
		<ul class="polls_browse">
	      <?php foreach( $this->paginator as $poll ): ?>
	      <li id="poll-item-<?php echo $poll->poll_id ?>">
	        <?php echo $this->htmlLink($poll->getHref(), $this->itemPhoto($poll->getOwner(), 'thumb.icon'), array('class' => 'polls_browse_photo')) ?>
	        
	        <div class="polls_browse_info">
	          <h3 class="polls_browse_info_title">
	            <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
	            <?php if( $poll->closed ): ?>
				  <div><i class="fa fa-lock" aria-hidden="true"></i> <?php echo $this->translate('Closed') ?></div>
	            <?php endif ?>
	          </h3>
	          <div class="polls_browse_info_date">
	              <?php echo $this->translate('Posted by %s', $this->htmlLink($poll->getOwner(), $poll->getOwner()->getTitle())) ?>
	              <?php echo $this->timestamp($poll->creation_date) ?>
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
		
   <?php endif; ?>
    
    <ul class="ynforum_poll_item_navigation">
	    <li>
	        <a class="buttonlink icon_forum_all_polls" href="<?php echo $this -> url(array('forum_id' => $this -> forum -> getIdentity()), 'ynforum_poll', true)?>"><?php echo $this -> translate("All Polls"); ?></a>
	    </li>	
	    <?php if($this -> forum -> checkPermission($this -> viewer ,'forum', 'fpoll.edit')):?>    
	    <li>
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