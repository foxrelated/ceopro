<?php if( count($this->paginator) > 0 ): ?>
<div id="advgroup_list_group" class="<?php echo $this -> class_mode;?>">
    <div class="advgroup-action-view-method ynclearfix"> 	
    	<?php if(in_array('map', $this -> mode_enabled)):?>
		<div class="advgroup_home_page_list_content" rel="map_view">
			<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
		    <span id="map_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-map-marker" onclick="advgroup_view_map_time();"></span>
		</div>
		<?php endif;?>	
		<?php if(in_array('grid', $this -> mode_enabled)):?>
		<div class="advgroup_home_page_list_content" rel="map_view">
			<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
		    <span id="grid_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-grid-view" onclick="advgroup_view_grid_time();"></span>
		</div>
		<?php endif;?>
		<?php if(in_array('list', $this -> mode_enabled)):?>
		<div class="advgroup_home_page_list_content" rel="map_view">
			<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
			<span id="list_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-list-view" onclick="advgroup_view_list_time();"></span>
		</div>
		<?php endif;?>
    </div>
	
    <div class="advgroup-tabs-content ynclearfix">   
        <div class="tabcontent" style="display:block">     
    	  	<ul class='generic_list_widget groups_browse yn-clearfix'>
			  <?php foreach( $this->paginator as $group ): ?>
				<li>
					<div class="list-view">  
						<div class="photo">
							<a href="<?php echo $group->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $group -> getPhotoUrl("thumb.normal"); ?>')" >
									
								</span>
							</a>
							<?php if($group->isNewGroup()): ?>								
								<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
							<?php $group_name = Engine_Api::_()->advgroup()->subPhrase($group->getTitle(),60);
								echo $this->htmlLink($group->getHref(), $group_name);
							?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon-time" title="Time create"></i>
									<?php echo $group -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon-person" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $group->countGroupMembers()),$group->countGroupMembers()); ?>
								</div>
							</div>
							<div class="desc">
								<?php echo $this -> string() -> truncate(strip_tags($group->description), 80); ?>
							</div>
						</div>
					</div>
					<div class="grid-view">
						<div class="photo">
							<?php if($group->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
							<a class="thumb" href="<?php echo $group->getHref() ?>">
								<span class="image-thumb" style="background-image: url('<?php echo $group->getPhotoUrl("thumb.feature");?>');">
								</span>
							</a>
						</div>
						<div class="info">
							<div class="title">
								<?php $group_name = Engine_Api::_()->advgroup()->subPhrase($group->getTitle(),60);
									echo $this->htmlLink($group->getHref(), $group_name);
								?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon-time" title="Time create"></i>
									<?php echo $group->getTimeAgo();?>
								</div>
								<div class="groups_members">
									<i class="ynicon-person" title="Guests"></i>
									<?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
								</div>
							</div>
						</div>
					</div>
				</li>
			  <?php endforeach; ?>
			</ul>
        </div>
	   <iframe id='list-most-time-iframe'style="max-height: 500px; display: none;" > </iframe>
    </div>
</div>

<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no groups yet.') ?>
			<?php if( $this->canCreate): ?>
            <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
				'<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
			<?php endif; ?>
		</span>
    </div>
<?php endif; ?>
<div id='paginator'>
<?php if( $this->paginator->count() > 1 ): ?>
     <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
<?php endif; ?>
</div>

<script type="text/javascript">
    var advgroup_view_map_time = function(){
       	document.getElementById('advgroup_list_group').set('class','advgroup_map-view');
       	var tab = 'tab_groups_newest';
       	var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'group_general') ?>'+'/tab/'+tab;
       		
       	document.getElementById('list-most-time-iframe').dispose();
       		
		var iframe = new IFrame({
			id : 'list-most-time-iframe',
			src: html,
			styles: {			       
				'height': '500px',
				'width' : '100%'
			},
		});
       	iframe.inject($('advgroup_list_group'));
		document.getElementById('list-most-time-iframe').style.display = 'block';
		//$$('.layout_advgroup_groups_listing .pages')[0].style.display = 'none';
		document.getElementById('list-most-time-iframe').style.display = 'block';
		document.getElementById('paginator').style.display = 'none';
		setCookie('view_mode', 'map');
    }
    var advgroup_view_grid_time =  function(){
		document.getElementById('advgroup_list_group').set('class','advgroup_grid-view');
		//$$('.layout_advgroup_groups_listing .pages')[0].style.display = 'block';
		setCookie('view_mode','grid');
		document.getElementById('paginator').style.display = 'block';
    }         
	var advgroup_view_list_time = function(){
		document.getElementById('advgroup_list_group').set('class','advgroup_list-view');
		//$$('.layout_advgroup_groups_listing .pages')[0].style.display = 'block';
		setCookie('view_mode', 'list');
		document.getElementById('paginator').style.display = 'block';
    }     
      <?php if($this -> view_mode == 'map'):?>
      {
	     advgroup_view_map_time();       
	     document.getElementById('paginator').style.display = 'none';		
	  }
	  <?php endif;?>   
</script>

<script type="text/javascript">
	window.addEvent('domready', function(){
			
		if(getCookie('view_mode')!= "")
		{
			document.getElementById('advgroup_list_group').set('class',"advgroup_"+getCookie('view_mode')+"-view");
			var map = getCookie('view_mode');							
			if(map == 'map')
			{
				advgroup_view_map_time();
			}
		}
		else
		{
			document.getElementById('advgroup_list_group').set('class',"<?php echo $this -> class_mode;?>");			
		}						
	});
	
	function setCookie(cname,cvalue,exdays)
    {
		var d = new Date();
		d.setTime(d.getTime()+(exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	}
	
	function getCookie(cname)
	{
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) 
		{
			var c = ca[i].trim();
			if (c.indexOf(name)==0) return c.substring(name.length,c.length);
		}
		return "";
	}
</script>
