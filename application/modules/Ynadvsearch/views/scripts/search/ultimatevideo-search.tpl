<script type='text/javascript'>
	var smoothbox = this.Smoothbox;
	var params = <?php echo json_encode($this -> params); ?>;
    window.addEvent('domready', function() {
        if ($('filter_form')) $('filter_form').set('action', '<?php echo $this->url(array('action' => 'ultimatevideo-search'),'ynadvsearch_search', true);?>');
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
            url = en4.core.baseUrl + 'widget/index/name/ynadvsearch.ynultimatevideo-result';
            ajax_params = params;
        }
        ajax_params['format'] = 'html';
        ajax_params['mode_simple'] = 1;
        ajax_params['mode_list'] = 1;
        ajax_params['mode_casual'] = 1;
        ajax_params['view_mode'] = 'simple';
        ajax_params['numberOfItems'] = 9;
        var request = new Request.HTML({
            url : url,
            data : ajax_params,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                setTimeout(function(){
                    addEventsForYnultimatevideoPopup();
                    ynultimatevideoRenderViewMode(0, 'simple');
                }, 200);
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
    function ynultimatevideoAddNewPlaylist(ele, guid) {
        var nextEle = ele.getNext();
        if(nextEle.hasClass("ynultimatevideo_active_add_playlist")) {
            //click to close
            nextEle.removeClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "none");
        } else {
            //click to open
            nextEle.addClass("ynultimatevideo_active_add_playlist");
            nextEle.setStyle("display", "block");
        }
        $$('.play_list_span').each(function(el){
            if(el === nextEle){
                //do not empty the current box
            } else {
                el.empty();
                el.setStyle("display", "none");
                el.removeClass("ynultimatevideo_active_add_playlist");
            }
        });
        var data = guid;
        var url = '<?php echo $this->url(array('action' => 'get-playlist-form'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.HTML({
            url : url,
            data : {
                subject: data,
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                var spanEle = nextEle;
                spanEle.innerHTML = responseHTML;
                eval(responseJavaScript);

                var popup = spanEle.getParent('.ynultimatevideo-action-pop-up');
                var layout_parent = popup.getParent('.layout_middle');
                if (!layout_parent) layout_parent = popup.getParent('#global_content');
                var y_position = popup.getPosition(layout_parent).y;
                var p_height = layout_parent.getHeight();
                var c_height = popup.getHeight();
                if(p_height - y_position < (c_height + 21)) {
                    layout_parent.addClass('popup-padding-bottom');
                    var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace( /\D+/g, ''));
                    layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 21 + y_position - p_height)+'px');
                }
            }
        });
        request.send();
    }

    function ynultimatevideoAddToPlaylist(ele, playlistId, guild) {
        var checked = ele.get('checked');
        var data = guild;
        var url = '<?php echo $this->url(array('action' => 'add-to-playlist'), 'ynultimatevideo_playlist', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                subject: data,
                playlist_id: playlistId,
                checked: checked,
            },
            onSuccess: function(responseJSON) {
                if (!responseJSON.status) {
                    ele.set('checked', !checked);
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToWatchLater(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_watch_later', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        var html = '<i class="fa fa-ban"></i>' + ' ' + '<?php echo $this->translate('Unwatched') ?>';
                        ele.innerHTML = html;
                    } else {
                        var html = '<i class="fa fa-play-circle"></i>' + ' ' + '<?php echo $this->translate('Watch Later') ?>';
                        ele.innerHTML = html;
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

    function ynultimatevideoAddToFavorite(ele, video_id) {
        var url = '<?php echo $this->url(array('action' => 'add-to'), 'ynultimatevideo_favorite', true);?>';
        var request = new Request.JSON({
            url : url,
            data : {
                video_id: video_id
            },
            onSuccess: function(responseJSON) {
                if (responseJSON.result) {
                    if (responseJSON.added == 1) {
                        ele.addClass('added');
                    } else {
                        ele.removeClass('added');
                    }
                }
                var div = ele.getParent('.ynultimatevideo-action-pop-up');
                var notices = div.getElement('.add-to-playlist-notices');
                var notice = new Element('div', {
                    'class' : 'add-to-playlist-notice',
                    text : responseJSON.message
                });
                notices.adopt(notice);
                notice.fade('in');
                (function() {
                    notice.fade('out').get('tween').chain(function() {
                        notice.destroy();
                    });
                }).delay(2000, notice);
            }
        });
        request.send();
    }

</script>