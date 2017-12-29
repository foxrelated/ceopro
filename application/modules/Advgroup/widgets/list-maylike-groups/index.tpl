<ul class="generic_layout_container" id="recent_group">
  <?php foreach( $this->groups as $item ): ?>
  <li>
    <div class="group_photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
    </div>
    <?php
    	$session = new Zend_Session_Namespace('mobile');
		if($session -> mobile)
		{
			$title = $item->getTitle();
			$owner_name = $item->getOwner()->getTitle();
		}
		else 
		{
			$title = Engine_Api::_()->advgroup()->subPhrase($item->getTitle(), 18);
        	$owner_name = Engine_Api::_()->advgroup()->subPhrase($item->getOwner()->getTitle(), 25);
		}
    ?>
    <div class="group_info" style="padding: 2px 0px 2px 0px">
         <?php echo "<b>".$this->htmlLink($item->getHref(),$title)."</b>"; ?>
         <div class="group_owner" style="font-size: 11px; color:#7E7E7E;">
           <?php echo $this->translate('led by %1$s',
              $this->htmlLink($item->getOwner()->getHref(),$owner_name)) ?>
           <br/>
           <?php echo $this->translate('in').' '.$item->getCategory() ?>
         </div>
       </div>
    </li>
  <?php endforeach; ?>
</ul>
