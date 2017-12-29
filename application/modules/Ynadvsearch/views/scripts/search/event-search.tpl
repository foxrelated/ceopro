
<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
    	if ($('filter_form')) {
            $('filter_form').set('action', '<?php echo $this->url(array('action' => 'event-search'),'ynadvsearch_search', true);?>');
        }
        else {
            if (($$('#global_page_ynadvsearch-search-event-search .filters')).length)
                $$('#global_page_ynadvsearch-search-event-search .filters')[0].set('action', '<?php echo $this->url(array('action' => 'event-search'),'ynadvsearch_search', true);?>');
        }
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        <?php if ($this->ynevent_enable) : ?>
        if($('keyword'))
        {
            $('keyword').value = '<?php echo $this -> query?>';
        }
        <?php else : ?>
        if($('search_text'))
        {
            $('search_text').value = '<?php echo $this -> query?>';
        }
        <?php endif; ?>
	});
	
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        <?php if ($this->ynevent_enable) : ?>
        var widget = 'ynadvsearch.yn-event-result';
        <?php else : ?>
        var widget = 'ynadvsearch.event-result';
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
                <?php if ($this->ynevent_enable) : ?>
                   var mode_view = getCookie('ynevent_mode_view');
                   if (mode_view) {
                       switch (mode_view) {
                           case 'map':
                                ynevent_view_map_time();
                                break;
                           case 'grid':
                                ynevent_view_grid_time();
                                break;
                           case 'list':
                                ynevent_view_list_time();
                                break;
                       }
                   }
                <?php endif; ?>
            }
        });
        request.send();
    }
</script>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>
</script>