<?php if( $this->subject()->cover_id !== null ): ?>
  <div>
    <?php $iMain = Engine_Api::_() -> getItem('storage_file', $this->subject() -> cover_id);
		$coverUrl = '';
    	if($iMain)
    	{
    		$coverUrl = $iMain -> map();
    	}
    	if($coverUrl):?>
    		<img height="100" src="<?php echo $coverUrl?>" />
    	<?php endif;?>
  </div>
<?php endif; ?>