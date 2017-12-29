<?php if ($this->view_mode == '1') : ?>
<div class="middle-categories-style-1">
	<div class="generic_list_widget">
	    <ul id="middle-categories-list" class="ynmultilisting-categories">
	    	<?php $count = 1;?>
	        <?php foreach ($this->categories as $category) : ?>
        	<?php if ($count > $this->limit) break;?>
	        <li>
	            <a style="display:block; background: url(<?php echo $category -> getImageUrl();?>)" href="<?php echo $category->getHref(); ?>">
	                <span class="title">
	                    <?php echo $this->string()->truncate($category->getTitle(), 20); ?>
	                </span>
	            </a>
	        </li>
	        <?php $count++;?>
	        <?php endforeach; ?>
	    </ul>
	</div>
	<div style="clear: both;"></div>
	<?php if (count($this->categories) > $this->limit):?>
		<a id="middle-categories-viewmore-btn" href="javascript:void(0)" onclick="showMore(<?php echo ($this->limit + $this->from)?>)"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('More Categories')?></a>
		<div id="middle-categories-loading" style="display: none; text-align: center">
			<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'/>
		</div>
		<script type="text/javascript">
		function showMore(from){
		    var url = '<?php echo $this->url(array('module' => 'core','controller' => 'widget','action' => 'index','name' => 'ynmultilisting.middle-categories'), 'default', true) ?>';
		    $('middle-categories-viewmore-btn').destroy();
		    $('middle-categories-loading').style.display = '';
		    var params = {};
		    params.format = 'html';
		    params.from = from;
		    params.itemCountPerPage = <?php echo $this->limit?>;
		    var request = new Request.HTML({
		      	url : url,
		      	data : params,
		      	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		        	$('middle-categories-loading').destroy();
		            var result = Elements.from(responseHTML);
		            var results = result.getElement('#middle-categories-list').getChildren();
		            $('middle-categories-list').adopt(results);
		            var viewMore = result.getElement('#middle-categories-viewmore-btn');
		            if (viewMore[0]) viewMore.inject($('middle-categories-list'), 'after');
		            var loading = result.getElement('#middle-categories-loading');
		            if (loading[0]) loading.inject($('middle-categories-list'), 'after');
		            eval(responseJavaScript);
		        }
		    });
		   request.send();
		  }
		
		</script>
	<?php endif;?>	
</div>

<?php else :?>
<div class="middle-categories-style-2">
	<div class="ynmultilisting_category_main clearfix" id="middle-categories-list">
	<?php $count = 1;?>
	<?php foreach ($this->categories as $category) :?>
	<?php if ($count > $this->limit) break;?>
	    <div class="browse_category">
	        <div class="item-category-browser">
	            <?php 
	                $num_listings = $category->getNumOfListings();
					$children = $category->getChildList();
					 $children_length = count($children);
	            ?>
	
	            <div class="category-browser-top">
	                <span class="category_icon"><?php echo $this->itemPhoto($category, 'thumb.icon')?></span>
	                <span class="category-stat"><?php echo '('. $num_listings .')'?></span>
	                <span class="category-title"><?php echo $this->htmlLink($category->getHref(), $category->getTitle())?></span>                
	            </div>
	            
	            <?php if (($children_length > 0) && ($children_length <= 5)) : ?>
	                <ul>
	                    <?php foreach($children as $child) : ?>
	                        <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $child->getTitle())?></li>
	                    <?php endforeach; ?>
	                </ul>
	            <?php elseif ($children_length > 5) : ?>
	                <ul>
	                    <?php 
	                        $i = 1;
	                        foreach($children as $child) : ?>
	                            <?php if ($i<=5) : ?>
	                                <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $child->getTitle())?></li>
	                            <?php endif; ?>
	                    <?php 
	                        $i++;
	                        endforeach; ?>
	                </ul>
	                <span class="btn-toggle-more-category"></span>
	                <div class="category-more-data">
	                    <ul>
	                        <?php 
	                            $i = 1;
	                            foreach($children as $child) : ?>
	                                <?php if ($i>5) : ?>
	                                    <li><span class="fa fa-angle-right"></span><?php echo $this->htmlLink($child->getHref(), $child->getTitle())?></li>
	                                <?php endif; ?>
	                        <?php 
	                            $i++;
	                            endforeach; ?>
	                    </ul>
	                </div>
	            <?php endif; ?>
	        </div>
	    </div>
	<?php $count++;?>
	<?php endforeach;?>
	</div>
	
	<script type="text/javascript">
	    window.addEvent('domready', function() {
	    	$$('.btn-toggle-more-category').removeEvents('click');
	        $$('.btn-toggle-more-category').addEvent('click', function(){
	            var category_item = this.getParent(),
	                layout_middle = $$('.layout_main .layout_middle')[0];
	
	            category_item.toggleClass('ynmultilisting-category-expand');
	
	            if ( category_item.hasClass('ynmultilisting-category-expand') ) {
	                var category_expand = category_item.getElement('.category-more-data');
	                layout_middle.setStyle(
	                    'min-height', category_expand.getSize().y + category_expand.getPosition().y - layout_middle.getPosition().y
	                );
	            } else {
	                layout_middle.erase('style'); 
	            }
	
	        }); 
	    });
	</script>
	
	<?php if (count($this->categories) > $this->limit):?>
		<a id="middle-categories-viewmore-btn" href="javascript:void(0)" onclick="showMore(<?php echo ($this->limit + $this->from)?>)"><i class="fa fa-arrow-down"></i> <?php echo $this->translate('More Categories')?></a>
		<div id="middle-categories-loading" style="display: none; text-align: center">
			<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif'/>
		</div>
		<script type="text/javascript">
		function showMore(from){
		    var url = '<?php echo $this->url(array('module' => 'core','controller' => 'widget','action' => 'index','name' => 'ynmultilisting.middle-categories'), 'default', true) ?>';
		    $('middle-categories-viewmore-btn').destroy();
		    $('middle-categories-loading').style.display = '';
		    var params = {};
		    params.format = 'html';
		    params.from = from;
		    params.itemCountPerPage = <?php echo $this->limit?>;
		    var request = new Request.HTML({
		      	url : url,
		      	data : params,
		      	onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
		        	$('middle-categories-loading').destroy();
		            var result = Elements.from(responseHTML);
		            var results = result.getElement('#middle-categories-list').getChildren();
		            $('middle-categories-list').adopt(results);
		            var viewMore = result.getElement('#middle-categories-viewmore-btn');
		            if (viewMore[0]) viewMore.inject($('middle-categories-list'), 'after');
		            var loading = result.getElement('#middle-categories-loading');
		            if (loading[0]) loading.inject($('middle-categories-list'), 'after');
		            eval(responseJavaScript);
		        }
		    });
		   request.send();
		  }
		
		</script>
	<?php endif;?>
</div>	
<?php endif;?>