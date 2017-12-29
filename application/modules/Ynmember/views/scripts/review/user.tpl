<div class="ynmember-review-user-avatar ynmember-clearfix">

	<div class="ynmember-review-user-main">
		<h3 class="ynmember-review-user-title">
			<?php
	         $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
	         $step = 900;
	         $select = $onlineTable -> select() -> where('user_id=?', (int)$this->subject() -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
	         $online = $onlineTable -> fetchRow($select);
	         if(is_object($online)): ?>
	     		<span class="ynmember-item-status online"></span>
	        <?php else:?>
	            <span class="ynmember-item-status off"></span>
	        <?php endif;?>

			<?php echo $this->htmlLink($this->subject()->getHref(), $this->subject()->getTitle(), array());?>
		</h3>		
        
		<div class="ynmember-review-user-option" id='profile_options'>
			<?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
			    echo $this->navigation()
			      ->menu()
			      ->setContainer($this->navigation)
			      ->setPartial(array('_navIcons.tpl', 'core'))
			      ->render()
			?>
		</div>
		
		<?php if ($this->studyplaces) :?>			
		<div class="ynmember-review-user-study ynmember-review-user-item">
			<span class="ynicon yn-graduation-cap"></span>
			<span><?php echo $this->translate("Studied at "); ?></span>
			<?php echo $this->studyplaces; ?>
		</div>
		<?php endif;?>
		
		<?php if ($this->workplaces) :?>			
		<div class="ynmember-review-user-work ynmember-review-user-item">
			<span class="ynicon yn-briefcase"></span>
			<span><?php echo $this->translate("Works at "); ?></span>
			<?php echo $this->workplaces; ?>
		</div>
		<?php endif;?>
		
		<?php if ($this->liveplaces) :?>
		<div class="ynmember-review-user-live ynmember-review-user-item">
			<span class="ynicon yn-map-marker"></span>
			<span><?php echo $this->translate("Lives in ");?></span>
			<?php echo $this->liveplaces; ?>
		</div>
		<?php endif;?>
		
		<?php if (count($this->groups)):?>
		<div class="ynmember-review-user-group ynmember-review-user-item">
			<span class="ynicon yn-users"></span>
			<span><?php echo $this-> translate("Member of group ");?></span>
			<?php foreach ($this->groups as $group) :?>
				<a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
				<?php break;?>
			<?php endforeach;?>
			<?php if (count($this->groups) > 1) : ?>
				<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('action' => 'user-group'),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($this->groups) - 1)), (count($this->groups) - 1 ))?></a>
			<?php endif;?>
		</div>
		<?php endif;?>

		<div class="ynmember-review-user-rating clearfix">
			<span class="ynmember-review-user-reviews">
				<?php echo $this->translate(array('%s review', '%s reviews', count($this->reviews)), $this->locale()->toNumber(count($this->reviews))) ?>
			</span>

			<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $this->subject()->rating));?>
		</div>
	</div>
	
</div>