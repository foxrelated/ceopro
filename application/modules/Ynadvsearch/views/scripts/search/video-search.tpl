<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
	   if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array("action"=>"video-search"), "ynadvsearch_search", true) ?>');
	   loadContents(''); 
	   if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('text'))
        {
            $('text').value = '<?php echo $this -> query?>';
        }
	});
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        <?php if ($this->ynvideo_enable) : ?>
        var widget = 'ynvideo.list-videos';
        <?php else : ?>
        var widget = 'ynadvsearch.video-result';
        <?php endif; ?>
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/'+widget;
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                $('ynadvsearch_loading').style.display = 'none';
                <?php if ($this->ynvideo_enable):?>
                $('ynadvsearch_result').show();
                <?php endif; ?>
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
                <?php if ($this->ynvideo_enable):?>
                $$('.ynvideo_videos_list h4').each(function(el) {
                    el.destroy();
                });
                <?php endif; ?>
            }
        });
        request.send();
    }
</script>
<?php if ($this->ynvideo_enable):?>
<div id="ynadvsearch_result" style="display: none">
    <div class='count_results ynadvsearch-clearfix'>
        <span class="search_icon fa fa-search"></span>
        <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
        <span class="total_results">(<?php echo $this->total_content?>)</span>
        <span class="label_results"><?php echo $this->htmlLink(array('route' => 'video_general'), $this->label_content, array());?></span>
    </div>
</div>
<?php endif; ?>

<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_content_result"></div>