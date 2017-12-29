<?php 
   $photoUrl = $this-> owner ->getPhotoUrl();
   if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_user_thumb_normal.png';
?>
<div class="ynblog_owner_info">
	<!-- Owner Photo -->
	<div class="ynblog_owner_photo">
	    <a href="<?php echo $this->owner->getHref(); ?>" style="background-image: url(<?php echo $photoUrl; ?>)"></a>
	</div>

	<!-- Owner Name -->
	<div class="ynblog_owner_name">
	<?php echo $this->htmlLink($this->owner->getHref(), 
	    $this->owner->getTitle(), 
	    array('class' => '')) ?>
	</div>
</div>
   