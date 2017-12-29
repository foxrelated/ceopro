<script type="text/javascript">
 en4.core.runonce.add(function(){
    var anchor = $('sub_groups').getParent();
    $('sub_groups_previous').style.display = '<?php echo ( $this->sub_groups->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('sub_groups_next').style.display = '<?php echo ( $this->sub_groups->count() == $this->sub_groups->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('sub_groups_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->sub_groups->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('sub_groups_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->sub_groups->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
  });	
</script>
<?php if($this->sub_mode):?>
  <h3><?php echo $this->translate("Sub Groups");?></h3>
<?php else:?>
  <h3><?php echo $this->translate("Parent Group");?></h3>
<?php endif;?>
<ul class="generic_list_widget" id="sub_groups" style="background:none; overflow: hidden; margin-bottom: 10px;">
	<?php
	foreach($this->sub_groups as $sub_group):
	$owner = $sub_group->getParent();
	?>
	<li>
		<div class="photo">
			<?php echo $this->htmlLink($sub_group->getHref(),$this->itemPhoto($sub_group, 'thumb.icon'))
			?>
		</div>
		<div class="info">
			<b style= "word-wrap:break-word"> <?php echo $this->htmlLink($sub_group->getHref(), Engine_Api::_()->advgroup()->subPhrase($sub_group->getTitle(),28));
			?></b>
			<br/>
			<?php echo $this -> translate("Owner");?>
			<?php echo $this->htmlLink($owner->getHref(), Engine_Api::_()->advgroup()->subPhrase($owner->getTitle(),28));?>
			<?php if($sub_group->getCategory()):?>
			<?php echo $this->translate('in').' '.$sub_group->getCategory() ?>	
			<?php endif;?>
		</div>
	</li>
	<?php endforeach;?>
</ul>
<div style="margin-bottom: 40px;">
  <div id="sub_groups_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="sub_groups_next" class="paginator_next" style="padding-top:0px;">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>