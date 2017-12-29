<?php 
	$common_labels = array(
		'creation_date' => 'Added date',
		'owner' => 'Added by',
		'expiration_date' => 'Expired date'
	);
	
	$topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynmultilisting_listing');
	if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type') {
		$profileTypeField = $topStructure[0] -> getChild();
		$formArgs = array('topLevelId' => $profileTypeField -> field_id, 'topLevelValue' => $this->category -> option_id);
		$customFields = new Ynmultilisting_Form_Custom_Fields($formArgs);
		$customFields->setIsCreation(true);
		$elements = $customFields -> getElements();
		$customOptions = array();
		foreach ($elements as $elm) {
			$customOptions[$elm->getName()] = $elm->getLabel();
		}
	}
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        new Sortables('compare-listings', {
            contrain: false,
            clone: true,
            handle: 'li',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('controller'=>'compare','action'=>'sort'), 'ynmultilisting_compare') ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                        'category_id': '<?php echo $this->category->getIdentity()?>'
                    },
                }).send();
            }
        });
    });
</script>

<div id="ynmultilisting-compare-page">
    <div id="compare-header" class="ynmultilisting-clearfix">
        <?php if ($this->prevCategory !== false) :?>
        <?php $url = $this->url(array('category_id' => $this->prevCategory), 'ynmultilisting_compare', true);?>
        <div id="prev-button-div">
            <button id="prev-button" onclick="window.location = '<?php echo $url;?>'"><?php echo $this->translate('Prev')?></button>
        </div>
        <?php endif; ?>
        <?php if ($this->nextCategory !== false) :?>
        <?php $url = $this->url(array('category_id' => $this->nextCategory), 'ynmultilisting_compare', true);?>
        <div id="next-button-div">
            <button id="next-button" onclick="window.location = '<?php echo $url;?>'"><?php echo $this->translate('Next')?></button>
        </div>
        <?php endif; ?>
        <div id="select-category-div">
            <span><?php echo $this->translate('Comparison') ?></span>
            <?php $categories = Engine_Api::_()->ynmultilisting()->getAvailableCategories();?>
            <select id="select-category" onchange="changeCategory(this)">
                <?php foreach ($categories as $category) : ?>
                <option id="option-category_<?php echo $category->getIdentity()?>" <?php if ($category->getIdentity() == $this->category->getIdentity()) echo 'selected';?> value="<?php echo $category->getIdentity()?>"><?php echo $category->getTitle().' ('.Engine_Api::_()->ynmultilisting()->countComparelistingsOfCategory($category->getIdentity()).')'?></option>
                <?php endforeach;?>
            </select>
            <?php 
            if (!Engine_Api::_()->ynmultilisting()->countComparelistingsOfCategory($this->category->getIdentity()))
                Engine_Api::_()->ynmultilisting()->removeCompareCategory($this->category->getIdentity()); 
            ?>
        </div>
    </div>
    
    <div id="compare-main">
        <?php if (count($this->listings)) : ?>
        <div id="compare-fields-title-div">
            <ul id="compare-fields-title">
            <?php foreach ($this->comparison->common_fields as $common_field) : ?>
            	<?php if ($common_field != 'short_description') : ?>
				<li class="compare-fields-title" id="compare-fields-title-<?php echo $common_field?>"><?php echo (isset($common_labels[$common_field])) ? $common_labels[$common_field] : '';?></li>
           		<?php endif;?>
           	<?php endforeach;?>
           	
           	<?php if(!empty($this->comparison->rating_fields) && in_array('number_review', $this->comparison->review_fields)) : ?>
           		<li class="compare-fields-title" id="compare-fields-title-ratings"><?php echo $this->translate('Ratings & Reviews')?></li>
       		<?php elseif (!empty($this->comparison->rating_fields)) : ?>
           		<li class="compare-fields-title" id="compare-fields-title-ratings"><?php echo $this->translate('Ratings')?></li>
       		<?php elseif (in_array('number_review', $this->comparison->review_fields)) : ?>
           		<li class="compare-fields-title" id="compare-fields-title-ratings"><?php echo $this->translate('Reviews')?></li>
            <?php endif;?>
            <?php $hasReview = in_array('latest_review', $this->comparison->review_fields);?>
            <?php if ($hasReview) : ?>
            	<li class="compare-fields-title" id="compare-fields-title-reviews"><?php echo $this->translate('Latest Review')?></li>
           	<?php endif;?>
           	<?php if (in_array('short_description', $this->comparison->common_fields)) : ?>
           		<li class="compare-fields-title" id="compare-fields-title-short_description"><?php echo $this->translate('Short Description')?></li>
           	<?php endif;?>
           	<?php if (!empty($this->comparison->custom_fields)) : ?>
           		<li class="compare-fields-title compare-fields-title-header"><?php echo $this->translate('LISTING SPECIFICATIONS')?></li>
       		<?php foreach ($this->comparison->custom_fields as $custom_field) : ?>
       			<?php if (isset($customOptions[$custom_field])) : ?>
       			<li class="compare-fields-title" id="compare-fields-title-<?php echo $custom_field;?>"><?php echo $this->translate($customOptions[$custom_field])?></li>	
       			<?php endif; ?>
       		<?php endforeach;?>
       		<?php endif;?>
            </ul>
        </div>

        <div id="compare-fields-content-div">
            <ul id="compare-listings">
            <?php foreach ($this->listings as $listing) :?>
                <li id="compare-listing_<?php echo $listing->getIdentity()?>">
                    <div class="delete">
                        <a href="javascript:void(0)" onclick="deleteListingInCompare(this, <?php echo $listing->getIdentity();?>, <?php echo $this->category->getIdentity();?>)"><i class="fa fa-times"></i></a>
                    </div>
                    <ul class="compare-values">
                    <?php foreach ($this->comparison->common_fields as $common_field) : ?>
                    	<?php switch ($common_field) :
                    	case 'photo': ?>
                        <?php $photo_url = ($listing->getPhotoUrl('thumb.main')) ? $listing->getPhotoUrl('thumb.main') : "application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_main.png";?>
                    	<li class="compare-fields-content photo">
                            <div class="photo-background" style="background-image: url( '<?php echo $photo_url ?>' )">
                                 
                             </div> 
                    	</li>
                    	<?php break; ?>
                    	
                    	<?php case 'title': ?>
                    	<li class="compare-fields-content title">
                    		<?php echo $listing;?>
                    	</li>
                    	<?php break; ?>
                    	
                    	<?php case 'price': ?>
                    	<li class="compare-fields-content price">
                    		<?php echo $this->locale()->toCurrency($listing->price, $listing->currency);?>
                    	</li>
                    	<?php break; ?>
                    	
                    	<?php case 'owner': ?>
                    	<li class="compare-fields-content owner">
                    		<?php echo $listing->getOwner();?>
                    	</li>
                    	<?php break; ?>
                    	
                    	<?php case 'creation_date': ?>
                    	<li class="compare-fields-content creation-date">
                    		<?php echo $this->locale()->toDate($listing->getCreationDate())?>
                    	</li>
                    	<?php break; ?>
                    	<?php case 'expiration_date': ?>
                    	<li class="compare-fields-content expiration-date">
                    		<?php if ($listing->getExpirationDate()) echo $this->locale()->toDate($listing->getExpirationDate())?>
                    	</li>
                    	<?php break; ?>
						
                    	<?php endswitch; ?>
	           		<?php endforeach;?>
	           		
	           		<?php if(!empty($this->comparison->rating_fields) && in_array('number_review', $this->comparison->review_fields)) : ?>    
                    	<li class="compare-fields-content ratings">
                    	<?php foreach ($this->comparison->rating_fields as $field) : ?>
                    		<?php if ($field == 'overal_rating') : ?>
                    			<div class="listing-rating overall">
	                    			<span class="title"></span>
	                    			<span class="rating"><?php echo $this->partial('_compare_rating_big.tpl', 'ynmultilisting', array('value' => $listing->rating));?></span>
	                    			<span class="point"><?php echo $listing->rating ?></span>
                    			</div>
                    		<?php elseif ($rating = Engine_Api::_()->getItem('ynmultilisting_ratingtype', $field)) : ?>
                    			<div class="listing-rating">
	                    			<span class="title"><?php echo $this->translate($rating->getTitle())?></span>
	                    			<?php $ratingValue = Engine_Api::_()->getDbTable('ratingvalues', 'ynmultilisting')->getRatingOfType($field, $listing->getIdentity())?>
                                    <div class="rating-point">
	                    			    <span class="rating"><?php echo $this->partial('_compare_rating_big.tpl', 'ynmultilisting', array('value' => $ratingValue));?></span>
	                    			    <span class="point"><?php echo $ratingValue ?></span>
                                    </div>
                    			</div>
                    		<?php endif;?>
                    	<?php endforeach;?>
                    	<?php if (in_array('number_review', $this->comparison->review_fields)): ?>
                    		<div class="listing-review-count"><?php echo $this->translate(array('<span>%s</span> review', '<span>%s</span> reviews', $listing->review_count), $listing->review_count) ?></div>
                    	<?php endif; ?>
                    	</li>
                    <?php endif;?>
                    
                    <?php if ($hasReview) : ?>
		            	<li class="compare-fields-content latest-review">
		            	<?php $latestReview = $listing->getLatestReview()?>
		            	<?php if ($latestReview) :?>
		            		<?php 
		            		$owner = $latestReview->getOwner();
							$isEditor = Engine_Api::_() -> ynmultilisting() -> checkIsEditor($latestReview -> getListingType() -> getIdentity(), $owner);
		            		?>
		            		<div class="review_owner">
		            			<span class="review-rating-by"><?php echo $this -> translate('by');?></span>
			                        <span class="review-rating-username">
			                            <?php if($isEditor) :?>
			                            	<a href="<?php echo $owner -> getHref();?>"><?php echo $this -> translate('Editor');?></a>
			                            <?php else:?>
			                            	<?php echo $owner?>
			                            <?php endif;?>
		                        	</span>


		                	  	<span class="review-rating-date">
			                        <?php 
						        		$createdDateObj = new Zend_Date(strtotime($latestReview -> creation_date));	
										$createdDateObj->setTimezone($this->timezone);
										echo '&nbsp;-&nbsp;'.date('M d Y', $createdDateObj -> getTimestamp());
			                        ?>
		                        </span>
		            		</div>
		            		<div class="review_pros"> 
				            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Pros:');?></h5>
				                <p><?php echo $latestReview->pros?></p>
				            </div>
				            <div class="review_cons"> 
				            	<h5><?php echo '<i class="fa fa-caret-right"></i>'.$this -> translate(' Cons:');?></h5>
				                <p><?php echo $latestReview->cons?></p>
				            </div>
							
							<div class="review-field">
								<?php foreach($latestReview -> getReview() as $reviewValue) :?>
									<div class="review-table-rows">
										<h5><?php echo $this -> translate($reviewValue -> title);?></h5>
										<p><?php echo $reviewValue -> content;?></p>
									</div>
								<?php endforeach;?>
							</div>
							<div class="review_detail"> 
		                		<?php echo $latestReview->overal_review?>
		            		</div>
		            	<?php endif; ?>
		            	</li>
		           	<?php endif;?>
		           	
		           	<?php if (in_array('short_description', $this->comparison->common_fields)) : ?>
		           		<li class="compare-fields-content description"><?php echo $listing->getDescription();?></li>
		           	<?php endif;?>
           			<?php if (!empty($this->comparison->custom_fields)) : ?>
           				<li class="compare-fields-content compare-fields-title-header"></li>
		           	<?php foreach ($this->comparison->custom_fields as $custom_field) : ?>
	       				<?php if (isset($customOptions[$custom_field])) : ?>
	       				<li class="compare-fields-content custom-field">
	       				<?php 
	       				$field_id = substr($custom_field, strrpos($custom_field, '_') + 1);
	       				$field_value = $listing->getFieldValue($field_id);	
	       				echo $field_value;
	       				?>
	       				</li>	
	       				<?php endif; ?>
	       			<?php endforeach;?>
	       			<?php endif; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
            </ul>            
        </div>
        <?php else : ?>
            <?php echo $this->translate('No listings for comparison')?>
        <?php endif; ?>
    </div>
    
    <div class="back-btn">
    	<button type="button" onclick="window.history.back();"><?php echo $this->translate('Back')?></button>
    </div>
