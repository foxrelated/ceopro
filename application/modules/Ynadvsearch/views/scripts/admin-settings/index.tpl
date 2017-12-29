<h2>
  <?php echo $this->translate('YouNet Advanced Search Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<!-- admin main menu -->
<?php //echo $this->content()->renderWidget('socialstore.admin-main-menu') ?>
<div class='clear'>
  <div class='settings'>
<form id = "admin_search_mod" class="global_form" method="post" action="<?php echo $this->url()?>">
	<div>
	<h3><?php echo $this->translate('Manage Plugins')?></h3>
	<p class="form-description"><?php echo $this->translate("Tick on the box next to plugin that you want to appear in Search Filters column")?></p>
	<?php if ($this->saveSuccess) : ?>
		<ul class="form-notices">
			<li><?php echo $this->translate("Your changes have been saved.")?></li>
		</ul>
	<?php endif;?>
	<div class = "form-elements">
	<?php
	foreach($this->modules as $module) :
		if ($module->name == "ynshare" || $module->name == "ynsharebutton")
			continue;
	?>
	<div class = "admin_mod_checkbox">
		<input type="hidden" name="moduleynsearch[<?php echo $module->name?>]" value="0">
		<input type="checkbox" id = "moduleynsearch[<?php echo $module->name?>]" name="moduleynsearch[<?php echo $module->name?>]" value="1"  <?php echo ($module->enabled)? 'checked' : '';?>><?php if ($module->name == 'activity') echo $this->translate('Hash Tags'); else echo $module->title;?>
	</div>
	<?php endforeach;?>
	</div>
	<div class = "admin_mod_submit">
	<button name = "submit" id = "submit" type="submit"> <?php echo $this->translate('Save Changes')?> </button>
	</div>
	<div class = "admin_mod_check">
	<button name="btn" type="button" onclick="CheckAll()" value="Check All"> <?php echo $this->translate('Select All')?> </button>
	<button name="btn" type="button" onclick="UncheckAll()" value="Uncheck All"><?php echo $this->translate('Deselect All')?> </button>
	</div>
	</div>
</form>

  </div>
</div>
<style type="text/css">
.admin_mod_checkbox {
	float: left;
    padding-bottom: 10px;
    padding-right: 20px;
    width: 200px;
}
.admin_mod_submit {
	float: left;
	margin-top:10px
}
.admin_mod_check {
	float: right;
	margin-top:10px
}
</style>
<script type="text/javascript">

function CheckAll()
{
	var checkboxes = document.getElementById('admin_search_mod');
	for (var i =0; i < checkboxes.elements.length; i++)
	{
		checkboxes.elements[i].checked = true;
	}
}
function UncheckAll(){
	var checkboxes = document.getElementById('admin_search_mod');
	for (var i =0; i < checkboxes.elements.length; i++)
	{
		checkboxes.elements[i].checked = false;
	}
}
</script>