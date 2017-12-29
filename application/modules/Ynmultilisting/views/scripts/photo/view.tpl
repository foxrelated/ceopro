<?php
  $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/ynmultilisting.js') 
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js');

  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');

  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/tagger/tagger.js');
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
      'suggestParam' : <?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?>,
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
  <?php echo $this->listing->__toString() ?>
  <?php echo $this->translate('&#187;'); ?>
  <?php echo $this->album->__toString() ?>
  <?php echo $this->translate('&#187;'); ?>
  <?php echo $this->translate('Photos')  ?>
</h2>

<div class='layout_middle'>
  <div class='ynmultilisting_photo_view'>
    <?php if( $this->photo->getTitle() ): ?>
      <h3 class="ynmultilisting_photo_title">
        <?php echo $this->photo->getTitle(); ?>
      </h3>
    <?php endif; ?>
    <div class="ynmultilisting_photo_owner">
      <?php echo $this->translate('By');?> <?php echo $this->htmlLink($this->photo->getOwner()->getHref(), $this->photo->getOwner()->getTitle()) ?>
    </div>
    
    <div class='ynmultilisting_photo_info'>
      <div class='ynmultilisting_photo_container' id='media_photo_div'>
        <a id='media_photo_next'  href='<?php echo $this->photo->getNextCollectible()->getHref() ?>'>
          <?php echo $this->htmlImage($this->photo->getPhotoUrl(), $this->photo->getTitle(), array(
            'id' => 'media_photo'
          )); ?>
        </a>
        <div class="ynmultilisting_photo_nav">
        <?php if ($this->album->count() > 1): ?>
          <?php echo $this->htmlLink($this->photo->getPrevCollectible()->getHref(), $this->translate('<span class="ynicon yn-arr-left
"></span>')) ?>
          <?php echo $this->htmlLink($this->photo->getNextCollectible()->getHref(), $this->translate('<span class="ynicon yn-arr-right"></span>')) ?>
        <?php else: ?>
        <?php endif; ?>
    </div>
      </div>
      <div class="ynmultilisting_photo_date">
        <?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->creation_date) ?>
        <?php if($this -> viewer -> getIdentity()):?>
          <?php if( $this->canEdit ): ?>
            <div class="ynmultilisting_photo_detail_option">

            <?php echo $this->htmlLink(array('route' => 'ynmultilisting_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), '<span class="ynicon yn-gear"></span>'.$this->translate('Edit'), array('class' => 'smoothbox')) ?>
            <i class="yn_dots">.</i>

            <?php echo $this->htmlLink(array('route' => 'ynmultilisting_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), '<span class="ynicon yn-trash"></span>'.$this->translate('Delete'), array('class' => 'smoothbox')) ?>
            <i class="yn_dots">.</i>

          <?php endif; ?>
          <?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), '<span class="ynicon yn-share"></span>'.$this->translate('Share'), array('class' => 'smoothbox')); ?>
          <i class="yn_dots">.</i>

          <?php echo $this->htmlLink(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=>$this->photo->getGuid(), 'format' => 'smoothbox'), '<span class="ynicon yn-warning-triangle"></span>'.$this->translate('Report'), array('class' => 'smoothbox')); ?>
          <i class="yn_dots">.</i>

          <?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), '<span class="ynicon yn-photo-o"></span>'.$this->translate('Make Profile Photo'), array('class' => 'smoothbox')) ?>
          </div>
        <?php endif; ?> 
        <div class="ynmultilisting-options">
            <div class="ynmultilisting-options-button"><span class="ynicon yn-arr-down"></span></div>
            <ul class="ynmultilisting_dropdown_items">
                <li class="ynmultilisting_dropdown_item"><?php echo $this->htmlLink(array('route' => 'ynmultilisting_extended', 'controller' => 'photo', 'action' => 'edit', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-gear"></span>Edit'), array('class' => 'smoothbox')) ?></li>
                <li class="ynmultilisting_dropdown_item"><?php echo $this->htmlLink(array('route' => 'ynmultilisting_extended', 'controller' => 'photo', 'action' => 'delete', 'photo_id' => $this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-trash"></span>Delete'), array('class' => 'smoothbox')) ?></li>
                <li class="ynmultilisting_dropdown_item"><?php echo $this->htmlLink(Array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>$this->photo->getType(), 'id'=>$this->photo->getIdentity(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-share"></span>Share'), array('class' => 'smoothbox')); ?></li>
                <li class="ynmultilisting_dropdown_item"><?php echo $this->htmlLink(Array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=>$this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-warning-triangle"></span>Report'), array('class' => 'smoothbox')); ?></li>
                <li class="ynmultilisting_dropdown_item"><?php echo $this->htmlLink(array('route' => 'user_extended', 'module' => 'user', 'controller' => 'edit', 'action' => 'external-photo', 'photo' => $this->photo->getGuid(), 'format' => 'smoothbox'), $this->translate('<span class="ynicon yn-photo-o"></span>Make Profile Photo'), array('class' => 'smoothbox')) ?></li>
            </ul>
        </div>
      </div>

      <?php if( $this->photo->getDescription() ): ?>
        <div class="ynmultilisting_photo_description">
          <span class="ynmultilisting_photo_detail_thumb-description-content"><?php echo $this->photo->getDescription() ?></span>
          <span class="ynmultilisting_photo_detail_thumb-more"><?php echo $this->translate('<span>Show more<span class="ynicon yn-arr-down"></span></span>'); ?></span>
        </div>
      <?php endif; ?>

      <div>
        <?php echo $this->translate('Photo');?> <?php echo $this->photo->getCollectionIndex() + 1 ?>
        <?php echo $this->translate('of');?> <?php echo $this->album->count() ?>
      </div>
      
      <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
        <!-- Go to www.addthis.com/dashboard to customize your tools --> 
      <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script>
      
    </div>
  </div>
</div>
<script type="text/javascript">
  $$('.core_main_ynmultilisting').getParent().addClass('active');
  window.addEvent('domready', function() {
    ynmultilistingOptions();
  });
  window.addEvent('domready', function() {
        $$('.ynmultilisting_photo_detail_thumb-more').removeEvents('click').addEvent('click', function() {
          $$('.ynmultilisting_photo_detail_thumb-description-content').toggleClass('ynmultilisting_photo_detail-description_toggle');
          if ($$('.ynmultilisting_photo_detail_thumb-description-content')[0].hasClass('ynmultilisting_photo_detail-description_toggle'))
            $$('.ynmultilisting_photo_detail_thumb-more').set('html','<span>Show more <span class="ynicon yn-arr-down"></span></span>');
          else
            $$('.ynmultilisting_photo_detail_thumb-more').set('html','<span>Show less <span class="ynicon yn-arr-up"></span></span>');
        });

            var hide = Object.getLength($$('.ynmultilisting_photo_detail_thumb-description-content'));
            if(hide != '0'){
              var height = $$('.ynmultilisting_photo_detail_thumb-description-content')[0].getHeight();
              if(height > 55){
                $$('.ynmultilisting_photo_detail_thumb-more').setStyle('display', 'inline-block');
                $$('.ynmultilisting_photo_detail_thumb-description-content').toggleClass('ynmultilisting_photo_detail-description_toggle');
              }
              else{
                $$('.ynmultilisting_photo_description').setStyle('height','74px');
              }
            }
            else{
                 $$('.ynmultilisting_photo_description').setStyle('display', 'none');
            }
        });
</script>
<?php echo $this->action("list", "comment", "core", array("type"=>"ynmultilisting_photo", "id"=>$this->photo->getIdentity())); ?>