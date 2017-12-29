<?php $users = $this->element->getAttrib('users');?>
<div id="users-wrapper" class="form-wrapper">
	<div id="users-label" class="form-label">
		<label for="users" class="required"></label>
	</div>
	<div id="users-element" class="form-element">
		<input type="hidden" name="users" value="">
		<ul class="form-options-wrapper">
			<?php foreach($users as $user):?>
			<li>
				<input type="checkbox" name="users[]" id="users-<?php echo $user -> getIdentity()?>" value="<?php echo $user -> getIdentity()?>">
				<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title'=>$user->getTitle()))?>
				<label for="users-<?php echo $user -> getIdentity()?>">
					<?php echo $this->htmlLink($user->getHref(), $this -> string() -> truncate($user->getTitle(), 20), array('title'=>$user->getTitle()))?>
				</label>
			</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>