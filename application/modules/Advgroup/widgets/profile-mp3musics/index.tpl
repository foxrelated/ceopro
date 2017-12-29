<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php if ($this->canCreate):?>
				<?php echo $this->htmlLink(array(
					'route' => 'group_mp3music_create_album',
					'module' => 'mp3music',
					'controller' => 'album',
					'action' => 'create',
					'subject_id' => $this->subject()->getGuid(),
					'parent_type' => 'group',
					), $this->translate('Create Album'), array(
					'class' => 'buttonlink icon_group_photo_new'
					))?>
			<?php endif; ?>
		</div>		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): 
		$group = $this->group;?>
		<ul class="thumbs advgroup_music">  			
			<?php foreach ($this->paginator as $album): ?>     	
			<li id="mp3music_album_item_<?php echo $album->getIdentity() ?>">
				<div class="mp3music_browse_info music_browse_info">
					<div class="photo">
						<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)">
							<?php if($album -> getPhotoUrl("thumb.profile")): ?>
								<span class="image-thumb" style="background-image:url('<?php echo $album -> getPhotoUrl("thumb.profile"); ?>')"></span>
							<?php else: ?>
								<span class="image-thumb" style="background-image:url('<?php echo $this->baseURL(); ?>/application/modules/Advgroup/externals/images/nophoto_music_playlist.png')"></span>
							<?php endif; ?>
						 </a> 
					</div>
					<div class="info">
						<div class="mp3music_browse_info_title title">					
						<?php if($album->getSongIDFirst($album->album_id)): ?>
							<a href="javascript:;" onClick="return openPage('<?php echo $this->url(array('album_id'=>$album->album_id), 'mp3music_album');?>',500,565)"><?php echo $album->getTitle() ?></a>
						<?php else: ?>
							<?php echo $album->getTitle() ?>
						<?php endif; ?>					
						</div>
						<div class="stats">
							<div class="author-name">
							<?php if(Engine_Api::_() -> advgroup() -> getSingers($album->album_id)): ?>
								<?php echo Engine_Api::_() -> advgroup() -> getSingers($album->album_id);?>
							<?php else: ?>
								<?php echo $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle()) ?>
							<?php endif; ?>
							</div>							
						</div>
					</div>
                    <div class="mp3music_browse_options music_browse_options">
						<?php if ($album->isDeletable() || $album->isEditable()): ?>
						<?php 
							$params = array(
							'subject_id' => $this->subject()->getGuid(),
							'parent_type' => 'group'
							) ;
						?>
							<ul>
								<?php if ($album->isEditable()): ?>
								<li>         	
								<?php echo $this->htmlLink($album->getEditHref($params),
									$this->translate(''),
									array('class'=>'buttonlink icon_mp3music_edit'
									)) ?>
								</li>
								<?php endif; ?>
								<?php if ($album->isDeletable()): ?>
								<li>
								<?php echo $this->htmlLink(array(
									'route' => 'group_extended',
									'module' => 'advgroup',
									'controller' => 'music',
									'action' => 'delete',
									'item_id' => $album->album_id,
									'group_id' => $group->getIdentity(),
									'type' => 'mp3music_album',
											),
									$this->translate(''),
									array('class'=>'buttonlink smoothbox icon_mp3music_delete'
								)) ?>
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
  