<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
?>
<div class="generic_layout_container layout_middle">
	<?php echo $this->partial('_forum_dashboard.tpl', 'ynforum', array('callback_url'=>$this -> callback_url, 'signature' => 'active'))?>  
	<?php echo $this->form->render($this) ?>
</div>
