<?php
  $this->headScript()
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.js')
    ->appendFile($this->baseUrl() . '/externals/moolasso/Lasso.Crop.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->baseUrl() . '/externals/tagger/tagger.js');
  $this->headTranslate(array(
    'Save', 'Cancel', 'delete',
  ));
?>

<script type="text/javascript">
  var taggerInstance;
  en4.core.runonce.add(function() {
    taggerInstance = new Tagger('media_photo_next', {
      'title' : '<?php echo $this->translate('ADD TAG');?>',
      'description' : '<?php echo $this->translate('Type a tag or select a name from the list.');?>',
      'createRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'deleteRequestOptions' : {
        'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
        'data' : {
          'subject' : '<?php echo $this->subject()->getGuid() ?>'
        }
      },
      'cropOptions' : {
        'container' : $('media_photo_next')
      },
      'tagListElement' : 'media_tags',
      'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
      'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
      'enableCreate' : <?php echo ( $this->canEdit ? 'true' : 'false') ?>,
      'enableDelete' : <?php echo ( $this->canEdit ? 'true' : 'false') ?>
    });

    // Remove the href attrib while tagging
    var nextHref = $('media_photo_next').get('href');
    taggerInstance.addEvents({
      'onBegin' : function() {
        $('media_photo_next').erase('href');
      },
      'onEnd' : function() {
        $('media_photo_next').set('href', nextHref);
      }
    });
    
  });
</script>


<h2>
  <?php echo $this->group->__toString() ?>
  <?php echo $this->translate('&#187;'); ?>
  <?php $albumFeature = Engine_Api::_()->getApi('settings','core')->getSetting('group.albumFeature',1);
    if($albumFeature == 0) {
      echo $this->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->group->getGuid(),
        ), $this->translate('Photos'));
    }
    else {
      echo $this->htmlLink($this->album->getAlbumHref(),$this->album->getTitle());
    }
    ?>
</h2>

