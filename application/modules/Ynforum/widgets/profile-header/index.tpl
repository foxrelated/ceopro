<div class="forum_breadcumb">
	<div>
    <?php
        echo $this->partial('_navigation.tpl', array(
            'linkedCategories' => $this->linkedCategories,
            'navigationForums' => $this->navigationForums,
        ));
    ?>
   </div>
    <div class="forum_user_dashboard">
		<?php 
		echo $this->htmlLink(array(
			'callback_url' => base64_encode($this -> url()),						
			'route' => 'ynforum_dashboard'),
			$this->translate('User Dashboard'),
			array('class' => 'buttonlink icon_forum_user_dashboard'));
		?>	           
	</div>  
    <?php if ($this->canTopic) : ?>
        <div class="forum_addnew_button">
            <form action="<?php echo $this->forum->getHref(array('action' => 'topic-create'))?>" method="get">
                <button type="submit"><?php echo $this->translate('Add New Topic') ?></button>						
            </form>
        </div>
    <?php endif;?>
</div>  

<div>
	<ul class="forum_categories">
    <li>
	    <div>
	    	<?php 
		        echo $this->partial('_form_search.tpl', array(
		            'searchInSubForums' => $this->searchInSubForums,
		            'title' => $this->title));
		    ?>		
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
    </li>		
</div>
<div style="height: 20px" class="ynresforum-forum-view">
<?php
    if ($this->canApprove) {
        echo $this->htmlLink(array(
                'route' => 'ynforum_forum',
                'forum_id' => $this->forum->getIdentity(),
                'action' => 'view-not-approved-posts'), $this->translate("View unapproved posts"), 
            array('class' => 'buttonlink icon_forum_post_not_approved forum_post_not_approved'));
    }
    if ($this->viewer->getIdentity()) {
        if (!$this->isWatching) {
            echo $this->htmlLink(array(
                    'forum_id' => $this->forum->getIdentity(), 
                    'action' => 'watch', 
                    'watch' => 1,
                    'route' => 'ynforum_forum'), 
                $this->translate('Watch this forum'),
                array('class' => 'buttonlink icon_forum_topic_watch smoothbox'));
        } else {
            echo $this->htmlLink(array(
                    'forum_id' => $this->forum->getIdentity(), 
                    'action' => 'watch', 
                    'watch' => 0,
                    'route' => 'ynforum_forum'), 
                $this->translate('Stop watching this forum'),
                array('class' => 'buttonlink icon_forum_topic_unwatch smoothbox'));
        }
    }
?>
	<!-- AddThis Button BEGIN -->
	<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
    <!-- Go to www.addthis.com/dashboard to customize your tools -->  
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
	<!-- AddThis Button END -->
</div>
