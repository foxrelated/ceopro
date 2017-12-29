<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route'=>'group_general'), $this->label_content, array());?></span>
</div>
<?php if( count($this->paginator) > 0 ): ?>

<ul class='groups_browse'>
  <?php foreach( $this->paginator as $group ): ?>
    <li>
      <div class="groups_photo">
        <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
      </div>
      <div class="groups_options">
      </div>
      <div class="groups_info">
        <div class="groups_title">
          <h3><?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?></h3>
        </div>
        <div class="groups_members">
          <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
          <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
        </div>
        <div class="groups_desc">
          <?php echo $this->viewMore($group->getDescription()) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] )): ?>
<div class="tip">
    <span>
    <?php echo $this->translate('There were no groups found matching your search criteria.');?>
    </span>
</div> 
    
<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('There were no groups found matching your search criteria.') ?>
    </span>
  </div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
  'query' => $this->formValues
)); ?>