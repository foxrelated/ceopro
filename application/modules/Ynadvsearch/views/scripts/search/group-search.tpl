
<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   if ($('filter_form')) {
            $('filter_form').set('action', '<?php echo $this->url(array('action' => 'group-search'),'ynadvsearch_search', true);?>');
        }
        else {
            if (($$('#global_page_ynadvsearch-search-group-search .filters')).length)
                $$('#global_page_ynadvsearch-search-group-search .filters')[0].set('action', '<?php echo $this->url(array('action' => 'group-search'),'ynadvsearch_search', true);?>');
        }
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        <?php if ($this->advgroup_enable) : ?>
        if($('text'))
        {
            $('text').value = '<?php echo $this -> query?>';
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
        <?php if ($this->advgroup_enable) : ?>
        var widget = 'ynadvsearch.yn-group-result';
        <?php else : ?>
        var widget = 'ynadvsearch.group-result';
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

                <?php if ($this->advgroup_enable):?>
                $('ynadvsearch_result').show();
                if(getCookie('view_mode')!= "")
                {
                    document.getElementById('advgroup_list_group').set('class',"advgroup_"+getCookie('view_mode')+"-view");
                    var map = getCookie('view_mode');                           
                    if(map == 'map')
                    {
                        advgroup_view_map_time();
                    }
                }
                else
                {
                    document.getElementById('advgroup_list_group').set('class',"<?php echo $this -> class_mode;?>");            
                }   

                if ( advgroup_list_group.get('class') == null ) {
                    advgroup_list_group.addClass('advgroup_list-view');
                }
                <?php endif; ?>
                
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
</script>
<?php if ($this->advgroup_enable):?>
<div id="ynadvsearch_result" style="display: none">
    <div class='count_results ynadvsearch-clearfix'>
        <span class="search_icon fa fa-search"></span>
        <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
        <span class="total_results">(<?php echo $this->total_content?>)</span>
        <span class="label_results"><?php echo $this->htmlLink(array('route' => 'group_general'), $this->label_content, array());?></span>
    </div>
</div>
<?php endif; ?>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>
