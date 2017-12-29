<div class="form-wrapper form-ynmultilisting-choose-theme">
	<div class="form-label">
		<?php echo $this->translate('Select Themes')?>
	</div>
	<div class="form-element">
		
			<?php foreach($this->package->themes as $item) :?>
				<div class="item-form-theme-choose">
					<input <?php if($this->theme == $item) echo "checked='true'"?>  id='category_<?php echo $item?>' type='radio'  name='theme' value ='<?php echo $item?>'>
					<img width="50" src="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/<?php echo $item?>.png" >
					<?php if(!$this->select_theme) :?>
						<span class="btn-preview-theme" data-image="<?php echo $this->baseUrl();?>/application/modules/Ynmultilisting/externals/images/prev_<?php echo $item?>.jpg"><?php echo $this->translate('Preview Theme')?></span>
					<?php endif ;?>
				</div>
			<?php endforeach ;?>
	</div>
</div>
