<?php if (!is_null($this->inline) && $this->inline == true):?>
<span>
	<?php echo $this->translate("Was this useful?");?>
</span>
<span>
	<span>
		<?php if ($this->checked && $this->checked_value == '1') :?>
			<?php echo $this->translate("Yes")?>
		<?php else :?>
			<a href="javascript:void(0);" onclick="ynmember.set_useful(<?php echo $this->review_id?>, 1, <?php echo ($this->inline) ? 1: 0;?>)"><?php echo $this->translate("Yes")?></a>
		<?php endif;?>
		<span>
		<?php echo '<span>(' . $this->locale()->toNumber($this->yes_count) . ')</span>';?>
		</span>
	</span> -
	<span>
		<?php if  ($this->checked && $this->checked_value == '0') :?>
			<?php echo $this->translate("No")?>
		<?php else :?>
			<a href="javascript:void(0);" onclick="ynmember.set_useful(<?php echo $this->review_id?>, 0, <?php echo ($this->inline) ? 1: 0;?>)"><?php echo $this->translate("No")?></a>
		<?php endif;?>
		<?php echo '<span>(' . $this->locale()->toNumber($this->no_count) . ')</span>';?>
	</span>
</span>
<?php else:?>
<div>
	<?php echo $this->translate("Was this useful?");?>
</div>
<div>
	<span>
		<?php if ($this->checked && $this->checked_value == '1') :?>
			<?php echo $this->translate("Yes")?>
		<?php else :?>
			<a href="javascript:void(0);" onclick="ynmember.set_useful(<?php echo $this->review_id?>, 1, <?php echo ($this->inline) ? 1: 0;?>)"><?php echo $this->translate("Yes")?></a>
		<?php endif;?>
		<?php echo '<span>(' . $this->locale()->toNumber($this->yes_count) . ')</span>';?>
	</span> -
	<span>
		<?php if  ($this->checked && $this->checked_value == '0') :?>
			<?php echo $this->translate("No")?>
		<?php else :?>
			<a href="javascript:void(0);" onclick="ynmember.set_useful(<?php echo $this->review_id?>, 0, <?php echo ($this->inline) ? 1: 0;?>)"><?php echo $this->translate("No")?></a>
		<?php endif;?>
		 <?php echo '<span>(' . $this->locale()->toNumber($this->no_count) . ')</span>';?>
	</span>
</div>
<?php endif;?>