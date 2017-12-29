<!-- Distinguish mobile or desktop -->
<script type="text/javascript">
	jQuery.noConflict();
	(function($){
		$(function(){
			if(!(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))){
				if(!$('body').hasClass('Web')){
					$('body').addClass('Web');
				}
			}
		});
	})(jQuery);
</script>
<!-- End distinguish mobile or desktop -->

<div id="advgroup_list_item" class="<?php echo $this -> class_mode;?>"> 	
 	<div id="adv_group_tabs" class="tabs_alt tabs_parent">
        <!--  Tab bar -->
        <ul id="adv_group_tab_list" class = "main_tabs">
			
			<!-- Newest Groups -->
			<?php if(in_array('recent', $this -> tab_enabled)):?>
            <li class="active">
            	<a href="javascript:;" rel="tab_groups_newest" >
					<?php echo $this->translate('Newest Groups');?>	
				</a>
            </li>
			<?php endif;?>
			
			<!-- Popular Groups -->
			<?php if(in_array('popular', $this -> tab_enabled)):?>
			<li>
				<a href="javascript:;" rel="tab_groups_popular">
					<?php echo $this->translate('Popular Groups');?>
				</a>
			</li>
			<?php endif;?>
			
            <!-- Active Groups -->
            <?php if(in_array('active', $this -> tab_enabled)):?>
			<li>
				<a href="javascript:;" rel="tab_groups_active">
					<?php echo $this->translate('Active Groups');?>
				</a>
			</li>
			<?php endif;?>
			
			<!-- Directories Groups -->
			<?php if(in_array('directory', $this -> tab_enabled)):?>
			<li>
				<a href="javascript:;" rel="tab_groups_directories">
					<?php echo $this->translate('Groups Directories');?>
				</a>
			</li>
			<?php endif;?>
			
        </ul>
		<!--  End Tab bar -->
		
		<!-- Mode View -->
		<div class="advgroup-action-view-method">
			<?php if(in_array('map', $this -> mode_enabled)):?>
			<!-- Map View -->
			<div class="advgroup_home_page_list_content" rel="map_view">
				<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('Map View')?></div>
				<span id="map_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-map-marker" onclick="advgroup_view_map();"></span>
			</div>
			<!-- End Map View -->
			<?php endif;?>			
			
			<?php if(in_array('grid', $this -> mode_enabled)):?>
			<!-- Grid View -->
			<div class="advgroup_home_page_list_content" rel="map_view">
				<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('Grid View')?></div>
				<span id="grid_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-grid-view" onclick="advgroup_view_grid();"></span>
			</div>
			<!-- End Grid View -->
			<?php endif;?>
			
			<?php if(in_array('list', $this -> mode_enabled)):?>
			<!-- List View -->
			<div class="advgroup_home_page_list_content" rel="map_view">
				<div class="advgroup_home_page_list_content_tooltip"><?php echo $this->translate('List View')?></div>
				<span id="list_view_<?php echo $this->identity;?>" class="advgroup_home_page_list_content_icon ynicon yn-list-view" onclick="advgroup_view_list();"></span>
			</div>
			<!-- End List View -->
			<?php endif;?>
			
		</div>
		<!-- End Mode View -->
    </div>
	
    <div id="advgroup_list_item_content" class="advgroup-tabs-content ynclearfix">
    	<?php if(in_array('recent', $this -> tab_enabled)):?>
		<!-- Newest Groups Tab Content-->
		<div id="tab_groups_newest" class="tabcontent" style="display: block;">
			<?php if(count($this->recentgroups)):?>
			<!-- Static Content -->
			<ul class="generic_list_widget groups_browse yn-clearfix">
				<?php foreach( $this->recentgroups as $item ): ?>
				<?php
			    	$session = new Zend_Session_Namespace('mobile');
					if($session -> mobile)
					{
						$title = $item->getTitle();
						$owner_name = $item->getOwner()->getTitle();
					}
					else 
					{
						$title = Engine_Api::_()->advgroup()->subPhrase($item->getTitle(),200);
			        	$owner_name = Engine_Api::_()->advgroup()->subPhrase($item->getOwner()->getTitle(),13);
					}
			    ?>	
				<li>
					<div class="list-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl(); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
								<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
							<div class="desc">
								<?php 
									$description = strip_tags($item->description);
									if(strlen($description) > 80)
									{								
										echo $this -> string() -> truncate($description, 80); 
									}
									else 
									{
										echo $description;
									}
								?>
							</div>
						</div>
					</div>
					<div class="grid-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl(); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>									
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
						</div>
					</div>
				</li>	
				<?php endforeach; ?>
			</ul>
			<!-- End Static Content -->
			<?php else:?>
			<div class="tip">
				<span>
					<?php echo $this->translate("There are no groups.") ?>
				</span>
			</div>
			<?php endif;?>
		</div>
		<?php endif; ?>
		<?php if(in_array('popular', $this -> tab_enabled)):?>
		<!-- Popular Groups Tab Content -->
		<div id="tab_groups_popular" class="tabcontent" style="display: none;">
			<?php if( count($this->populargroups) > 0 ): ?>
			<!-- Static Content -->
			<ul class="generic_list_widget groups_browse">
				<?php foreach( $this->populargroups as $item ): ?>
				<?php
			    	$session = new Zend_Session_Namespace('mobile');
					if($session -> mobile)
					{
						$title = $item->getTitle();
						$owner_name = $item->getOwner()->getTitle();
					}
					else 
					{
						$title = Engine_Api::_()->advgroup()->subPhrase($item->getTitle(),18);
			        	$owner_name = Engine_Api::_()->advgroup()->subPhrase($item->getOwner()->getTitle(),13);
					}
			    ?>	
				<li>
					<div class="list-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl(); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>									
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
							<div class="desc">
								<?php 
									$description = strip_tags($item->description);
									if(strlen($description) > 80)
									{								
										echo $this -> string() -> truncate($description, 80); 
									}
									else 
									{
										echo $description;
									}
								?>
							</div>
						</div>
					</div>
					<div class="grid-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl(); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
						</div>
					</div>
				</li>		
				<?php endforeach; ?>
			</ul>
			<!-- End Static Content -->
			<?php else:?>
			<div class="tip">
				<span>
					<?php echo $this->translate("There are no groups.") ?>
				</span>
			</div>
			<?php endif;?>
		</div>
		<?php endif;?>
		<?php if(in_array('active', $this -> tab_enabled)):?>
		<!-- Active Groups Tab Content -->
		<div id="tab_groups_active" class="tabcontent">
			<?php if( count($this->activegroups) > 0 ): ?>
			<!-- Static Content -->
			<ul class="generic_list_widget groups_browse">
				<?php foreach( $this->activegroups as $item ): ?>
				<?php
			    	$session = new Zend_Session_Namespace('mobile');
					if($session -> mobile)
					{
						$title = $item->getTitle();
						$owner_name = $item->getOwner()->getTitle();
					}
					else 
					{
						$title = Engine_Api::_()->advgroup()->subPhrase($item->getTitle(),18);
			        	$owner_name = Engine_Api::_()->advgroup()->subPhrase($item->getOwner()->getTitle(),13);
					}
			    ?>	
				<li>
					<div class="list-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl(); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
							<div class="desc">
								<?php 
									$description = strip_tags($item->description);
									if(strlen($description) > 80)
									{								
										echo $this -> string() -> truncate($description, 80); 
									}
									else 
									{
										echo $description;
									}
								?>
							</div>
						</div>
					</div>
					<div class="grid-view">  
						<div class="photo">
							<a href="<?php echo $item->getHref() ?>" class="thumb">
								<span class="image-thumb" style="background-image:url('<?php echo $item -> getPhotoUrl("thumb.profile"); ?>')" >
									
								</span>
							</a>
							<?php if($item->isNewGroup()): ?>								
							<span class="newGroup"></span>														
							<?php endif; ?>	
						</div>
						<div class="info">
							<div class="title">
								<?php echo $this->translate($this->htmlLink($item->getHref(),$title)); ?>
							</div>
							<div class="stats">
								<div class="time_active">
									<i class="ynicon yn-alarm" title="Time create"></i>
									<?php echo $item -> getTimeAgo(); ?>
								</div>
								<div class="groups_members">
									<i class="ynicon yn-user" title="Guests"></i>	
									<?php echo $this->translate(array("%s member", "%s member", $item->countGroupMembers()),$item->countGroupMembers()); ?>
								</div>
							</div>
						</div>
					</div>
				</li>
				<?php endforeach;?>
			</ul>
			<!-- End Static Content -->
			<?php else:?>
			<div class="tip">
				<span>
					<?php echo $this->translate("There are no groups.") ?>
				</span>
			</div>
			<?php endif;?>
		</div>
		<?php endif; ?>
		<?php if(in_array('directory', $this -> tab_enabled)):?>
		<!-- Group Directories Tab Content -->
		<div id="tab_groups_directories" class="tabcontent" style="display: none;">
			<div id = 'advgroup_group_directory'>
				<?php if( count($this->directory) > 0 ): ?>
				<ul class="generic_list_widget"  style="padding-bottom:0px;">
					<?php foreach ($this->directory as $group): ?>
				            <li class="advgroup_group_row">
					                <?php if(count($group->getAllSubGroups()) > 0) : ?>
					                       <span class="advgroup-group-collapse-control advgroup-group-collapsed"></span>
					                <?php else : ?>
					                       <span class="advgroup-group-collapse-nocontrol"></span>
					                <?php endif; ?>
					                <div class="advgroup_info">
										<b><?php echo $group; ?></b>	
										<?php echo $this->translate('led by');?>
										<?php echo $group->getOwner();?>   
										-
										<?php echo $this->translate(array('%s member', '%s members', $group->member_count), $this->locale()->toNumber($group->member_count)) ?>
										,
										<?php echo $this->translate('last updated: %s', $this->timestamp($group->modified_date)) ?>   
									</div>
									<div class="advgroup_description"> 
										<span class="advgroup-group-collapse-nocontrol"></span>
										<?php echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($group->description),75);?>
									</div>
				            </li>
				           	<?php foreach ($group->getAllSubGroups() as $subGroup) : ?>
				                <li class="advgroup-group-sub-group" >
				                    <div class="advgroup_info">
										<b><?php echo $subGroup; ?></b>	
										<?php echo $this->translate('led by');?>
										<?php echo $subGroup->getOwner();?>   
										-
										<?php echo $this->translate(array('%s member', '%s members', $subGroup->member_count), $this->locale()->toNumber($subGroup->member_count)) ?>
										,
										<?php echo $this->translate('last updated: %s', $this->timestamp($subGroup->modified_date)) ?>   
									</div>
									<div class="advgroup_description"> 
										<?php echo Engine_Api::_()->advgroup()->subPhrase(strip_tags($subGroup->description),75);?>
									</div>
				                </li>
				            <?php endforeach ?>
				        <?php endforeach; ?>
				</ul>
				<div class="clear"></div>
				<?php else:?>
				<div class="tip">
					<span>
						<?php echo $this->translate("There are no groups.") ?>
					</span>
				</div>
				<?php endif;?>
			</div>
		</div>
		<?php endif; ?>
		<iframe id='list-most-items-iframe' style="max-height: 500px;"> </iframe>
    </div>
    
     <script type="text/javascript">
           var adv_group_tabs =new ddtabcontent("adv_group_tabs");
           adv_group_tabs.setpersist(false);
           adv_group_tabs.setselectedClassTarget("link");
           adv_group_tabs.init(900000);
           var advgroup_view_map = function()
           {
           		document.getElementById('advgroup_list_item').set('class','advgroup_map-view');
           		var tab = $$('.layout_advgroup_list_most_items #adv_group_tab_list li .selected')[0].get('rel');
           		var html =  '<?php echo $this->url(array('action'=>'display-map-view'), 'group_general') ?>'+'/tab/'+tab;
           		document.getElementById('list-most-items-iframe').dispose();
           		var iframe = new IFrame({
           			id : 'list-most-items-iframe',
           			src: html,
    			    styles: {	       
    			        'height': 500,
    			    },
    			});
           		iframe.inject($$('#advgroup_list_item_content')[0]);
           		document.getElementById('list-most-items-iframe').style.display = 'block';
           		setCookie('view_mode', 'map');
           }  
           
           var advgroup_view_grid =  function()
           {
           		document.getElementById('advgroup_list_item').set('class','advgroup_grid-view');
           		setCookie('view_mode','grid');
           		
           }  
           
            var advgroup_view_list = function()
           {       
           		document.getElementById('advgroup_list_item').set('class','advgroup_list-view');
           		setCookie('view_mode', 'list');
           }  
           <?php if($this -> view_mode == 'map'):?>
           {
	       		advgroup_view_map();       		
	       	}
	       <?php endif;?>       
    </script>
    
</div>
<script type="text/javascript">
	window.addEvent('domready', function(){
			
		if(getCookie('view_mode')!= "")
		{
			document.getElementById('advgroup_list_item').set('class',"advgroup_"+getCookie('view_mode')+"-view");
			var map = getCookie('view_mode');						
			if(map == 'map')
			{
				advgroup_view_map();
			}
		}
		else
		{
			document.getElementById('advgroup_list_item').set('class',"<?php echo $this -> class_mode;?>");
			
		}
		
		$$('#advgroup_list_item #adv_group_tab_list > li > a').each(function(el, idx){
			el.addEvent('click', function(e){
				if(this.getProperty('rel') == 'tab_groups_directories')
					$$('.advgroup-action-view-method').hide();
				else
				{
					$$('.advgroup-action-view-method').show();
						if(getCookie('view_mode') != "")
						{
							var map = getCookie('view_mode');							
							if(map == 'map')
							{
								advgroup_view_map();
							}
							document.getElementById('advgroup_list_item').set('class',"advgroup_"+getCookie('view_mode')+"-view");
						}
						else
						{							
							document.getElementById('advgroup_list_item').set('class',"<?php echo $this -> class_mode;?>");
						}
										
				}			
			});
		});
		
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
