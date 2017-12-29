<?php
  // Render the admin js
  echo $this->render('_jsAdmin.tpl')
?>
<h2><?php echo $this->translate("YouNet Advanced Member Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div style='display: none'><?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?></div>
<h3><?php echo $this->translate('Review and Rating Settings') ?></h3>

<br /><br />

<hr style="border-width: 1px; width: 95%;" />
<span style='margin-top: -5px; float:right; display: block' class='review open_all_icon'>&nbsp&nbsp&nbsp&nbsp</span>
<span style='margin-top: -5px; float:right; display: none' class='review close_all_icon'>&nbsp&nbsp&nbsp&nbsp</span>
<h4 style="background-color: #fff; margin-top: -10px; width: 150px;"><?php echo $this->translate('Criteria of Review')?></h4>
<div id='add_review_field_wrapper'>
	</br>
	<div class="admin_fields_options">
	  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate("Add Question") ?></a>
	  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading">Add Heading</a>
	  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;">Save Order</a>
	</div>
	<br />
	<ul class="admin_fields">
	  <?php foreach( $this->secondLevelMaps as $map ): ?>
	    <?php echo $this->adminFieldMeta($map) ?>
	  <?php endforeach; ?>
	</ul>
</div>

<br /><br />

<hr style="border-width: 1px; width: 95%;" />
<span style='margin-top: -5px; float:right; display: block' class='rating open_all_icon'>&nbsp&nbsp&nbsp&nbsp</span>
<span style='margin-top: -5px; float:right; display: none' class='rating close_all_icon'>&nbsp&nbsp&nbsp&nbsp</span>
<h4 style="background-color: #fff; margin-top: -10px; width: 150px;"><?php echo  $this->translate('Criteria of Rating')?></h4>
</br>
<div id='add_rating_field_wrapper'>
	<input placeholder="Add New Rating Type..." type='text' id='add_rating_field' /> <span id='add_rating_field_button' class='add_rating_icon'>&nbsp&nbsp&nbsp&nbsp</span>
	<br />
	<?php foreach($this-> listRatingType as $item) :?>
		<div id='rating_type_row_<?php echo $item->getIdentity();?>' class='rating_type_row'>
			<input class='rating_type_row_input' type='text' data_id="<?php echo $item->ratingtype_id;?>" value="<?php echo $item->title;?>" />
			<span data-id='<?php echo $item->getIdentity();?>' class='delete_rating_icon_button delete_rating_icon'>&nbsp&nbsp&nbsp&nbsp</span>
		</div>
	<?php endforeach;?>
</div>

