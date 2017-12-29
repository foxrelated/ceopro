<?php $group = $this->group;
      $viewer = $this->viewer; ?>
      
<?php if($this->helpfulLink ):?>
	<?php
      echo $this->htmlLink(array('route' => 'group_link', 'action' => 'create', 'subject' => $this->subject()->getGuid()), $this->translate('Add New Link'), array(
    'class' => 'buttonlink icon_group_photo_new'
  )) ?>
<?php endif;?>

<?php if(count($this->paginator)>0):?>
  <ul>
        <?php foreach($this->paginator as $item):?>
        <li style="padding: 10px 0px 10px 10px;border-bottom: 1px solid #EAEAEA ;">
          <h3 style="padding-left: 10px;"><?php echo $item->title;?></h3>
          <p style="padding-left: 10px;"><?php echo $item->description?></p>
          <p style="padding-left: 10px;"><a href="<?php echo $item->link_content;?>" target="_blank">
             <b><?php echo $item->link_content;?></b>
          </a></p>
        </li>
        <?php endforeach;?>
  </ul>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No links have been added in this group yet.');?>
    </span>
  </div>
<?php endif;?>

