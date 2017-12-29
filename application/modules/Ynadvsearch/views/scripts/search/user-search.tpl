<?php if ($this->ynmember_enable) : ?>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynmember/externals/scripts/wookmark/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Ynmember/externals/scripts/wookmark/jquery.wookmark.min.js"></script>
<?php endif; ?>
<script type='text/javascript'>
	var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'user-search'),'ynadvsearch_search', true);?>');
	   if ($$('.field_search_criteria').length) $$('.field_search_criteria')[0].set('action', '<?php echo $this->url(array('action' => 'user-search'),'ynadvsearch_search', true);?>');
	   loadContents('');
	   if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('displayname'))
        {
            $('displayname').value = '<?php echo $this -> query?>';
        }
	});
	
	var loadContents = function(url)
	{
	    $('ynadvsearch_content_result').innerHTML = '';
		$('ynadvsearch_loading').style.display = '';
		<?php if ($this->ynmember_enable) : ?>
        var widget = 'ynmember.members-listing';
        <?php else : ?>
        var widget = 'ynadvsearch.member-result';
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
            evalScripts: false,
	        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
	        {
	            $('ynadvsearch_loading').style.display = 'none';
	            if($('ynadvsearch_content_result')) 
	            {
	            	$('ynadvsearch_content_result').innerHTML = responseHTML;
                    eval(responseJavaScript);
                }
				$$('.pages > ul > li > a').each(function(el)
				{
				    el.addEvent('click', function() {
				        var url = el.href;
                        el.href = 'javascript:void(0)';
                        loadContents(url);
				    });
				});

                Smoothbox.bind();
	        }
	    });
	    request.post();
    }
    
</script>
<?php if ($this->ynmember_enable):?>
<div id="ynadvsearch_result" style="display: none">
    <div class='count_results ynadvsearch-clearfix'>
        <span class="search_icon fa fa-search"></span>
        <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
        <span class="total_results">(<?php echo $this->total_content?>)</span>
        <span class="label_results"><?php echo $this->htmlLink(array('route' => 'ynmember_general'), $this->label_content, array());?></span>
    </div>
</div>
<?php endif; ?>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>