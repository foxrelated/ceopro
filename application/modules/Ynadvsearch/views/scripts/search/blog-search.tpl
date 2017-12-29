<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   var form = 'filter_form';
	   if ($(form)) $(form).set('action', '<?php echo $this->url(array('action' => 'blog-search'),'ynadvsearch_search', true);?>');
	   loadContents('');
	   if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('search'))
        {
            $('search').value = '<?php echo $this -> query?>';
        }
	});
	
	var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        <?php if ($this->ynblog_enable) : ?>
        var widget = 'ynadvsearch.yn-blog-result';
        <?php else : ?>
        var widget = 'ynadvsearch.blog-result';
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

                if (typeof ynblogRenderViewMode == 'function') { 
                    ynblogRenderViewMode();
                }
            }
        });
        request.send();
    }
</script>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>