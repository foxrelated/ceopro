<?php if($this->currentPage <= $this->paginator->count()): ?>
<?php 
    $thumb_photo = 'thumb.profile';
?>
<?php if($this->view_mode == 'grid'): ?>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="ynbusinesspages_photo_items">
          <a  class="thumbs_photo" href="<?php echo $photo->getHref(); ?>"></a>
          <span class="ynbusinesspages_photo" style="background-image: url(<?php echo $photo->getPhotoUrl($thumb_photo); ?>);"></span>
        </div>
      </li>
    <?php endforeach;?>
    <?php if($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div id="ynbusiness_canloadmore" current_page="<?php echo $this->paginator->getCurrentPageNumber()+1; ?>">
            <div id="ynbusiness_loadmore"></div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <?php foreach( $this->paginator as $photo ): ?>
     <li class="ynbusinesspages_photo_items ynbusinesspages_pinterest" id="thumbs-photo-<?php echo $photo->photo_id ?>" class="swiper-slide">
       <div class="ynbusinesspages_photo_items_thumbs">
         <a class="ynbusinesspages_photo_items_temp" href="<?php echo $photo->getHref(); ?>"></a>
         <img class="ynadvalbum_pinteres_thumbs-photo" src="<?php echo $photo->getPhotoUrl($thumb_photo); ?>" />
       </div>
     </li>
    <?php endforeach;?>
<?php endif; ?>
<?php endif; ?>