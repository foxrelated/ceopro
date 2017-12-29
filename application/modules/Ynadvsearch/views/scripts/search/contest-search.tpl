<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
		if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array("action"=>"contest-search"), "ynadvsearch_search", true) ?>');
	    if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('contest_name'))
        {
            $('contest_name').value = '<?php echo $this -> query?>';
        }
        loadContents('');
	});
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/yncontest.listing-search';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        if (ajax_params['contest_name'] == null) {
            ajax_params['contest_name'] = '';
        }
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
                $$('.yncontest_no_border').each(function(el){
                    el.destroy();
                })
            }
        });
        request.send();
    }
</script>
<div id="ynadvsearch_result" >
	<div class='count_results ynadvsearch-clearfix'>
		<span class="search_icon fa fa-search"></span>
		<span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator ->getTotalItemCount()),$this->paginator ->getTotalItemCount())?></span>
		<span class="total_results">(<?php echo $this->total_content?>)</span>
		<span class="label_results"><?php echo $this->htmlLink(array('route' => 'yncontest_general'), ucfirst($this->label_content), array());?></span>
	</div>
</div>
<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display:none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result">
</div>