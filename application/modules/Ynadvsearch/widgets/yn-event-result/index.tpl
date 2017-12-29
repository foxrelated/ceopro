<div class='count_results ynadvsearch-clearfix'>
	<span class="search_icon fa fa-search"></span>
	<span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
	<span class="total_results">(<?php echo $this->total_content?>)</span>
	<span class="label_results"><?php echo $this->htmlLink(array('route' => 'event_general'), $this->label_content, array());?></span>
</div>
<div class="ynevent-search-result-page">
<?php $repeateType = array(
	'0' => '', 
	'1' => $this->translate('daily'), 
	'7' => $this->translate('weekly'), 
	'30' => $this->translate('monthly'));
	?>

<?php if (!empty($this->message)) : ?>
	<div class="tip">
		<span><?php echo $this->translate($this->message)?></span>
	</div>  
<?php endif;?>

<?php if( count($this->paginator) > 0 ): ?>
	<div id="ynevent_list_time" class="ynevent_list-view">

	    <div class="ynevent-action-view-method ynclearfix"> 	
			<div class="ynevent_home_page_list_content" rel="map_view">
				<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
			    <span id="map_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_map_view" onclick="ynevent_view_map_time();"><i class="fa fa-map-marker"></i></span>
			</div>

			<div class="ynevent_home_page_list_content" rel="grid_view">
				<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
			    <span id="grid_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_grid_view" onclick="ynevent_view_grid_time();"><i class="fa fa-th"></i></span>
			</div>
			
			<div class="ynevent_home_page_list_content" rel="list_view">
				<div class="ynevent_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
				<span id="list_view_<?php echo $this->identity;?>" class="ynevent_home_page_list_content_icon tab_icon_list_view" onclick="ynevent_view_list_time();"><i class="fa fa-th-list"></i></span>
			</div>
	    </div>

		<div class="ynevent-tabs-content ynadvsearch-clearfix">   
			<div class="tabcontent" style="display:block">     
				<?php
				echo $this->partial('_list_most_item.tpl', 'ynevent', array('events' => $this->paginator));
				?>
			</div>
			<iframe id='list-most-time-iframe'style="max-height: 500px; display: none;" > </iframe>
		</div>
	</div>
	<?php if( $this->paginator->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->paginator, null, null, array(
			'query' => $this->formValues,
			)); ?>
		<?php endif; ?>

	<?php else: ?>
		<?php if (empty($this->message)) : ?>
			<div class="tip">
				<span>
					<?php if ($this->is_search): ?>
						<?php echo $this->translate('There were no events found matching your search criteria.') ?>
					<?php else: ?>
						<?php if( $this->filter != "past" ): ?>
							<?php echo $this->translate('There were no events found matching your search criteria.') ?>
						<?php else: ?>
							<?php echo $this->translate('There were no events found matching your search criteria.') ?>
						<?php endif; ?>
					<?php endif; ?>
				</span>
			</div>
		<?php endif;?>

	<?php endif; ?>

	<script type="text/javascript">
		var ynevent_view_map_time = function()
		{
			document.getElementById('ynevent_list_time').set('class','ynevent_map-view');
			var html =  '<?php echo $this->url(array('action'=>'display-map-view', 'tab' => 'browse', 'formValues' => Zend_Json::encode($this->formValues)), 'event_general') ?>';

			document.getElementById('list-most-time-iframe').dispose();

			var iframe = new IFrame({
				id : 'list-most-time-iframe',
				src: html,
				styles: {                
					'height': '500px',
					'width' : '100%'
				},
			});
			iframe.inject($('ynevent_list_time'));
			document.getElementById('list-most-time-iframe').style.display = 'block';
			$$('.ynevent-search-result-page .pages')[0].style.display = 'none';
			document.getElementById('list-most-time-iframe').style.display = 'block';
			setCookie('ynevent_mode_view', 'map');
		}
		var ynevent_view_grid_time =  function()
		{
			document.getElementById('ynevent_list_time').set('class','ynevent_grid-view');
			$$('.ynevent-search-result-page .pages')[0].style.display = 'block';
			setCookie('ynevent_mode_view', 'grid');
		}  

		var ynevent_view_list_time = function()
		{
			document.getElementById('ynevent_list_time').set('class','ynevent_list-view');
			$$('.ynevent-search-result-page .pages')[0].style.display = 'block';
			setCookie('ynevent_mode_view', 'list');
		}     
	</script>
</div>