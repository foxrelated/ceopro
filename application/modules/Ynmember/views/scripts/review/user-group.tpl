<?php if (count($this->groups)) :?>
<ul class="generic_list_widget" style="min-width: 300px; padding: 15px; margin-bottom: 0px;">
<h3><?php echo $this -> translate('Groups');?></h3>
  <?php foreach( $this->groups as $group ): ?>
    <li>
      <div class="photo">
        <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.icon'), array('class' => 'thumb')) ?>
      </div>
      <div class="info">
        <div class="title">
          <h3>
          <?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
          </h3>
        </div>
        <div class="stats">
           <?php echo $this->translate('led by %1$s',
              $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle())) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php endif;?>