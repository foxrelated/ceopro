<?php	
$album = $this->album;
$album_id = 0;
//add Script for HML5 player
	$this -> headScript() ->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/m2bmusic_class.js')
		->appendFile($this->baseUrl().'/externals/smoothbox/smoothbox4.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/jquery.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/noconflict.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/jquery-ui.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/slimScroll.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/mediaelement-and-player.min.js')
		->appendFile($this->baseUrl().'/application/modules/Ynmobileview/externals/scripts/mp3music.js');

?>
<?php if(count($this->songs) <= 0): ?>
 <div class="tip">
      <span>
        <?php echo $this->translate('There are no songs uploaded yet.') ?>
      </span>
    </div>
<?php  else:?>
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
<div id = "mp3music_reverse">
<ul id = "ynmp3music-wrapper">
	<div id = "ynmp3music-inner">
    <h3 style="color: #464646;"><?php echo $album->title; ?></h3>
			<div  style="margin-bottom: 20px;">
				<div class="mp3_album_info_player" style="float: left; margin-right: 10px;">
				<?php echo $this->translate('Posted by %1s on %2s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle()),$this->timestamp($album->creation_date)) ?>
				</div>
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style">
					<a class="addthis_button_preferred_1"></a>
					<a class="addthis_button_preferred_2"></a>
					<a class="addthis_button_preferred_3"></a>
					<a class="addthis_button_preferred_4"></a>
					<a class="addthis_button_compact"></a>
				</div>
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4ed6f6272ab47b32"></script>
				<!-- AddThis Button END -->
			</div>
			<div>
				<p style="font-size: 9pt; margin-bottom: 10px" class="mp3_album_info" >
				 <?php echo $album->description; ?>
				</p>
			</div>
			<div class="mainplayer_image_album">  
				<?php echo $this->itemPhoto($album, 'thumb')?>
			</div>
     <!-- HTML5 -->
			<div class="ynmobileviewmusic_container younet_html5_player init">
				<div class="ynmobileview-music">
					<div class="song-info">
						<MARQUEE SCROLLDELAY="300">
								<span id="song-title-head"><?php echo $this->songs[0]['title'];?></span>
						</MARQUEE>
					</div>
					<audio class="yn-audio-skin" class="mejs" width="100%" src="<?php echo $this->songs[0]['filepath'];?>" type="audio/mp3" controls autoplay preload = "none"></audio>
				</div>
				<!-- Playlist -->
				<ul class="song-list mejs-list scroll-pane" id = "test_safari">
					<?php foreach ($this->songs as $index => $arSong):?>
						<li class="<?php echo $index == 0 ? 'current': '';?>">
							<span class="song_id" style="display: none;"><?php echo $arSong['song_id'];?></span>
							<span class="link"><?php echo $arSong['filepath'];?></span>
							
							<a href="javascript:void(0)" onclick="_addPlaylist(<?php echo $arSong['song_id']; ?>)" class="yn-add-playlist">
								<?php echo $this->translate('Add to playlist');?>
							</a>
							
							<div class = "mp3music-song-title">
								<span class="song-title"><?php echo ++$index.'. '.$this->string()->truncate($arSong['title'],20);?></span>
								<span class="yn-play"><?php echo "(".$this->translate(array('%s play','%s plays',$arSong['play_count']),$arSong['play_count']).")";?></span>
							</div>
						</li>
					<?php endforeach;?>
				</ul>
				<!-- End Playlist -->
			</div>
			<!-- END -->
	</div>
</ul>          
</div>
<?php 
	echo $this->action("list", "comment", "core", array("type"=>($this->type == 'album')?"mp3music_album":"mp3music_playlist", "id"=>$album_id));
?>
<?php endif; ?> 