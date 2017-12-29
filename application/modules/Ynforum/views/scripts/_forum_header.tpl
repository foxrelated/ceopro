<div class="ynforum_header"	>
	<div class="forum_breadcumb">
	    <?php
	        echo $this->partial('_navigation.tpl', array(
	            'linkedCategories' => $this->linkedCategories,
	            'navigationForums' => $this->navigationForums,
	        ));
	    ?>
	</div>
	<ul class="forum_categories" >
		<div>
		    <div class="forum_icon">
		        <?php echo $this->itemPhoto($this->forum, 'thumb.icon') ?>
		    </div>    
		    <h3> 
		        <?php echo $this->forum->getTitle() ?> 
		    </h3>
		    <p class="forum_boxarea_description"> 
		        <?php echo $this->forum->description ?> 
		    </p>	
		    <span> 
		        <?php if (count($this->moderators)) : ?>
		            <?php echo $this->translate('Moderated by') ?> 
		            <?php echo $this->fluentList($this->moderators) ?>,
		        <?php endif; ?>    
		        <?php echo $this->locale()->toNumber($this->forum->approved_topic_count)?> <?php echo $this->translate(array('topic', 'topics', $this->forum->approved_topic_count))?>,
		        &nbsp;<?php echo $this->locale()->toNumber($this->forum->approved_post_count)?> <?php echo $this->translate(array('post', 'posts', $this->forum->approved_post_count))?>,
		        &nbsp;<?php echo $this->translate(array('%1$s view', '%1$s views', $this->forum->view_count), $this->locale()->toNumber($this->forum->view_count))?>                    
		    </span>
		    		
		</div>
	</ul>
</div>