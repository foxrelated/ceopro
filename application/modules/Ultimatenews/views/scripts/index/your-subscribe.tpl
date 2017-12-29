<div class='layout_middle'>
    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='groups_browse'>
        <?php foreach( $this->paginator as $rss ): 
        	$logo_url = $rss->category_logo ? $rss->category_logo : $rss->logo	;			
			$logo_url = is_null($logo_url) ? 'no_photo' : $logo_url ; 
        	?>
          <li class="clearfix">
            <div class="groups_photo clearfix">
            <?php if($logo_url == 'no_photo'):?>
            	<a href="<?php echo $this->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $rss->category_id),'ultimatenews_feed')?>"><img src="/qc/luannd/contest402/application/modules/Ultimatenews/externals/images/nophoto_content_thumb_icon.png" ></a>
            <?php else:?>
            	<a href="<?php echo $this->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $rss->category_id),'ultimatenews_feed')?>"><img src="<?php echo $logo_url?>"></a>
            <?php endif;?>
             <a  href="<?php echo $this->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $rss->category_id),'ultimatenews_feed')?>">
            	<?php echo $rss->category_name;?>
            </a>  
            </div>
            <div class="groups_options">            	
	            <a class="smoothbox link_subscribe " href="<?php echo $this->url(array('action'=>$rss->isSubscribe(),'feed' => $rss->getIdentity()), 'ultimatenews_extended')?>" > 
                	<?php echo $this->translate(ucfirst($rss->isSubscribe()))?> 
                </a>                   
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      <?php endif; ?>
	<?php else:?>    	
    	<div class="tip">
	        <span>
	            <?php echo $this->translate("You have not subscribed RSS yet.")?>           
	        </span>
	    </div>
    <?php endif; ?>
  </div>