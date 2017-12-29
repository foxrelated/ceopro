<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Discussions');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
			<?php echo $this->form->render($this);?>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
				'class' => 'buttonlink icon_back'
			)) ?>
			<?php if ($this->can_post) {
				echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'topic', 'action' => 'create', 'subject' => $this->group->getGuid()), $this->translate('Post New Topic'), array(
				'class' => 'buttonlink icon_group_post_new'
				)) ;
			}?>
		</div>
		
		<!-- Content -->
		<?php if( count($this->paginator) > 0 ): ?>
		<ul class="advgroup_discussions">
			<?php foreach( $this->paginator as $topic ):
				$owner = $topic->getOwner();
				$lastpost = $topic->getLastPost();
				$lastposter = $topic->getLastPoster();
			?>
			<li>
				<div class="advgroup_discussions_lastreply">
					<?php echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon')) ?>
					<div class="advgroup_discussions_lastreply_info">
						<b><?php echo $owner->__toString() ?></b>
					</div>
				</div>
				<div class="advgroup_discussions_replies">
					<span>
						<?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
					</span>
                    <?php echo $this->translate(array('advgroup_reply', 'replies', $topic->post_count - 1), $topic->post_count - 1) ?>
				</div>
				<div class="advgroup_discussions_info">
					<h3<?php if( $topic->sticky ): ?> class='advgroup_discussions_sticky'<?php endif; ?>>
					<?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
					</h3>
					<div class="advgroup_discussions_blurb" style="text-align: justify;">
						<?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
					</div>
					<?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Replied')) ?>
					<?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
					-
					<?php echo $this->timestamp(strtotime($topic->modified_date)) ?>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
		<div>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>
		<?php else:?>
		<div class="tip">
			<span>
				<?php echo $this->translate('No topics have been posted in this group yet.');?>
				<?php if($this->canCreate):?>
					<?php echo $this->translate('Create a %1$snew one%2$s',
					'<a href="'.$this->url(array('controller'=>'topic','action' => 'create','subject' =>$this->group->getGuid()), 'group_extended').'">', '</a>');?>
				<?php endif;?>
			</span>
		</div>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });
</script>