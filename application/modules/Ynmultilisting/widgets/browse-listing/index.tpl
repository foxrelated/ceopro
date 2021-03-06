<?php
$this->headScript()
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.wookmark.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.imagesloaded.js');
?>

<div id="ynmultilisting_list_item_browse" class="<?php echo $this -> class_mode;?>">
    <div id="yn_listings_tabs_browse">
        <!--  Tab bar -->
        <ul id="yn_listings_tab_list_browse" class = "main_tabs">
            <li class="active">
                <a href="javascript:;" rel="tab_browse_listings" class="selected">
                    <?php if ($this->category) : ?>
                    <?php echo $this->category->title?>
                    <?php else: ?>
                    <?php echo $this->translate('Browse Listings');?>
                    <?php endif; ?>
                </a>
            </li>
        </ul>


    </div>
            <div class="ynmultilisting-action-view-method">
            <?php if(in_array('map', $this -> mode_enabled)):?>
            <div class="ynmultilisting_home_page_list_content" rel="map_view">
                <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
                <span id="map_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_map_view" title="<?php echo $this->translate('Map View')?>" onclick="ynmultilisting_view_map_browse();"></span>
            </div>
            <?php endif;?>
            <?php if(in_array('pin', $this -> mode_enabled)):?>
            <div class="ynmultilisting_home_page_list_content" rel="pin_view">
                <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Pin View')?></div>
                <span id="pin_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_pin_view" title="<?php echo $this->translate('Pin View')?>" onclick="ynmultilisting_view_pin_browse();"></span>
            </div>
            <?php endif;?>
            <?php if(in_array('grid', $this -> mode_enabled)):?>
            <div class="ynmultilisting_home_page_list_content" rel="map_view">
                <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
                <span id="grid_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_grid_view" title="<?php echo $this->translate('Grid View')?>" onclick="ynmultilisting_view_grid_browse();"></span>
            </div>
            <?php endif;?>
            <?php if(in_array('list', $this -> mode_enabled)):?>
            <div class="ynmultilisting_home_page_list_content" rel="map_view">
                <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
                <span id="list_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_list_view" title="<?php echo $this->translate('List View')?>" onclick="ynmultilisting_view_list_browse();"></span>
            </div>
            <?php endif;?>
        </div>

    <div id="ynmultilisting_list_item_browse_content" class="ynmultilisting-tabs-content ynclearfix">
        <div id="tab_listings_browse_listings">
            <?php
			echo $this->partial('_list_most_item.tpl', 'ynmultilisting', array('listings' => $this->paginator, 'tab' => 'listings_browse_listing'));
            ?>
        </div>
    </div>

    <script type="text/javascript">
    	jQuery.noConflict();
        var ynmultilisting_view_map_browse = function() {
        	if (document.getElementById('ynmultilisting_list_item_browse'))
            	document.getElementById('ynmultilisting_list_item_browse').set('class','ynmultilisting_map-view');
            var tab = $$('.layout_ynmultilisting_browse_listing #yn_listings_tab_list_browse li .selected')[0].get('rel');
            var html =  '<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->listingIds), 'ynmultilisting_general') ?>';
			$$('#ynmultilisting_list_item_browse_content #browse-iframe').destroy();
			
            var iframe = new IFrame({
                id : 'browse-iframe',
                src: html,
                styles: {
                    'width': '100%',
                    'height': 500
                }
            });

            iframe.inject($$('#ynmultilisting_list_item_browse_content')[0]);
            document.getElementById('browse-iframe').style.display = 'block';
            setCookie('browse_view_mode-<?php echo $this->identity ?>', 'map');
        }

        var ynmultilisting_view_pin_browse =  function()
        {
        	if (document.getElementById('ynmultilisting_list_item_browse'))
            	document.getElementById('ynmultilisting_list_item_browse').set('class','ynmultilisting_pin-view');
            setCookie('browse_view_mode-<?php echo $this->identity ?>','pin');

            jQuery.noConflict();
            (function (jQuery){
                var handler = jQuery('#ynmultilisting_list_item_browse .listing_pin_view_content li');

                handler.wookmark({
                    // Prepare layout options.
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: jQuery('#ynmultilisting_list_item_browse .listing_pin_view_content'), // Optional, used for some extra CSS styling
                    offset: 20, // Optional, the distance between grid items
                    outerOffset: 0, // Optional, the distance to the containers border
                    itemWidth: 220, // Optional, the width of a grid item
                    flexibleWidth: '50%'
                });

            })(jQuery);
        }

        var ynmultilisting_view_grid_browse =  function()
        {
        	if (document.getElementById('ynmultilisting_list_item_browse'))
            	document.getElementById('ynmultilisting_list_item_browse').set('class','ynmultilisting_grid-view');
            setCookie('browse_view_mode-<?php echo $this->identity ?>','grid');
        }

        var ynmultilisting_view_list_browse = function()
        {
    		if (document.getElementById('ynmultilisting_list_item_browse'))
            	document.getElementById('ynmultilisting_list_item_browse').set('class','ynmultilisting_list-view');
            setCookie('browse_view_mode-<?php echo $this->identity ?>','list');
        }
    </script>
</div>
<?php if( count($this->paginator) > 1 ): ?>
<?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues,
)); ?>
<?php endif; ?>

<script type="text/javascript">
	window.addEvent('domready', function(){
    	var view_mode = getCookie('browse_view_mode-<?php echo $this->identity ?>');
		if (view_mode == '') view_mode = '<?php echo $this->view_mode?>';
    	switch (view_mode) {
    		case 'map':
    			ynmultilisting_view_map_browse();
    			break;
			case 'pin':
    			ynmultilisting_view_pin_browse();
    			break;
			case 'grid':
    			ynmultilisting_view_grid_browse();
    			break;
			case 'list':
    			ynmultilisting_view_list_browse();
    			break;
    	}
		
		if ($('tab_listings_browse_listings')) {
			var imgLoad = imagesLoaded( document.querySelector('#tab_listings_browse_listings') );
	        imgLoad.on( 'done', function( instance ) 
	        {
	            var view_mode = getCookie('browse_view_mode-<?php echo $this->identity ?>');
	            if(view_mode == 'pin'){
	                ynmultilisting_view_pin_browse();
	            }
	        });
		}
    });
	
    function setCookie(cname,cvalue,exdays)
    {
        var d = new Date();
        d.setTime(d.getTime()+(exdays*24*60*60*1000));
        var expires = "expires="+d.toGMTString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname)
    {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++)
        {
            var c = ca[i].trim();
            if (c.indexOf(name)==0) return c.substring(name.length,c.length);
        }
        return "";
    }
</script>