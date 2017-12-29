<?php if (count($this->paginator) > 1):?>
<?php echo $this->translate("Your search returned too many results; only displaying the first 20.") ?>
<?php endif;?>
<?php foreach ($this->paginator as $user):?>
<?php if (!$this->object->isMember($user)):?>
  <li>
    <a href='javascript:addMember(<?php echo $user->getIdentity();?>);'><?php echo $user->getTitle();?></a>
  </li>
<?php endif;?>
<?php endforeach;?>