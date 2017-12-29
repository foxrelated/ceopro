<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Videos');
			?>
		</h2>
	</div>
</div>

<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="search">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
				'class' => 'buttonlink icon_back'
			)) ?>
			<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller'=>'video','action'=>'manage','subject' => $this->subject()->getGuid()), $this->translate('My Videos'), array(
				'class' => 'buttonlink icon_group_video'
			)) ?>
			<?php
				 $ynvideo_enable = Engine_Api::_() -> advgroup() ->checkYouNetPlugin('ynvideo');
			 ?>
			<?php if( $this->canCreate ): ?>
				<?php 
					if($ynvideo_enable)
					{
						echo $this->htmlLink(array(
							'route' => 'video_general',
							'action' => 'create',
							'parent_type' =>'group',
							'subject_id' =>  $this->group->group_id,
						), $this->translate('Create New Video'), array(
						'class' => 'buttonlink icon_group_video_new'
						)) ;
					}
					else
					{
						echo $this->htmlLink(array(
							'route' => 'video_general',
							'action' => 'create',
							'parent_type' =>'group',
							'subject_id' =>  $this->group->getGuid(),
						), $this->translate('Create New Video'), array(
						'class' => 'buttonlink icon_group_video_new'
						)) ;
					}
				?>
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<?php if ($this->paginator->getTotalItemCount()> 0) : ?>
		<ul class="videos_browse" id="ynvideo_recent_videos">
			<?php foreach ($this->paginator as $item): ?>
			<li>
				<?php
					echo $this->partial('_video_listing.tpl', 'advgroup', array(
						'video' => $item,
						'infoCol' => $this->infoCol,
					));
				?>
			</li>
			<?php endforeach; ?>
		</ul>
		<br/>
		<div class ="ynvideo_pages">
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>      
		<?php else : ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('There is no video found.'); ?>
			</span>
		</div>
		<?php endif; ?>		
	</div>
</div>
<!-- Menu Bar -->


<script type="text/javascript">
  en4.core.runonce.add(function()
  {
	  if($('title'))
	    {
	      new OverText($('title'), 
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