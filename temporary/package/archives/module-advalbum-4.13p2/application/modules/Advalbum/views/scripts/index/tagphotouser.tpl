<h2>
  <?php echo $this->translate('%1$s\'s Photos', $this->htmlLink($this->user->getHref(),$this->user->displayname)); ?>
</h2>
 <?php if($this->paginatorPhotos->getTotalItemCount() > 0): ?>
  <div class="generic_layout_container">
  <span style="font-weight: bold"><?php echo $this->translate('Photos of You'); ?>  </span>
  </div>
  <div class="layout_middle" style="padding: 5px;">
  <ul class="thumbs thumbs_nocaptions">
  <?php foreach( $this->paginatorPhotos as $photo ): ?>
   <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
   </li>
  <?php endforeach; ?>
  </ul>
    <div style="float: left; font-weight: bold; padding-left : 5px;">
                <a href="albums/browsebyuser/id/<?php echo $this->id; ?>" ><?php echo $this->translate('Back '); ?></a>
         </div>
   <?php if( $this->paginatorPhotos->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
            array(
            'pageAsQuery' => false,
            'query' => $this->formValues
        )); ?>
    <?php endif; ?>
  </div>
  <?php endif; ?>