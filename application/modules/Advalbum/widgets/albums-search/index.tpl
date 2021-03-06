
<script type="text/javascript">
//<![CDATA[
  window.addEvent('domready', function() {

    $$('.advalbum_color_patterns li').each(function(li,index){
    	li.addEvent('click', function(){
    		if(li.hasClass('selected')){
	        	colorElm = $("color");
	        	colorElm.set("value", "");
	        	li.removeClass('selected');  
    		}
    		else
    		{
    			colorElm = $("color");
	        	colorElm.set("value", li.get('content'));
	        	$$('.advalbum_color_patterns li').removeClass('selected');
	            li.addClass('selected');
	        }
        });
    });
  });
  en4.core.runonce.add(function()
  {
	  if($('search'))
	    {
	      new OverText($('search'), 
	      {
	        poll: true,
	        pollInterval: 500,
	        positionOptions: {
	          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
	          offset: {
	            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
	            y: 2
	          }
	        }
	      });
	    }
	 });

</script>

<div>
	<div class="ynadvalbum_search">
		<?php echo $this->search_form->render($this) ?>
	</div>
	<ul class="advalbum_color_patterns clearfix">
		<?php if (count($this->colors)):?>
			<?php foreach ($this->colors as $color):?>
				<li title="<?php echo $color -> title; ?>" style="background-color: <?php echo $color->hex_value;?>;" content="<?php echo $color -> title; ?>" <?php echo (isset($this->color) && $this->color == $color->title) ? 'class="selected"' : ''; ?>></li>
			<?php endforeach;?>
		<?php endif;?>
		<button name="Search" id="advalbum_button_search" type="submit"><?php echo $this -> translate("Search")?></button>
	</ul>	
</div>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        $('filter_form').grab( $$('.advalbum_color_patterns')[0] );
    });    
</script>