<?php if (count($this->paginator)) 
{
	$total = $this->paginator->getTotalItemCount();
	echo '<span class="ynbusinesspages_result_count">'.$total.'</span>';
	echo $this->translate(array('business', 'businesses', $total),$total);
}?>
<?php if( count($this->paginator) ): ?>
<div id="ynbusinesspages-browse-listings" class="ynbusinesspages-browse-company-viewmode-list">
	<ul>
	<?php foreach ($this->paginator as $business) :?>
		<li>
			<div class="ynbusinesspages-company-item">
				<div class="ynbusinesspages-company-item-image">
					<?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
				</div>

				<div class="ynbusinesspages-company-item-owner-action">
					<?php echo $this->htmlLink(array(
		              'action' => 'un-follow',
		              'business_id' => $business->getIdentity(),
		              'route' => 'ynbusinesspages_specific',
		              'reset' => true,
		            ), $this->translate('<i class="ynicon yn-arrow-right"></i> Unfollow'), array(
		              'class' => 'buttonlink smoothbox icon_ynbusinesspages_unfollow',
		            )) ?>
				</div>
				
				<div class="ynbusinesspages-company-item-content">
					<div class="ynbusinesspages-company-item-name"><a href='<?php echo $business -> getHref() ?>'><?php echo $business -> name;?></a></div>
					<div class="ynbusinesspages-company-item-statitics">
						<span><?php echo $this -> translate('Reviews: ') ;?></span>
						<b><?php echo $business -> getReviewCount() ;?></b>
						<span>|</span>
						<span><?php echo $this -> translate('Rating: ') ;?></span>
						<?php echo Engine_Api::_() -> ynbusinesspages() -> renderBusinessRating($business -> getIdentity(), false);?>
						<span>|</span>
						<span><?php echo $this -> translate('Members: ') ;?></span>
						<b><?php echo $business -> getMemberCount() ;?></b>
						<span>|</span>
						<span><?php echo $this -> translate('Followers: ') ;?></span>
						<b><?php echo $business -> getFollowerCount() ;?></b>
						
					</div>
				</div>
			</div>
		</li>	
	<?php endforeach;?>
	</ul>
	<div>
	    <?php echo $this->paginationControl($this->paginator, null, null, array(
	        'pageAsQuery' => true,
	        'query' => $this->formValues,
	    )); ?>
	</div>
</div>	
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no following businesses.') ?>
    </span>
  </div>
<?php endif; ?>