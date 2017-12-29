 <h2>
  <?php echo $this->translate('%1$s\'s Photos', $this->htmlLink($this->user->getHref(),$this->user->displayname)); ?>
</h2>
 <?php if($this->paginatorPhotos->getTotalItemCount() > 0): ?>
  <div class="generic_layout_container">
  <span style="font-weight: bold"><?php echo $this->translate('Photos of You'); ?>  </span>
  </div>
  <div  style="padding: 5px;">
  <ul class="thumbs thumbs_nocaptions">
<?php $index = 0; ?>
  <?php foreach( $this->paginatorPhotos as $photo ): if($index < 10): $index ++; ?>
   <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
   </li>
  <?php endif; endforeach; ?>
  </ul>

<?php  if($this->paginatorPhotos->getTotalItemCount() >= 10): ?>
   <div style="float: right; font-weight: bold; padding-right: 130px;">
                <a href="albums/tagphotouser/id/<?php echo $this->id; ?>" ><?php echo $this->translate('View more'); ?></a>
         </div>
<?php endif; ?>
  </div>
      <br/>
  <?php endif; ?>
<?php if($this->paginator->getTotalItemCount() <=  0 ): ?>
    <div class="tip" style="clear:left; width: 500px;">
      <span>
        <?php echo $this->translate('Nobody has created an album yet.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
    <?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
   <div class="generic_layout_container">
 <span style="font-weight: bold"><?php echo $this->translate('Your Photos'); ?>  </span>
  </div>
     <div style=" padding: 5px; " >
     <div >
    <ul class="thumbs">
      <?php foreach( $this->paginator as $album ): ?>
        <li>
          <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
          <p class="thumbs_info">
            <span class="thumbs_title">
              <?php echo $this->htmlLink($album, $this->string()->chunk(substr($album->getTitle(), 0, 45), 10)) ?>
            </span>
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            <br />
            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
          </p>
        </li>
      <?php endforeach;?>
    </ul>

    <?php if( $this->paginator->count() > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum")) ?>
    <?php endif; ?>
    </div>
</div>
  <?php endif; ?>