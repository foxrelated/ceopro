<h2>
  <?php echo $this->group->__toString() ?>
  <?php echo $this->translate('&#187; Photos');?>
</h2>
  <div class="group_discussions_options">
    <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
            'class' => 'buttonlink icon_back'
    )) ?>

    <?php if( $this->canUpload ): ?>
        <?php echo $this->htmlLink(array(
            'route' => 'group_extended',
            'controller' => 'photo',
            'action' => 'upload',
            'subject' => $this->subject()->getGuid(),
          ), $this->translate('Upload Photos'), array(
            'class' => 'buttonlink icon_group_photo_new'
        )) ?>
    <?php endif; ?>
  </div>
<div class='layout_middle'>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
  <ul class="thumbs thumbs_nocaptions">
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
      </li>
    <?php endforeach;?>
  </ul>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator); ?>
    <br />
  <?php endif; ?>
</div>