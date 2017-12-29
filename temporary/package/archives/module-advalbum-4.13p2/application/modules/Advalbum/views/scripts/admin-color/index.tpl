<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2012 YouNetCo
 * @version    $Id: index.tpl
 * @author     Long Le
 */
?>
<h2>
  <?php echo $this->translate('YouNet Advanced Album Plugin') ?>
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

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>   
<style type="text/css">
#head_color_items-label,
#head_color_settings-label
{
	color: Black;
	text-transform: uppercase;
	margin-bottom: -10px;
}
.settings #clear-label 
{
	display: none;
}
#clear-wrapper
{
	clear: none;
}
#submit-wrapper
{
	float: left;
	padding-right: 10px;
}
</style>