<?php $this->headScript()
    ->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/advgroup_function.js'); ?>
<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Albums Music');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="album_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
			'class' => 'buttonlink icon_back'
			)) ?>
			<?php if ($this->canCreate):?>
				<?php if ($this->ItemTable == 'music_playlist'): ?>
					<?php $session = new Zend_Session_Namespace('mobile');
					if(!$session -> mobile):?>
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
					<?php endif;?>
				<?php else: ?> 	
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
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<?php if( $this->paginator->getTotalItemCount() > 0 ): $group = $this->group;?>
		<ul class="thumbs advgroup_music">  	
		<?php if ($this->ItemTable == 'music_playlist'): ?>  		
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
		<?php else: ?>	
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
									$this->translate('Edit'),
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
									$this->translate('Delete'),
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
		<?php endif; ?>	
		</ul>  
		<?php if( $this->paginator->count() > 0 ): ?>
			<?php echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
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
  