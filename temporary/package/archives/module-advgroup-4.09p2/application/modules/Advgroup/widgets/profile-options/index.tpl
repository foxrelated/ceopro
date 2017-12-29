<div id='profile_options' style="margin-bottom: 10px;">
	
<?php 
$session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <h4 class="group_desc_responsive">          
        <img alt="" style="float:right;" rel="group_desc" id="responsive_desc_more_icon_id" src="./application/modules/Advgroup/externals/images/up.jpg" onmousedown="toggleMenu('responsive_desc_more_icon_id'); return false;">
      </h4>
  <?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->setPartial(array('_navIcons.tpl', 'core'))
      ->render()
  ?>
  
<script type="text/javascript"> 
 function toggleMenu(img_id){
    if($$('#profile_options ul')[0].style.display == 'none'){
      $$('#profile_options ul')[0].style.display = 'block';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/up.jpg';
    }else{
      $$('#profile_options ul')[0].style.display = 'none';
      document.getElementById(img_id).src = './application/modules/Advgroup/externals/images/down.jpg';
    }
  }
</script>
  
  
 <?php } else
  {
  	?>
  	<ul >
  	  
	  <?php 
	  $max = 3; 
	  $count = 0;
	  foreach( $this->navigation as $link ): $count ++;
	    if($count <= $max):?>
	    <li>
	      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
	        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
	        'style' => 'background-image: url('.$link->get('icon').');',
	        'target' => $link->get('target'),
	      )) ?>
	    </li>    	
	  <?php endif; endforeach; ?>
	  <?php  if($count > $max){ ?>
	  <li class="ymb_show_more_option">
	  	<a href="javascript:void(0)" class="ymb_showmore_group">
	  		<i class="icon_showmore_group">
	  			Show more
	  		</i>	  		  		
	  	</a>
	  	<div class="ymb_listmore_option">
	  		<div class="ymb_bg_showmore">
	  			<i class="ymb_arrow_showmore"></i>
	  		</div>	  		
		<?php 
		 	$count = 0;
			foreach( $this->navigation as $link ): $count ++;
			if($count > $max):
		?>
			<div>
			      <?php echo $this->htmlLink($link->getHref(), $this->translate($link->getLabel()), array(
			        'class' => 'buttonlink' . ( $link->getClass() ? ' ' . $link->getClass() : '' ),
			        'style' => 'background-image: url('.$link->get('icon').');',
			        'target' => $link->get('target'),
			      )) ?>
			 </div>
			 <?php endif; endforeach; ?>
		</div>
	  </li>
	  <?php } ?>
	</ul>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_option').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
				var item = jQuery('.ymb_listmore_option > div');
				if(item.length > 0){
					jQuery('.layout_advgroup_profile_options + .layout_core_container_tabs').css({
						'min-height': item.length*35
					});
				}				
			});
		});
	</script>
  <?php  }?>
</div>