<script type='text/javascript'>
	var smoothbox = this.Smoothbox;
	var params = <?php echo json_encode($this -> params); ?>;
    window.addEvent('domready', function() {
        if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'videochannel-search'),'ynadvsearch_search', true);?>');
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('keyword'))
        {
            $('keyword').value = '<?php echo $this -> query?>';
        }
    });
    
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/ynadvsearch.ynvideochannel-result';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        ajax_params['numberOfItems'] = 9;
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
    </div>
</div>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>

<script type="text/javascript">
</script>