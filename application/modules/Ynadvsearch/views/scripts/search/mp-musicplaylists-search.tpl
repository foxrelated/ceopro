<script type='text/javascript'>
    var params = <?php echo json_encode($this -> params); ?>;
	window.addEvent('domready', function() {
		<?php $url = $this->url(array('action'=>'mp-music-search'),'ynadvsearch_search', true);?>
		$('frm_search').set('action', '<?php echo $url;?>');
	    loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('title'))
        {
            $('title').value = '<?php echo $this -> query?>';
        }
    });
    
    var loadContents = function(url)
    {
        $('ynadvsearch_loading').style.display = '';
        $('ynadvsearch_content_result').innerHTML = '';
        var ajax_params = {};
        if (url == '') {
            url = en4.core.baseUrl + 'widget/index/name/mp3music.browse-playlists';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript)
            {
                $('ynadvsearch_loading').style.display = 'none';
                $('ynadvsearch_result').show();
                if($('ynadvsearch_content_result')) 
                {
                    $('ynadvsearch_content_result').innerHTML = responseHTML;
                    if ($$('.layout_mp3music_browse_playlists h3')[0]) {
                        $$('.layout_mp3music_browse_playlists h3')[0].destroy();
                    }
                }
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
<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<div id="ynadvsearch_result" style="display: none">
	<div class='count_results ynadvsearch-clearfix'>
		<span class="search_icon fa fa-search"></span>
		<span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
		<span class="total_results">(<?php echo $this->total_content?>)</span>
		<span class="label_results"><?php echo $this->htmlLink(array('route' => 'mp3music_browseplaylists'), ucfirst($this->label_content), array());?></span>
	</div>
</div>
<div id="ynadvsearch_content_result"></div>