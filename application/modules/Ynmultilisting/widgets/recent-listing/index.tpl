<?php
$this->headScript()
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.wookmark.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.imagesloaded.js');
?>

<div id="ynmultilisting_list_item_browse-recent" class="<?php echo $this -> class_mode;?>">
    <div id="yn_listings_tabs_browse">
        <div class="ynmultilisting-action-view-method">

            <?php if(in_array('map', $this -> mode_enabled)):?>
                <div class="ynmultilisting_home_page_list_content" rel="map_view">
                    <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
                    <span id="map_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_map_view" title="<?php echo $this->translate('Map View')?>" onclick="ynmultilisting_view_map_browse<?php echo $this->identity ?>();"></span>
                </div>
            <?php endif;?>

            <?php if(in_array('pin', $this -> mode_enabled)):?>
                <div class="ynmultilisting_home_page_list_content" rel="pin_view">
                    <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Pin View')?></div>
                    <span id="pin_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_pin_view" title="<?php echo $this->translate('Pin View')?>" onclick="ynmultilisting_view_pin_browse<?php echo $this->identity ?>();"></span>
                </div>
            <?php endif;?>

            <?php if(in_array('grid', $this -> mode_enabled)):?>
                <div class="ynmultilisting_home_page_list_content" rel="map_view">
                    <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
                    <span id="grid_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_grid_view" title="<?php echo $this->translate('Grid View')?>" onclick="ynmultilisting_view_grid_browse<?php echo $this->identity ?>();"></span>
                </div>
            <?php endif;?>

            <?php if(in_array('list', $this -> mode_enabled)):?>
                <div class="ynmultilisting_home_page_list_content" rel="map_view">
                    <div class="ynmultilisting_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
                    <span id="list_view_<?php echo $this->identity;?>" class="ynmultilisting_home_page_list_content_icon tab_icon_list_view" title="<?php echo $this->translate('List View')?>" onclick="ynmultilisting_view_list_browse<?php echo $this->identity ?>();"></span>
                </div>
            <?php endif;?>

        </div>
    </div>

    <div id="ynmultilisting_list_item_browse-recent_content" class="ynmultilisting-tabs-content ynclearfix">
        <div id="tab_listings_browse_listings">
            <?php
                echo $this->partial('_list_most_item.tpl', 'ynmultilisting', array('listings' => $this->paginator, 'tab' => 'listings_browse_listing'));
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
	jQuery.noConflict();
    var ynmultilisting_view_map_browse<?php echo $this->identity ?> = function() {
        document.getElementById('ynmultilisting_list_item_browse-recent').set('class','ynmultilisting_map-view');
        var tab = '';
        if ($$('.layout_ynmultilisting_recent_listing #yn_listings_tab_list_browse li .selected')[0])
        {
            tab = $$('.layout_ynmultilisting_recent_listing #yn_listings_tab_list_browse li .selected')[0].get('rel');
        }
        
        var html =  '<?php echo $this->url(array('action'=>'display-map-view', 'ids' => $this->listingIds), 'ynmultilisting_general') ?>';
		$$('#ynmultilisting_list_item_browse-recent_content #browse-iframe').destroy();
		
        var iframe = new IFrame({
            id : 'browse-iframe',
            src: html,
            styles: {
                'width': '100%',
                'height': 500
            }
        });
        
        iframe.inject($$('#ynmultilisting_list_item_browse-recent_content')[0]);
        document.getElementById('browse-iframe').style.display = 'block';
        setCookie('browse_view_mode-<?php echo $this->identity ?>', 'map');
    }

    var ynmultilisting_view_pin_browse<?php echo $this->identity ?> =  function()
    {
        document.getElementById('ynmultilisting_list_item_browse-recent').set('class','ynmultilisting_pin-view');
        setCookie('browse_view_mode-<?php echo $this->identity ?>','pin');

        jQuery.noConflict();
        (function (jQuery){
            var handler = jQuery('#ynmultilisting_list_item_browse-recent .listing_pin_view_content li');

            handler.wookmark({
                // Prepare layout options.
                autoResize: true, // This will auto-update the layout when the browser window is resized.
                container: jQuery('#ynmultilisting_list_item_browse-recent .listing_pin_view_content'), // Optional, used for some extra CSS styling
                offset: 20, // Optional, the distance between grid items
                outerOffset: 0, // Optional, the distance to the containers border
                itemWidth: 220, // Optional, the width of a grid item
                flexibleWidth: '50%'
            });

        })(jQuery);
    }

    var ynmultilisting_view_grid_browse<?php echo $this->identity ?> =  function()
    {
        document.getElementById('ynmultilisting_list_item_browse-recent').set('class','ynmultilisting_grid-view');
        setCookie('browse_view_mode-<?php echo $this->identity ?>','grid');
    }

    var ynmultilisting_view_list_browse<?php echo $this->identity ?> = function()
    {
        document.getElementById('ynmultilisting_list_item_browse-recent').set('class','ynmultilisting_list-view');
        setCookie('browse_view_mode-<?php echo $this->identity ?>','list');
    }

</script>

<script type="text/javascript">
    window.addEvent('domready', function(){
    	renderViewMode<?php echo $this->identity ?>();
    	$$('.tab_layout_ynmultilisting_recent_listing a').addEvent('click', function() {
    		renderViewMode<?php echo $this->identity ?>();
    	});

        var imgLoad = imagesLoaded( document.querySelector('#tab_listings_browse_listings') );
        imgLoad.on( 'done', function( instance ) 
        {
            var view_mode = getCookie('browse_view_mode-<?php echo $this->identity ?>');
            if(view_mode == 'pin'){
                renderViewMode<?php echo $this->identity ?>();
            }
        });

    });
	
	function renderViewMode<?php echo $this->identity ?>() {
		var view_mode = getCookie('browse_view_mode-<?php echo $this->identity ?>');
		if (view_mode == '') view_mode = '<?php echo $this->view_mode?>';
    	switch (view_mode) {
    		case 'map':
    			ynmultilisting_view_map_browse<?php echo $this->identity ?>();
    			break;
			case 'pin':
    			ynmultilisting_view_pin_browse<?php echo $this->identity ?>();
                $("pin_view_<?php echo $this->identity;?>").click();
    			break;
			case 'grid':
    			ynmultilisting_view_grid_browse<?php echo $this->identity ?>();
    			break;
			case 'list':
    			ynmultilisting_view_list_browse<?php echo $this->identity ?>();
    			break;
    	}
	}

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
