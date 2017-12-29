<?php $session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile){?>
  <div class="headline">
	  <h2>
	      <?php echo $this->translate('Blogs');?>
	  </h2>
	  <div class="tabs">
	    <?php
	      // Render the menu
	      echo $this->navigation() -> menu()
	                               -> setContainer($this->navigation)
	                               -> render();
	    ?>
	  </div>
  </div>
  <?php }
  else
  {?>
  	<div id='tabs'>
	  	<ul class="ymb_navigation_more">
		  <?php 
		  $max = 2;
		  $count = 0;
		  foreach( $this->navigation as $item ): $count ++;
		  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
	        'reset_params', 'route', 'module', 'controller', 'action', 'type',
	        'visible', 'label', 'href'
	        )));
		    if($count <= $max):?>
		     <li<?php echo($item->active?' class="active"':'')?>>
          		<?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
        	</li>	
		  <?php endif; endforeach; ?>
		  <?php if(count($this->navigation) > $max):?>
		  <li class="ymb_show_more_menus">
		  	<a href="javascript:void(0)" class="ymb_showmore_menus">
		  		<i class="icon_showmore_menus">
		  			<?php echo $this-> translate("Show more");?>
		  		</i>	  		  		
		  	</a>
		  	<div class="ymb_listmore_option">
		  		<div class="ymb_bg_showmore">
		  			<i class="ymb_arrow_showmore"></i>
		  		</div>	  		
			<?php 
			 	$count = 0;
				foreach( $this->navigation as $item ): $count ++;
				 $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			        'reset_params', 'route', 'module', 'controller', 'action', 'type',
			        'visible', 'label', 'href'
			        )));
				if($count > $max):
			?>
				<div<?php echo($item->active?' class="active"':'')?>>
				     <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
				 </div>
				 <?php endif; endforeach; ?>
			</div>
		  </li>
		  <?php endif;?>
		</ul>
	</div>
	<script type="text/javascript">
		jQuery(function(){
			jQuery('.ymb_show_more_menus').click(function(){
				jQuery(this).find('.ymb_listmore_option').toggle();
			})
		});
	</script>
  <?php  }?>
<?php
if ($this->maximum_reach)
	echo 	"<div class='tip'><span >" . $this->translate("Sorry! Maximum number of allowed blogs is: %s blog(s)",$this->max_blogs) . "</span></div>";
else
	echo $this->form->render($this);
?>

<script type="text/javascript">
  var system_id = <?php echo $this->system_id; ?>;

  function updateTextFields() {
  if ($('system').selectedIndex == 3) {
    $('username-wrapper').show();
    $('url-wrapper').hide();
    $('filexml-wrapper').hide();
	  $('submit-wrapper').show();
  }
  else if($('system').selectedIndex == 4){
    $('username-wrapper').hide();
    $('url-wrapper').show();
    $('filexml-wrapper').hide();
	  $('submit-wrapper').show();
  }
  else{
    $('username-wrapper').hide();
    $('filexml-wrapper').show();
    $('url-wrapper').hide();
    $('submit-wrapper').show();
  }

  if ($('system').selectedIndex == 0)
  {
	 $('filexml-wrapper').hide();
	 $('username-wrapper').hide();
	 $('submit-wrapper').hide();
   $('url-wrapper').hide();
  }
}
switch(system_id){
  case 0:
     $('username-wrapper').hide();
     $('submit-wrapper').hide();
     $('filexml-wrapper').hide();
     $('url-wrapper').hide();
     break;
  case 1:
  case 2:
     $('url-wrapper').hide();
     $('username-wrapper').hide();
     break;
  case 3:
     $('filexml-wrapper').hide();
     $('url-wrapper').hide();
     break;
  case 4:
     $('filexml-wrapper').hide();
     $('username-wrapper').hide();
     break;
}
</script>