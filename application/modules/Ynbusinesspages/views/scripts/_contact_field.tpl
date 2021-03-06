<div class="form-wrapper">
	<div id="form_<?php echo $this -> field -> field_id;?>-label" class="form-label">
		<label for="form_<?php echo $this -> field -> field_id;?>"><?php echo $this -> field -> label;?></label>
	</div>
	<div id="form_<?php echo $this -> field -> field_id;?>-element" class="form-element">
		<div>
			<input type="checkbox" name="enable_fields[]" value="<?php echo $this -> field -> field_id;?>" <?php echo ($this -> field -> enabled) ? 'checked="checked"' :'';?> />
			<label><?php echo $this -> translate('Enable this field.');?></label>
			&emsp;&emsp;&emsp;&emsp;<a type="button" class="smoothbox ynicon yn-pencil" href="<?php echo  $this -> edit_href;?>"></a>
			&nbsp;&nbsp;<a type="button" class="smoothbox ynicon yn-minus" href="<?php echo  $this -> delete_href;?>"></a>
		</div>
		<div>
			<input type="checkbox" name="require_fields[]" value="<?php echo $this -> field -> field_id;?>" <?php echo ($this -> field -> required) ? 'checked="checked"' :'';?> />
			<label><?php echo $this -> translate('Set required this field.');?></label>
		</div>
	</div>
</div>
