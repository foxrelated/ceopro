<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.wookmark.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynlistings/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type='text/javascript'>
	var params = <?php echo json_encode($this -> params); ?>;
    window.addEvent('domready', function() {
        if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'listing-search'),'ynadvsearch_search', true);?>');
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('listing_title'))
        {
            $('listing_title').value = '<?php echo $this -> query?>';
        }
        var view_mode = getCookie('browse_view_mode');
        if (!view_mode) setCookie('browse_view_mode', 'list');
    });
    
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/ynlistings.browse-listings';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                $('ynadvsearch_result').show();
                if($('ynadvsearch_content_result')) 
                {
                    $('ynadvsearch_content_result').innerHTML = responseHTML;
                    if(getCookie('browse_view_mode')!= "")
                    {
                        document.getElementById('ynlistings_list_item_browse').set('class',"ynlistings_"+getCookie('browse_view_mode')+"-view");
                        var map = getCookie('browse_view_mode');                       
                        if(map == 'map')
                        {
                            ynlistings_view_map_browse();
                        }
                    }
                    else
                    {
                        document.getElementById('ynlistings_list_item_browse').set('class',"<?php echo $this -> class_mode;?>");
                    }
                   if ( document.getElementById('ynlistings_list_item_browse').hasClass('ynlistings_pin-view') ) 
				   {
						$('ynadvsearch_content_result').hide();
						jQuery.noConflict();
						(function (jQuery){
							var handler = jQuery('#ynlistings_list_item_browse .listing_pin_view_content li');
				
							handler.wookmark({
							  // Prepare layout options.
							  autoResize: true, // This will auto-update the layout when the browser window is resized.
							  container: jQuery('#ynlistings_list_item_browse .listing_pin_view_content'), // Optional, used for some extra CSS styling
							  offset: 20, // Optional, the distance between grid items
							  outerOffset: 0, // Optional, the distance to the containers border
							  itemWidth: 220, // Optional, the width of a grid item
							  flexibleWidth: '50%',
							});
				
						  // Capture clicks on grid items.
						  handler.click(function(){
							// Randomize the height of the clicked item.
							var newHeight = jQuery('img', this).height() + Math.round(Math.random() * 300 + 30);
							jQuery(this).css('height', newHeight+'px');
				
							// Update the layout.
							handler.wookmark();
						});
						})(jQuery);
						setTimeout(loadPin, 2000);
					 }
					 else
					 {
						$('ynadvsearch_loading').style.display = 'none';
					 }
                    $$('#ynlistings_list_item_browse #yn_listings_tab_list_browse > li > a').each(function(el, idx){
                        el.addEvent('click', function(e){
                            $$('.ynlistings-action-view-method').show();
                            if(getCookie('browse_view_mode') != "")
                            {
                                var map = getCookie('browse_view_mode');                           
                                if(map == 'map')
                                {
                                    ynlistings_view_map_browse();
                                }
                                document.getElementById('ynlistings_list_item_browse').set('class',"ynlistings_"+getCookie('browse_view_mode')+"-view");
                            }
                            else
                            {                           
                                document.getElementById('ynlistings_list_item_browse').set('class',"<?php echo $this -> class_mode;?>");
                            }
            
                        });
                    });
                }
                
                $$('.pages > ul > li > a').each(function(el)
                {
                    el.addEvent('click', function() {
                        var url = el.href;
                        el.href = 'javascript:void(0)';
                        loadContents(url);
                    });
                });
            }
        });
        request.send();
    }
    var loadPin = function()
    {
		$('ynadvsearch_content_result').show();
		$('ynadvsearch_loading').style.display = 'none';
		jQuery.noConflict();
		(function (jQuery){
			var handler = jQuery('#ynlistings_list_item_browse .listing_pin_view_content li');

			handler.wookmark({
			  // Prepare layout options.
			  autoResize: true, // This will auto-update the layout when the browser window is resized.
			  container: jQuery('#ynlistings_list_item_browse .listing_pin_view_content'), // Optional, used for some extra CSS styling
			  offset: 20, // Optional, the distance between grid items
			  outerOffset: 0, // Optional, the distance to the containers border
			  itemWidth: 220, // Optional, the width of a grid item
			  flexibleWidth: '50%',
			});

		  // Capture clicks on grid items.
		  handler.click(function(){
			// Randomize the height of the clicked item.
			var newHeight = jQuery('img', this).height() + Math.round(Math.random() * 300 + 30);
			jQuery(this).css('height', newHeight+'px');

			// Update the layout.
			handler.wookmark();
		});
		})(jQuery);
    }
</script>

<div id="ynadvsearch_result" style="display: none">
    <div class='count_results ynadvsearch-clearfix'>
        <span class="search_icon fa fa-search"></span>
        <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
        <span class="total_results">(<?php echo $this->total_content?>)</span>
        <span class="label_results"><?php echo $this->htmlLink(array('route' => 'ynlistings_general'), $this->label_content, array());?></span>
    </div>
</div>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>
