<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/wookmark/jquery.wookmark.js"></script>
<style type="text/css">
	.buttonlink.icon_photos_new.menu_advalbum_quick.advalbum_quick_upload{
		display: none;
	}
</style>
<?php 
	$photo_listing_id = 'advalbum_photos_listing';
?>
<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array("action"=>"photo-search"), "ynadvsearch_search", true) ?>');
	   loadContents('');
	   if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('search'))
        {
            $('search').value = '<?php echo $this -> query?>';
        }
        var view_mode = getCookie('advalbum_photos_listingview_mode');
        if (!view_mode) setCookie('advalbum_photos_listingview_mode', 'grid');  
    });
    
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/advalbum.photos-listing';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                $('ynadvsearch_loading').style.display = 'none';
                $('ynadvsearch_result').show();
                if($('ynadvsearch_content_result')) 
                {
                    $('ynadvsearch_content_result').innerHTML = responseHTML;
                }
                $$('.pages > ul > li > a').each(function(el)
                {
                    el.addEvent('click', function() {
                        var url = el.href;
                        el.href = 'javascript:void(0)';
                        loadContents(url);
                    });
                });
                jQuery.noConflict();
			    (function ($){
			    	
			          $('#<?php echo $photo_listing_id; ?>_tiles').imagesLoaded(function() {
			            
			            var options = {
			              itemWidth: 215,
			              autoResize: true,
			              container: $('#<?php echo $photo_listing_id; ?>_tiles'),
			              offset: 25,
			              outerOffset: 0,
			              flexibleWidth: '50%'
			            };
			    
			            // Get a reference to your grid items.
			            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li');
			    
			            var $window = $(window);
			            $window.resize(function() {
			              var windowWidth = $window.width(),
			                  newOptions = { flexibleWidth: '50%' };
			    
			              // Breakpoint
			              if (windowWidth < 1024) {
			                newOptions.flexibleWidth = '100%';
			              }
			    
			              handler.wookmark(newOptions);
			            });
			            
			            // Call the layout function.
			            handler.wookmark(options);
			         });
			         
			         $('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').click(function() {
			            var handler = $('#<?php echo $photo_listing_id; ?>_tiles li');
			            var options = {
			                  itemWidth: 215,
			                  autoResize: true,
			                  container: $('#<?php echo $photo_listing_id; ?>_tiles'),
			                  offset: 25,
			                  outerOffset: 0,
			                  flexibleWidth: '50%'
			            };
			            
			            // Breakpoint
			            if ( $(window).width() < 1024) {
			                options.flexibleWidth = '100%';
			            }
			                
			            $('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
			            $(this).addClass('active');
			                                    
			            $('#<?php echo $photo_listing_id; ?> .photo-list-content').attr('class', 'photo-list-content '+$(this).data('view') );
			            
			            if ( $(this).hasClass('grid-view') ) {
			            	
			                setCookie('<?php echo $photo_listing_id; ?>view_mode','grid');
			            }  
			           
			            if ( $(this).hasClass('pinterest-view') ) {
			                handler.wookmark(options);                
			                setCookie('<?php echo $photo_listing_id; ?>view_mode','pinterest');
			            }     
			         });
			         
			   		})(jQuery);
			            
					var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');	
					$$('#main_tabs li.tab_layout_<?php echo $photo_listing_id; ?>').addEvent('click', function(){
						if(view_mode == 'pinterest')
						{	
							var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
				            var options = {
			                  itemWidth: 215,
				                  autoResize: true,
				                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
				                  offset: 25,
				                  outerOffset: 0,
				                  flexibleWidth: '50%'
				            };
				            
				            // Breakpoint
				            if ( jQuery(window).width() < 1024) {
				                options.flexibleWidth = '100%';
				            }
							handler.wookmark(options);
						}		
					});
					if(getCookie('<?php echo $photo_listing_id; ?>view_mode') != "")
					{					
						var view_mode  = getCookie('<?php echo $photo_listing_id; ?>view_mode');				
						$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content ynalbum-'+ view_mode +'-view');			
						$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div').removeClass('active');
							
						if(view_mode == "grid" )
						{											
							$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.grid-view').addClass('active');
						}				
						if(view_mode == "pinterest" )
						{	
							$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.pinterest-view').addClass('active');
							var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
					            var options = {
					                  itemWidth: 215,
					                  autoResize: true,
					                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
					                  offset: 25,
					                  outerOffset: 0,
					                  flexibleWidth: '50%'
					            };
					            
					            // Breakpoint
					            if ( jQuery(window).width() < 1024) {
					                options.flexibleWidth = '100%';
					            }
								handler.wookmark(options);
						}							
					}
					else
					{						
						$$('#<?php echo $photo_listing_id; ?> .photo-list-content').set('class', 'photo-list-content '+'<?php echo $this->class_mode;?>');					
						$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.<?php echo $this->view_mode;?>-view').addClass('active');
						if("<?php echo  $this->view_mode ?>" == "pinterest" )
						{	
							$$('#<?php echo $photo_listing_id; ?> .ynalbum-listing-tab > div.pinterest-view').addClass('active');
							var handler = jQuery('#<?php echo $photo_listing_id; ?>_tiles li');
					            var options = {
					                  itemWidth: 215,
					                  autoResize: true,
					                  container: jQuery('#<?php echo $photo_listing_id; ?>_tiles'),
					                  offset: 25,
					                  outerOffset: 0,
					                  flexibleWidth: '50%'
					            };
					            
					            // Breakpoint
					            if ( jQuery(window).width() < 1024) {
					                options.flexibleWidth = '100%';
					            }
								handler.wookmark(options);
						}				
					
					}
				}			
        });
        request.send();
    }
</script>
<div id="ynadvsearch_result" style="display: none">
	<div class='count_results ynadvsearch-clearfix'>
		<span class="search_icon fa fa-search"></span>
		<span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->result_count),$this->result_count)?></span>
		<span class="total_results">(<?php echo $this->total_content?>)</span>
		<span class="label_results"><?php echo $this->htmlLink(array('route' => 'album_general'), ucfirst($this->label_content), array());?></span>
	</div>
</div>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>