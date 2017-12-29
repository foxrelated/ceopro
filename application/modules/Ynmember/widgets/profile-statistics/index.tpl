<?php if ($this->totalLikes):?>
<div class="ynmember_profile_statistics">
	<span class="ynicon yn-thumb-o-up"></span>
	<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'member-liked', 'id' => $this->subject->getIdentity()), 'ynmember_extended'); ?>">
	<?php echo $this->translate(array("%s member", "%s members", $this->totalLikes), $this->locale()->toNumber($this->totalLikes))?>
	</a>
	<?php echo " " .$this->translate("liked");?>
</div>
<?php endif;?>

<div class="ynmember_profile_statistics">
	<span class="ynicon yn-flag-waving"></span>
	<a class="smoothbox" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'member-got-notification', 'id' => $this->subject->getIdentity()), 'ynmember_extended'); ?>">
	<?php echo $this->translate(array("%s member", "%s members", $this->totalNotifications), $this->locale()->toNumber($this->totalNotifications))?>
	</a>
	<?php echo " " .$this->translate("wanted to get notification");?>
</div>