<div class='layout_middle'>
<div class='ynadvgroup_photo_view'>
  <?php if( $this->photo->getTitle() ): ?>
    <h3 class="ynadvgroup_photo_title">
      <?php echo $this->photo->getTitle(); ?>
    </h3>
  <?php endif; ?>
  <div class="ynadvgroup_photo_owner">
    <?php echo $this->translate('By');?> <?php echo $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()) ?>
  </div>
  <div class='ynadvgroup_photo_info'>
    <div class='ynadvgroup_photo_container' id='media_photo_div'>
      <a id='media_photo_next'  href='<?php echo $this->photo->getNextCollectible()->getHref() ?>'>
        <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
          'id' => 'media_photo'
        )); ?>
      </a>
      <div class="ynadvgroup_photo_nav">
        <?php if ($this->album->count() > 1): ?>
          <?php echo $this->htmlLink($this->photo->getPrevCollectible()->getHref(), $this->translate('<i class="fa fa-chevron-left"></i>')) ?>
          <?php echo $this->htmlLink($this->photo->getNextCollectible()->getHref(), $this->translate('<i class="fa fa-chevron-right"></i>')) ?>
        <?php else: ?>
          &nbsp;
        <?php endif; ?>
      </div>
    </div>
    
    <div class="ynadvgroup_photo_date">
      <?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->creation_date) ?>
        <div class="ynadvgroup_photo_detail_option">
        <?php if( $this->canEdit ): ?>
          <a href='javascript:void(0);' onclick='taggerInstance.begin();'><?php echo $this->translate('<span class="ynicon yn-user-plus"></span>Add Tag');?></a>
          <i class="yn_dots">.</i>
          <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-gear"></span>Edit'), array('class' => 'smoothbox')) ?>
          <i class="yn_dots">.</i>

          <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-trash"></span>Delete'), array('class' => 'smoothbox')) ?>
          <i class="yn_dots">.</i>
          <?php endif; ?>

          <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-share"></span>Share'), array('class' => 'smoothbox')); ?>
          <i class="yn_dots">.</i>

          <?php echo $this->htmlLink(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=>$this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-warning-triangle"></span>Report'), array('class' => 'smoothbox')); ?>
          <i class="yn_dots">.</i>

          <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-photo-o"></span>Make Profile Photo'), array('class' => 'smoothbox')) ?>
        </div>
        <div class="ynadvgroup-options">
          <div class="ynadvgroup-options-button"><span class="ynicon yn-arr-down"></span></div>
          <ul class="ynadvgroup_dropdown_items">
          <?php if( $this->canEdit ): ?>
            <li class="ynadvgroup_dropdown_item"><a href='javascript:void(0);' onclick='taggerInstance.begin();'><?php echo $this->translate('<span class="ynicon yn-user-plus"></span>Add Tag');?></a></li>
            <li class="ynadvgroup_dropdown_item">
          <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-gear"></span>Edit'), array('class' => 'smoothbox')) ?></li>
            <li class="ynadvgroup_dropdown_item"><?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-trash"></span>Delete'), array('class' => 'smoothbox')) ?></li>
            <?php endif; ?>
            <li class="ynadvgroup_dropdown_item"><?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-share"></span>Share'), array('class' => 'smoothbox')); ?></li>
            <li class="ynadvgroup_dropdown_item"><?php echo $this->htmlLink(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=>$this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-warning-triangle"></span>Report'), array('class' => 'smoothbox')); ?></li>
            <li class="ynadvgroup_dropdown_item"><?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-photo-o"></span>Make Profile Photo'), array('class' => 'smoothbox')) ?></li>
          </ul>
        </div>
    </div>

    <?php if( $this->photo->getDescription() ): ?>
      <div class="ynadvgroup_photo_description">
        <span class="ynadvgroup_photo_detail_thumb-description-content"><?php echo $this->photo->getDescription() ?></span>
        <span class="ynadvgroup_photo_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?></span>
      </div>
    <?php endif; ?>

    <div>
      <?php echo $this->translate('Photo');?> <?php echo $this->photo->getCollectionIndex() + 1 ?>
      <?php echo $this->translate('of');?> <?php echo $this->album->count() ?>
    </div>
    
    <div class="ynadvgroup_photo_tags" id="media_tags" class="tag_list" style="display: none;">
      <?php echo $this->translate('Tagged:');?>
    </div>
    <div class="ynadvgroup_album_detail_addthis">
      <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
      <!-- Go to www.addthis.com/dashboard to customize your tools -->  
      <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
    </div>
  </div>

  <?php echo $this->action("list", "comment", "core", array("type"=>"advgroup_photo", "id"=>$this->photo->getIdentity())); ?>
</div>
</div>

<script type="text/javascript">
  $$('.core_main_ynadvgroup').getParent().addClass('active');

 window.addEvent('domready', function() {
        $$('.ynadvgroup_photo_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynadvgroup_photo_detail_thumb-description-content').toggleClass('ynadvgroup_photo_detail-description_toggle');
          if ($$('.ynadvgroup_photo_detail_thumb-description-content')[0].hasClass('ynadvgroup_photo_detail-description_toggle'))
            $$('.ynadvgroup_photo_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynadvgroup_photo_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });

            var hide = Object.getLength($$('.ynadvgroup_photo_detail_thumb-description-content'));
            if(hide != '0'){
              var height = $$('.ynadvgroup_photo_detail_thumb-description-content')[0].getHeight();
              if(height > 55){
                $$('.ynadvgroup_photo_detail_thumb-more').setStyle('display', 'inline-block');
                $$('.ynadvgroup_photo_detail_thumb-description-content').toggleClass('ynadvgroup_photo_detail-description_toggle');
              }
              else{
                $$('.ynadvgroup_photo_description').setStyle('height','74px');
              }
            }
            else{
                 $$('.ynadvgroup_photo_description').setStyle('display', 'none');
            }
        });

  window.addEvent('domready', function() {
    ynadvgroupOptions();
  });
</script>