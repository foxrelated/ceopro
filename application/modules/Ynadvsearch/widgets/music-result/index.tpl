<?php if( 0 == $this->paginator -> getTotalItemCount() ): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There were no musics found matching your search criteria.') ?>
		</span>
	</div>
<?php else: ?>
	<?php $count= 0;?>
	<ul class="music_browse">
		<?php foreach ($this->paginator as $playlist): ?>
			<?php $count++; ?>
			<li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
				<div class="music_browse_author_photo">
					<?php echo $this->htmlLink($playlist->getOwner(),
					$this->itemPhoto($playlist->getOwner(), 'thumb.profile') ) ?>
				</div>        
				<div class="music_browse_info">
					<div class="music_browse_info_title">
						<h3>
							<?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
						</h3>
					</div>
					<div class="music_browse_info_date">
						<div><?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
						</div>

						<div>
						<?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
						</div>

						<div>
						<?php echo $this->htmlLink($playlist->getHref(),  $this->translate(array('%s comment', '%s comments', $playlist->getCommentCount()), $this->locale()->toNumber($playlist->getCommentCount()))) ?>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
