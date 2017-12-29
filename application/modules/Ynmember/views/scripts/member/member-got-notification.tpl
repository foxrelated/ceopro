<?php if (count($this->users)):?>
<ul class="generic_list_widget ynmember_generic_list_widget">
<?php foreach ($this->users as $user) :?>
	<li>
	      <div class="photo">
	        <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'thumb', 'target' => '_blank')) ?>
	      </div>
	      <div class="info" style="margin-left: 0.5em; white-space: nowrap;">
	        <div class="title">
	          <?php echo $this->htmlLink($user->getHref(), $this->string()->truncate($user->getTitle(), 50), array('target' => '_blank')) ?>
	        </div>
	      </div>
	</li>
<?php endforeach;?>
</ul>
<?php endif;?>