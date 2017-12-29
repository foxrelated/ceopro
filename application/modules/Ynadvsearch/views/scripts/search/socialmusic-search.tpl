<script type='text/javascript'>
	var smoothbox = this.Smoothbox;
	var params = <?php echo json_encode($this -> params); ?>;
    window.addEvent('domready', function() {
        if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'socialmusic-search'),'ynadvsearch_search', true);?>');
        loadContents('');
        if($('query'))
        {
            $('query').value = '<?php echo $this -> query?>';
        }
        if($('keyword'))
        {
            $('keyword').value = '<?php echo $this -> query?>';
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
            url = en4.core.baseUrl + 'widget/index/name/ynmusic.music-listing';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('ynadvsearch_loading').style.display = 'none';
                if($('ynadvsearch_content_result')) {
                    $('ynadvsearch_content_result').adopt(Elements.from(responseHTML));
                    eval(responseJavaScript);
                    smoothbox.bind();
                    $$('a.action-link.show-hide-btn').removeEvents('click').addEvent('click', function() {
			
				    	var parent = this.getParent('.show-hide-action');
				    	var popup = parent.getElement('.action-pop-up');
				    	$$('.action-pop-up').each(function(el) {
				    		if (el != popup) el.hide();
				    	});
				    	
				    	if (!popup.isDisplayed()) {
				    		var loading = popup.getElement('.add-to-playlist-loading');
				    		if (loading) {
					    		var url = loading.get('rel');
					    		loading.show();
					    		var checkbox = popup.getElement('.box-checkbox');
					    		checkbox.hide();
					    		var request = new Request.HTML({
						            url : url,
						            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
						                elements = Elements.from(responseHTML);
						                if (elements.length > 0) {
						                    checkbox.empty();
						                    checkbox.adopt(elements);
						                    eval(responseJavaScript);
						                    loading.hide();
						                    checkbox.show();
						                    var layout_parent = popup.getParent('.layout_middle');
									    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
									    	var y_position = popup.getPosition(layout_parent).y;
											var p_height = layout_parent.getHeight();
											var c_height = popup.getHeight();
								    		if(p_height - y_position < (c_height + 1)) {
								    			layout_parent.addClass('popup-padding-bottom');
								    			var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
								    			layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 1 + y_position - p_height)+'px');
											}
						                }
						            }
						        });
						        request.send();
					       	}
				    	}
				    	
				    	popup.toggle();
				    	
				    	var layout_parent = popup.getParent('.layout_middle');
				    	if (!layout_parent) layout_parent = popup.getParent('#global_content');
				    	var y_position = popup.getPosition(layout_parent).y;
						var p_height = layout_parent.getHeight();
						var c_height = popup.getHeight();
				    	if (popup.isDisplayed()) {
				    		if(p_height - y_position < (c_height + 1)) {
				    			layout_parent.addClass('popup-padding-bottom');
				    			layout_parent.setStyle('padding-bottom', (c_height + 1 + y_position - p_height)+'px');
							}
							else if (layout_parent.hasClass('popup-padding-bottom')) {
				    			layout_parent.setStyle('padding-bottom', '0');
				    		}
				    	}
				    	else {
				    		if (layout_parent.hasClass('popup-padding-bottom')) {
				    			layout_parent.setStyle('padding-bottom', '0');
				    		}
				    	}
				    });
				    
				    $$('a.action-link.cancel').removeEvents('click').addEvent('click', function() {
				    	var parent = this.getParent('.action-pop-up');
				    	if (parent) {
				    		parent.hide();
				    		var layout_parent = parent.getParent('.layout_middle');
				    		if (!layout_parent) layout_parent = popup.getParent('#global_content');
				    		if (layout_parent.hasClass('popup-padding-bottom')) {
				    			layout_parent.setStyle('padding-bottom', '0');
				    		}
				    	}
				    });
				    
				    if (typeof addEventForPlayBtn == 'function') { 
					  	addEventForPlayBtn(); 
					}
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
