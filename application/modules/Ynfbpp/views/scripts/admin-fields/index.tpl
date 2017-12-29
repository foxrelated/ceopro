<script type ="text/javascript">
	function isNumber(event) {
		var charCode = (event.which) ? event.which : event.keyCode
		if(charCode > 31 && (charCode < 48 || charCode > 57))
			return false;

		return true;
	}
</script>
<?php
// Render the admin js
echo $this->render('_jsAdmin.tpl')
?>

<h2><?php echo $this->translate('Profile Popup Plugin')
?></h2>
<?php
if (count($this->navigation)):
?>
<div class='tabs'>
	<?php
// Render the menu
//->setUlClass()
echo $this->navigation()->menu()->setContainer($this->navigation)->render()
	?>
</div>
<?php endif;?>
<?php
if (isset($this->message)):
?>
<ul class="form-notices">
	<li>
		<?php echo $this->translate($this->message)
		?>
	</li>
</ul>
<?php endif?>

<div class="clear">
	<div class="settings">
		<form method="post" class="global_form" action="<?php
		echo $this->url(array('action' => 'index'), "admin_default") . "?" . http_build_query(array('option_id'=>$this->topLevelOption->option_id))
		?>" >
			<div>
				<div>
				<h3><?php echo $this->translate('User Fields Settings') ?></h3>
				<p class="form-description">
					<?php echo $this->translate('Only non-empty value will be shown.') ?>
				</p>
				<br />
				<p>
					<label for=""><?php echo $this->translate('Choose Profile Type')
						?></label>
					: <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions)
					?>
				</p>
				<br />
				<table class="admin_table">
					<thead>
						<tr>
							<th><?php echo $this->translate('Field Label')
							?></th>
							<th><?php echo $this -> translate('Enabled');?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($this->secondLevelMaps as $map):
							$meta = $map->getChild();
							if (!in_array($meta->type, $this->supportedFields)) {
								continue;
							}
							if (isset($meta->field_id)) : 
								$field = Engine_Api::_()->getDbTable("popup", "ynfbpp")->getField($meta->field_id);
						?>
							<tr>
								<td>
								<input type="hidden" name="field[<?php echo $meta->field_id ?>]" value="<?php echo $meta->field_id ?>">
								</input><strong><?php echo $meta -> label;?></strong></td>
								<td>
								<input type="checkbox" name="enabled[<?php echo $meta->field_id ?>]" value="<?php echo $meta->field_id ?>" <?php if ($field && $field->enabled == 1): ?>
								checked<?php endif?>/> </td>
								<td>
							</tr>
							<?php endif;?>
						<?php endforeach;?>
					</tbody>
				</table>
				<div class="button" style="margin-top: 5px;">
					<button type="submit" >
						<?php echo $this->translate("Save Changes")
						?>
					</button>
				</div>
				</div>
			</div>
		</form>
	</div>
	<table>
