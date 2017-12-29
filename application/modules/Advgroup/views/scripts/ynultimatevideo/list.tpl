<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Ultimate Videos');
			?>
		</h2>
	</div>
</div>

<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="search">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array(
				'route' => 'group_profile',
				'id' => $this->group->getIdentity()),
				$this->translate('Back to Group'), array(
					'class' => 'buttonlink icon_back'
				)) ?>
			<?php echo $this->htmlLink(array(
				'route' => 'group_extended',
				'controller'=>'ynultimatevideo',
				'action'=>'manage',
				'subject' => $this->subject()->getGuid()),
				$this->translate('Manage Videos'), array(
					'class' => 'buttonlink icon_group_video'
				)) ?>
			<?php if( $this->canCreate ): ?>
				<?php 
					echo $this->htmlLink(array(
						'route' => 'ynultimatevideo_general',
						'action' => 'create',
						'parent_type' =>'group',
						'subject_id' =>  $this->group->group_id,
					), $this->translate('Create New Video'), array(
					'class' => 'buttonlink icon_group_video_new'
					)) ;
				?>
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<?php if ($this->paginator->getTotalItemCount()> 0) : ?>
		<br />
		<div id="ynultimatevideo_list_item_browse_<?php echo $this->identity; ?>" class="ynultimatevideo_simple-view">
			<div id="ynultimatevideo_list_item_browse_content" class="ynultimatevideo_list_item_browse_content_listgrid">
				<!--{*@TODO add a method to delete video here*}-->
				<ul class="ynultimatevideo_list_most_items clearfix">
					<?php foreach( $this->paginator as $video ): ?>
					<?php echo $this->partial('_video_item.tpl', 'ynultimatevideo', array('video' => $video)); ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<br/>
		<div class ="ynvideo_pages">
			<?php echo $this->paginationControl($this->paginator, null, null, array(
			'pageAsQuery' => true,
			'query' => $this->formValues,
			)); ?>
		</div>
		<?php else : ?>
		<div class="tip">
                      <span>
                          <?php echo $this->translate('There are no videos found.'); ?>
                      </span>
		</div>
		<?php endif; ?>
	</div>
</div>

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
						var html = '<i class="fa fa-ban"></i><?php echo $this->translate('Unwatched') ?>';
						ele.innerHTML = html;
					} else {
						var html = '<i class="fa fa-play-cỉcle"></i><?php echo $this->translate('Watch Later') ?>';
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