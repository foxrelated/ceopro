<style type="text/css">
	.form-label{
		display: none;
	}
</style>
<form class='global_form_smoothbox' method="post" action="<?php echo ($this->url()) ?>">
<h3><?php  echo $this->translate('Select Themes')?></h3>
<br/>
<?php
$package = $this -> listing -> getPackage();

echo $this->partial('_post_listings_themes.tpl', array(
	'theme' => $this->listing->theme,
	'package' => $package,
	'select_theme' => 1,
));
?>
<br/>
<div style="padding-left: 30px;">
	<button type='submit'><?php echo $this->translate("Select") ?></button>
	<?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
	<a href="javascript:void(0)" onclick="parent.Smoothbox.close();">
	<?php echo $this->translate("cancel") ?></a>
</div>
</form>

<script type="text/javascript">
	  window.addEvent('domready', function() {
	  	$$('.item-form-theme-choose').addEvent('click', function(){
	  		this.getElements('input')[0].set('checked','true');
	  	});
	  });
</script>	
