<?php if($this -> error_msg):?>
	<?php echo $this->partial('_menu.tpl', array()); ?>
	<div class="tip">
		<span>
			<?php echo $this -> error_msg;?>
		</span>
	</div>
<?php endif;?>