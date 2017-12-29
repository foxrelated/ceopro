<?php if(count($this->arr_photos) > 0 ): ?>

  <ul class="thumbs" id="ynmobileview_profile_photos">
    <?php foreach( $this->arr_photos as $photo ): ?>
      <li id="ynmobileview_profile_photos_items">
        <div class="ynmobileview_profile_photos_items_thumb">
          <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>"></a>
          <span style="background-image: url(<?php echo $photo->getPhotoUrl(); ?>);"></span>
        </div>
      </li>
    <?php endforeach;?>
  </ul>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this user yet.');?>
    </span>
  </div>

<?php endif; ?>