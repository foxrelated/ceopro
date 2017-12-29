<div style="float: left; padding-right: 10px;">
<?php //echo $this->htmlLink($this->page->getOwner()->getHref(), $this->itemPhoto($this->page->getOwner(), 'thumb.icon')) ?>  
</div>
<div>
<h3><?php echo $this->htmlLink($this->page->getHref(), $this->page->title) ?> </h3>
 <?php 
       if($this->page->parent_type=='group'):           
            
            echo $this->htmlLink($this->baseUrl().'/group/'.$this->page->parent_id.'/'.$this->page->getGroupSlug($this->page->getGroup($this->page->parent_id)),$this->page->getGroup($this->page->parent_id)).' &raquo;';
       endif;       
               
        foreach($this->page->getBreadCrumNode() as $node): ?>
                <?php echo $this->htmlLink($node->getHref(), $this->string()->truncate($node->title,15) ) ?>
                &raquo;
 <?php endforeach; if(count($this->page->getBreadCrumNode()) >= 0):?>
 <?php echo $this->htmlLink($this->page->getHref(),$this->string()->truncate ($this->page->title,15) ); endif; ?>   
  <a name="ratepage"></a>
 <div style="width: 250px;" <?php if ($this->can_rate): ?> 
        onmouseout="rating_mouseout()" 
        <?php elseif ($this->viewer->getIdentity() == 0): ?> 
          onmouseover ="canNotRate(2);" 
          onmouseout="canNotRate(0);" 
          <?php else: ?> 
          onmouseover ="canNotRate(1);" 
          onmouseout="canNotRate(0);" 
          <?php endif;?>  
          id="page_rate">
    <?php for($i = 1; $i <= 5; $i++): ?>
      <img id="rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="rate(<?php echo $i; ?>);" onmouseover="rating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Ynwiki/externals/images/<?php if ($i <= $this->page->rate_ave): ?>star_full.png<?php elseif( $i > $this->page->rate_ave &&  ($i-1) <  $this->page->rate_ave): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
    <?php endfor; ?>
    (<?php
    echo $this->translate(array('%s rating', '%s ratings', $this->page->rate_count), $this->locale()->toNumber($this->page->rate_count));
    ?>)
     </div>
 <div> 
 <div style="padding-left: 0px;">
 <?php if(!$this->can_rate): ?>
    <div style="color: #ccc;" id='mess_rate'></div>
    <?php endif; ?>  
 <?php 
 $creator =  Engine_Api::_()->getItem('user', $this->page->creator_id);
 echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($creator->getHref(), $creator->getTitle(), array('target'=>'_top')));?> |
 <?php echo $this->timestamp($this->page->creation_date) ?>   
 <?php $revision = $this->page->getLastUpdated();
 if($revision):  ?>
  |
  <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id); 
 echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?> 
 <?php echo $this->timestamp($revision->creation_date) ?>
  (<?php echo $this->htmlLink(array(
          'action' => 'compare-versions',
          'pageId' => $this->page->page_id,
          'route' => 'ynwiki_general',
          'reset' => true,
        ), $this->translate("view change"), array(
        )) ?>)
   <?php endif;?>  
 </div>
 <br/>
 </div>
 </div>
 <script type="text/javascript">
    var img_star_full = "application/modules/Ynwiki/externals/images/star_full.png";
    var img_star_partial = "application/modules/Ynwiki/externals/images/star_part.png";
    var img_star_none = "application/modules/Ynwiki/externals/images/star_none.png";  
    
    function rating_mousehover(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).src = img_star_full;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
     function canNotRate(status) {
         if(status == 1)
         {
                //$('mess_rate').innerHTML = "<?php echo $this->translate('You have just rated this page or you are the creator of this page!') ?>";
         }
         else if(status == 2)
         {
                //$('mess_rate').innerHTML = "<?php echo $this->translate('You have to log in to rate this page!') ?>";
         }
         else
         {
             $('mess_rate').innerHTML = "";
         }
     }
    function rating_mouseout() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->page->rate_ave ?>) {
            $('rate_'+x).src = img_star_full;
          } else if(<?php echo $this->page->rate_ave ?> > (x-1) && x > <?php echo $this->page->rate_ave ?>) {
            $('rate_'+x).src = img_star_partial;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
    function rate(rates){
        $('page_rate').onmouseout = null;
        Smoothbox.open(en4.core.baseUrl + 'wiki/rate/pageId/<?php echo $this->page->getIdentity();?>/rates/'+rates);
      }
  
</script>
