<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php if ($this->canCreate):?>
				<?php echo $this->htmlLink(array(
				'route' => 'music_general',
				'module' => 'music',
				'controller' => 'index',
				'action' => 'create',
				'subject_id' => $this->subject()->getGuid(),
				'parent_type' => 'group',
				), $this->translate('Create Album'), array(
				'class' => 'buttonlink icon_group_photo_new'
				))
				?>
			<?php endif; ?>
		</div>		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): 
		$group = $this->group;?>
		<ul class="thumbs advgroup_music">  			
			<?php foreach ($this->paginator as $playlist): ?>
			<li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
				<div class="music_browse_info">
					<div class="photo">
						<?php if($playlist -> getPhotoUrl("thumb.profile")): ?>
							<span class="image-thumb" style="background-image:url('<?php echo $playlist -> getPhotoUrl("thumb.profile"); ?>')"></span>
						<?php else: ?>
							<span class="image-thumb" style="background-image:url('<?php echo $this->baseURL(); ?>/application/modules/Advgroup/externals/images/nophoto_music_playlist.png')"></span>
						<?php endif; ?>
					</div>
					<div class="info">
						<div class="music_browse_info_title title">
							<?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
						</div>
						<div class="stats">
							<div class="author-name">
								<?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
							</div>							
						</div>
					</div>
                    <div class="mp3music_browse_options music_browse_options">
						<?php if ($playlist->isDeletable() || $playlist->isEditable()): ?>
						<ul>
						<?php if ($playlist->isEditable()): ?>
							<li>
							  <?php echo $this->htmlLink($playlist->getHref(array('route' => 'music_playlist_specific', 'action' => 'edit', 'subject_id' => $this->subject()->getGuid(),'parent_type' => 'group')),
								$this->translate('Edit Playlist'),
								array('class'=>'buttonlink icon_music_edit'))
							  ?>
							</li>
						<?php endif; ?>
						<?php if ($playlist->isDeletable()): ?>
							<li>
							<?php echo $this->htmlLink(array(
								'route' => 'group_extended',
								'module' => 'advgroup',
								'controller' => 'music',
								'action' => 'delete',
								'item_id' => $playlist->playlist_id,
								'group_id' => $group->getIdentity(),
								'type' => 'music_playlist',
							),
							$this->translate('Delete Playlist'),
							array('class'=>'buttonlink smoothbox icon_music_delete'))
						  ?>
							</li>
						<?php endif; ?> 
						</ul>
						<?php endif; ?>
					</div>
				</div>	        
			</li>	      
			<?php endforeach; ?>			 
		</ul>  
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
			)); ?>
		<?php endif; ?>
		<?php else: ?>
		<div class="tip">
			<span>
			  <?php echo $this->translate('No albums music have been uploaded.');?>
			  <?php if($this->canUpload):?>
			  <?php echo $this->translate('Create a %1snew one%2s.',
					'<a href="'.$this->url(array('controller'=>'music','action' => 'create','subject' =>$this->group->getGuid()), 'group_extended').'">', '</a>');?>
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
  