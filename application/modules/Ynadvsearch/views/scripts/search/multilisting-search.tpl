<?php
$this->headScript()
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.wookmark.min.js')
->appendFile($this->baseUrl() . '/application/modules/Ynmultilisting/externals/scripts/wookmark/jquery.imagesloaded.js');
?>
<script type='text/javascript'>
	var smoothbox = this.Smoothbox;
	var params = <?php echo json_encode($this -> params); ?>;
    window.addEvent('domready', function() {
        if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'multilisting-search'),'ynadvsearch_search', true);?>');
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('listing_title'))
        {
            $('listing_title').value = '<?php echo $this -> query?>';
        }
        // var view_mode = getCookie('browse_view_mode');
        // if (!view_mode) setCookie('browse_view_mode', 'list');
    });
    
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/ynmultilisting.browse-listing';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        if (!('listingtype_id' in ajax_params)) {
			ajax_params.listingtype_id = 0;
		}
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('ynadvsearch_loading').style.display = 'none';
                if($('ynadvsearch_content_result')) {
                    $('ynadvsearch_content_result').adopt(Elements.from(responseHTML));
                    eval(responseJavaScript);
                    smoothbox.bind();
                }
                $$('.pages > ul > li > a').each(function(el) {
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

<div id="ynadvsearch_result" style="display: none">
    <div class='count_results ynadvsearch-clearfix'>
        <span class="search_icon fa fa-search"></span>
        <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
        <span class="total_results">(<?php echo $this->total_content?>)</span>
        <span class="label_results"><?php echo $this->htmlLink(array('route' => 'ynmultilisting_general'), $this->label_content, array());?></span>
    </div>
</div>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>
