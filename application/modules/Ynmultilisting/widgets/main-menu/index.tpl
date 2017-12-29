    <div class="headline">
        <h2>
        <?php echo $this -> translate($this -> listingType -> title);?>
        </h2>
        <div class="tabs ynmultilisting-menu-top">
        <ul class="navigation">
        <?php
		foreach( $this->navigation as $item ): 
		    $count ++;
		    $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
		        'reset_params', 'route', 'module', 'controller', 'action', 'type',
		        'visible', 'label', 'href'
		    ))); ?>
		    <li<?php echo($item->active?' class="active"':'')?>>
		    	<?php if ($item -> getLabel() == "My Listings") :?>
		    		<?php echo $this->htmlLink($item->getHref(), $this->translate("My %s", Engine_Api::_() -> ynmultilisting() -> getCurrentListingType() -> getTitle()), $attribs) ?>
		   		<?php elseif($item -> getLabel() == "Browse Listings") :?>
		   			 <?php echo $this->htmlLink($item->getHref(), $this->translate("Browse %s", Engine_Api::_() -> ynmultilisting() -> getCurrentListingType() -> getTitle()), $attribs) ?>
		   		<?php else :?>
		   			<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
		   		<?php endif;?>
			</li> 
		<?php  endforeach; ?>
		</ul>
        </div>
    </div>

