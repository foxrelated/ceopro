<h2>
  <?php echo $this->group->__toString() ?>
  <?php echo $this->translate(' &#187; Manage Useful Links');?>
</h2>

<div class="advgroup_list">
  <div class="layout_middle">
    <div class="group_discussions_options" style="background-color: #E9F4FA">
      <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
        'class' => 'buttonlink icon_back'
      )) ?>
      <?php
          echo $this->htmlLink(array('route' => 'group_link', 'action' => 'create', 'subject' => $this->group->getGuid()), $this->translate('Add New Link'), array(
        'class' => 'buttonlink icon_group_photo_new'
      )) ?>
    </div>


<?php if(count($this->paginator)>0):
$session = new Zend_Session_Namespace('mobile');
$class_smoothbox = 'smoothbox';
if($session -> mobile)
{
	$class_smoothbox = '';
}
?>
<ul>
  <?php foreach( $this->paginator as $link ):?>
        <li class="ynadvgroup_manage_link">
          <div class='link_edit_options'>
            <?php echo $this->htmlLink(array(
              'route' => 'group_link',
              'action' => 'edit',
              'link_id' => $link->getIdentity(),
            ), $this->translate('Edit'), array(
              'class' => $class_smoothbox. ' buttonlink icon_group_edit',
            )) ?>
            <?php echo $this->htmlLink(array(
              'route' => 'group_link',
              'action' => 'delete',
              'link_id' => $link->getIdentity(),
            ), $this->translate('Delete'), array(
              'class' => 'smoothbox buttonlink icon_group_delete',
            )) ?>
          </div>
          <div class="ymb_link_info">
	          <h3 style="padding-left: 10px;padding-right: 122px;"><?php echo $link->title;?></h3>
	          <p style="padding-left: 10px;"><?php echo $link->description?></p>
	          <p><a href="<?php echo $link->link_content;?>" target="_blank">
	              <b style="padding-left: 10px;"><?php echo $link->link_content;?></b>
	          </a></p>
	       </div>
        </li>
  <?php endforeach; ?>
</ul>

  <?php if( $this->paginator->count() > 1 ): ?>
    <div>
      <?php echo $this->paginationControl($this->paginator) ?>
    </div>
  <?php endif; ?>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No links have been added in this group yet.');?>
    </span>
  </div>
<?php endif; ?>

  </div>
</div>