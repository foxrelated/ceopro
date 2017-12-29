<div class="">
	<div class="album_others">
		<h3>
			<?php echo $this->translate('%1$s\'s Other Albums', $this->album->getOwner()->__toString());?>
		</h3>
	</div>
</div>
<?php
echo $this->partial('_albumlist-column.tpl', array(
	'arr_albums'=>$this->otherAlbums,
	'album_listing_id'=> $this->identity,
));
?>