</div>

<script type="text/javascript">
    function changeCategory(obj) {
        var url = '<?php echo $this->url(array('action' => 'index'), 'ynmultilisting_compare', true)?>';
        window.location = url+'/index/category_id/'+obj.get('value');
    }
    
    function deleteListingInCompare(obj, id, category_id) {
        var url = '<?php echo $this->url(array('action' => 'remove-listing'),'ynmultilisting_compare', true)?>';
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'id': id,
                'category_id' : category_id,
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.status) {
                    obj.getParent('li').destroy();
                    var text = $('option-category_'+category_id).get('text');
                    var newText = text.replace(/\((\d)\)$/, "("+responseJSON.count+")");
                    $('option-category_'+category_id).set('text', newText);

                    autoHeightContent();
                }
                if(responseJSON.count == '0') {
                	window.location.replace("<?php echo $this -> url(array(), 'ynmultilisting_general' , true );?>");
                }
            }
        }).send();        
    };

    function autoHeightContent() {
        $$('#compare-fields-title > li').each(function(item, index) {
            item.erase('style');
        });

        $$('#compare-listings > li').each(function(item, index) {

            item.getElement('.compare-values').getElements('li').each(function(item, index){
                item.erase('style');               
            });    
        });

        $$('#compare-fields-title > li').each(function(item, index) {
            var index_div = index;
            var max_height = item.getSize().y;
            
            $$('#compare-listings > li').each(function(item, index) {
                item.getElement('.compare-values').getElements('li').each(function(item, index){
                    if ( index == index_div) {
                        if (max_height < item.getSize().y ) {
                            max_height = item.getSize().y;
                        }
                    }               
                });    
            });

            item.setStyle('height', max_height);
            $$('#compare-listings > li').each(function(item, index) {

                item.getElement('.compare-values').getElements('li').each(function(item, index){
                    if (index == index_div) {
                        item.setStyle('height', max_height);
                    }               
                });    
            });
        });
    }


    window.onload = function(e){ 
        $('compare-listings').setStyle('width', 220*$$('#compare-listings > li').length );   

        autoHeightContent();
    }
</script>