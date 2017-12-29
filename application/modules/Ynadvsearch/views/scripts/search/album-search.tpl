<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
    jQuery.noConflict();
</script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/wookmark/jquery.imagesloaded.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynadvsearch/externals/scripts/wookmark/jquery.wookmark.js"></script>
<style type="text/css">
	.buttonlink.icon_photos_new.menu_advalbum_quick.advalbum_quick_upload{
		display:none;
	}
</style>
<?php 
$album_listing_id = 'advalbum_albums_listing';
?>
<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   if($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action'=>'album-search'),'ynadvsearch_search', true) ?>');
	   loadContents('');
	   if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('search'))
        {
            $('search').value = '<?php echo $this -> query?>';
        }
        <?php if ($this->advalbum_enable) : ?>
        var view_mode = getCookie('advalbum_albums_listingview_mode');
        if (!view_mode) setCookie('advalbum_albums_listingview_mode', 'list');
	    <?php endif; ?>   
	});
	
    var loadContents = function(url)
    {
        $('ynadvsearch_content_result').innerHTML = '';
        $('ynadvsearch_loading').style.display = '';
        <?php if ($this->advalbum_enable) : ?>
        var widget = 'ynadvsearch.yn-album-result';
        <?php else : ?>
        var widget = 'ynadvsearch.album-result';
        <?php endif; ?>
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/' + widget;
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                $('ynadvsearch_loading').style.display = 'none';
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
				ynsearchInitViewMode();
                <?php if ($this->advalbum_enable) : ?>
                ynadvalbumOptions();
                <?php endif; ?>
            }
        });
        request.send();

		function ynsearchInitViewMode() {
			var view_mode = getCookie('<?php echo $album_listing_id; ?>view_mode') || 'grid';
			ynsearchSetViewMode(view_mode);

			$$('#<?php echo $album_listing_id; ?> .ynsearch-listing-tab > div').addEvent('click', function() {
				ynsearchSetViewMode(this.get('data-view'));
			});
		}

		function ynsearchSetViewMode(view_mode) {
			$$('#<?php echo $album_listing_id; ?> .ynsearch-listing-tab > div').removeClass('active');
			$$('#<?php echo $album_listing_id; ?> .ynsearch-listing-tab > div.' + view_mode + '-view').addClass('active');
			$$('#<?php echo $album_listing_id; ?>_view').set('class', 'ynalbum-' + view_mode);

			if(view_mode == "pinterest" )
			{
				var handler = jQuery('#<?php echo $album_listing_id; ?>_tiles li.ynsearch_pinterest');
                var options = {
                    itemWidth: 220,
                    autoResize: true,
                    container: jQuery('#<?php echo $album_listing_id; ?>_tiles'),
                    offset: 25,
                    outerOffset: 0,
                    flexibleWidth: '50%'
                };
				// Breakpoint
				if ( jQuery(window).width() < 1024) {
					options.flexibleWidth = '100%';
				}
				setTimeout(function(){
				handler.wookmark(options);
				}, 50);
			}

			setCookie('<?php echo $album_listing_id; ?>view_mode', view_mode);
		}
    }
</script>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>
</script>

