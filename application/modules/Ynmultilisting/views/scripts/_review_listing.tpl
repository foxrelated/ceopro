<?php
	if($this->edit)
	{
		$tableRatingValue = Engine_Api::_() -> getDbTable('ratingvalues', 'ynmultilisting');
		$viewer  = Engine_Api::_() -> user() -> getViewer();
	}
?>
<div class="form-wrapper form-ynmultilisting-rate">
	
	<div class="form-label">
		<?php echo $this->translate('General Rating');?>	
	</div>
	<div class="form-element">
		<div id="video_rating" class="rating" onmouseout="set_rating_general();">
	        <span id="rate_1" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate_general(1);" onmouseover="rating_over_general(1);"></span>
	        <span id="rate_2" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate_general(2);" onmouseover="rating_over_general(2);"></span>
	        <span id="rate_3" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate_general(3);" onmouseover="rating_over_general(3);"></span>
	        <span id="rate_4" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate_general(4);" onmouseover="rating_over_general(4);"></span>
	        <span id="rate_5" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate_general(5);" onmouseover="rating_over_general(5);"></span>
	    </div>
	     <input type="hidden" id="review_rating" name="review_rating" />
	</div>	
		
	<?php foreach($this -> ratingTypes as $item) :?>
		<div class="form-label">
			<?php echo $this -> translate($item -> title);?>	
		</div>
		<div class="form-element">
				<div id="listing_rating_<?php echo $item -> getIdentity();?>" class="rating" onmouseout="set_rating(<?php echo $item -> getIdentity();?>);">
			        <span id="rate_1_<?php echo $item -> getIdentity();?>" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate(1, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(1, <?php echo $item -> getIdentity();?>);"></span>
			        <span id="rate_2_<?php echo $item -> getIdentity();?>" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate(2, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(2, <?php echo $item -> getIdentity();?>);"></span>
			        <span id="rate_3_<?php echo $item -> getIdentity();?>" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate(3, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(3, <?php echo $item -> getIdentity();?>);"></span>
			        <span id="rate_4_<?php echo $item -> getIdentity();?>" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate(4, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(4, <?php echo $item -> getIdentity();?>);"></span>
			        <span id="rate_5_<?php echo $item -> getIdentity();?>" class="rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_generic" onclick="rate(5, <?php echo $item -> getIdentity();?>);" onmouseover="rating_over(5, <?php echo $item -> getIdentity();?>);"></span>
			    </div>
			    <input type="hidden" id="review_rating_<?php echo $item -> getIdentity();?>" name="review_rating_<?php echo $item -> getIdentity();?>" />
		</div>
	<?php endforeach;?>
</div>
<br />

<script type="application/javascript">
    
    var rated = 0;
    var new_rate = 0;
    var is_click = 0;
    
    var set_rating = window.set_rating = function(id) {
    	if(is_click)
    	{
       	 var rating = new_rate;
        }
        else
        {
          if($('review_rating_'+id).get('value'))
         	var rating = $('review_rating_'+id).get('value');
          else
          	var rating = 0;
        }
        for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x+'_'+id).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big');
        }
        for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x+'_'+id).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_disabled');
        }
        $('review_rating_'+id).set('value', rating);
        is_click = 0;
    }

    var rate = window.rate = function(rating,id) {
        if (!rated) {
            rated = 1;
        }
        is_click = 1;
        new_rate = rating;
        set_rating(id);
    }
    
    var rating_over = window.rating_over = function(rating,id) {
        for(var x=1; x<=5; x++) {
            if(x <= rating) {
                $('rate_'+x+'_'+id).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big');
            } else {
                $('rate_'+x+'_'+id).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_disabled');
            }
        }
    }
    
    <?php foreach($this -> ratingTypes as $item) :?>
   		<?php if($this->edit):?>
   			 is_click = 1;
   			 <?php $row = $tableRatingValue -> getRowRatingThisType($item -> getIdentity(), $this->review->getIdentity());?>
    		 <?php if($row):?>
	    		 new_rate = <?php echo $row -> rating; ?>;
		   		 set_rating(<?php echo $item -> getIdentity()?>);
	   		 <?php else :?>
	   		 	 new_rate = 0;
		   		 set_rating(<?php echo $item -> getIdentity()?>);
	   		 <?php endif;?>
	   		 new_rate = 0;
	   		 is_click =0;
	   	<?php else:?>	 
	   		 set_rating(<?php echo $item -> getIdentity()?>);
	    <?php endif;?>
    <?php endforeach;?>
    
</script>


<script type="application/javascript">
    var rated_general = 0;
    var new_rate_general = 0;
    
    var set_rating_general = window.set_rating_general = function() {
        var rating = new_rate_general;
        for(var x=1; x<=parseInt(rating); x++) {
            $('rate_'+x).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big');
        }
        for(var x=parseInt(rating)+1; x<=5; x++) {
            $('rate_'+x).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_disabled');
        }
        $('review_rating').set('value', rating);
    }
	
    var rate_general = window.rate_general = function(rating) {
        if (!rated_general) {
            rated_general = 1;
        }
        new_rate_general = rating;
        set_rating_general();
    }
    
    var rating_over_general = window.rating_over_general = function(rating) {
        for(var x=1; x<=5; x++) {
            if(x <= rating) {
                $('rate_'+x).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big');
            } else {
                $('rate_'+x).set('class', 'ynmultilisting_rating_star_big_generic ynicon yn-star ynmultilisting_rating_star_big_disabled');
            }
        }
    }
    <?php if($this->edit):?>
    	 new_rate_general = <?php echo $this->review->overal_rating;?>;
   		 set_rating_general();
   	<?php else:?>	 
   		 set_rating_general();
    <?php endif;?>
</script>
