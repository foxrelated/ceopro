<?php
//add Script for HML5 player
	$this -> headScript() ->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/m2bmusic_class.js')
		->appendFile($this->baseUrl().'/externals/smoothbox/smoothbox4.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/jquery.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/noconflict.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/jquery-ui.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/slimScroll.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/mediaelement-and-player.min.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/mp3music.js');

$album = $this->album;
if(count($this->songs) > 0):?>
<script type="text/javascript">
	
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
    en4.core.runonce.add(function() {
      Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
      en4.core.loader = new Element('img', {src: 'application/modules/Core/externals/images/loading.gif'});
      en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
      <?php if( $this->subject() ): ?>
        en4.core.subject = {type:'<?php echo $this->subject()->getType(); ?>',id:<?php echo $this->subject()->getIdentity(); ?>,guid:'<?php echo $this->subject()->getGuid(); ?>'};
      <?php endif; ?>
    });
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
</script>
<div class = "mp3music_wrapper">
	<?php if(!$this->play): ?>
		<div class = "mp3music_album_thumb">
			<?php echo $this->itemPhoto($album, 'main') ?>
			<span class = "mp3music_expand_thumb"></span>
		</div>
	<?php endif;?>
	<div class="ynmobileviewmusic_container younet_mp3music_feed init" <?php if($this->play) echo "style = 'position:static; visibility:visible'"; ?>>
		<div class="mp3music_feed_player">
				<div class="mainplayer_image_album">  
						<?php echo $this->itemPhoto($album, 'main') ?>
				</div>
				<div class="album_info">
					<?php
						$title = $this->songs[0]['title'];
					?>
					<div class = "feed_player_right">
					<MARQUEE SCROLLDELAY="300">
						<span id="song-title-head"><?php echo $title;?></span>
					</MARQUEE>
					<audio class="yn-audio-skin mejs"  src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls="controls" <?php if($this->play) echo "autoplay";?> preload="none" width = "100%"></audio>
					</div>
				</div>				
		</div>
		
		<ul class="song-list mejs-list scroll-pane">
			<?php foreach ($this->songs as $index => $arSong):?>
			<?php 
				$title = $this->string()->truncate($arSong['title'], 20);
			?>
			<li class="<?php echo $index == 0 ? 'current': '';?>">
				<span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
				<span class="link"><?php echo $arSong['filepath'];?></span>
				<a href="javascript:void(0)" onclick="_addPlaylist(<?php echo $arSong['song_id']; ?>)" class="yn-add-playlist">
					<?php echo $this->translate('Add to playlist');?>
				</a>
				<div class = "mp3music-song-title">
					<span class="song-title"><?php echo ++$index .'. '. $title;?></span>
					<span class="yn-play"><?php echo "(".$this->translate(array('%s play','%s plays',$arSong['play_count']),$arSong['play_count']).")";?></span>
				</div>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php else:?>
<div class = "tip">
	<span><?php echo $this->translate('There are no songs uploaded yet');?></span>
</div>
<?php endif;?>
