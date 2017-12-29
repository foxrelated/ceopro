<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>
<?php
       
$this->headLink()
  	->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ynforum/externals/styles/prettyPhoto.css');
$this->headScript()
	->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/forum.jquery.min.js')
    ->appendFile($this->baseUrl() . '/application/modules/Ynforum/externals/scripts/jquery.prettyPhoto.js');
 ?>
<div class="advforum_post_detail">
    <div class="advforum_post_header">
        <div class="advforum_post_header_left">
            <?php
                echo $this->partial('_navigation.tpl', array(
                    'linkedCategories' => $this->linkedCategories,
                    'navigationForums' => $this->navigationForums,
                ));
            ?>
        </div>        
        <div class="clear"></div>
    </div>
    
    <?php if ($this->topic->closed) : ?>
        <div class="advforum_discussions_thread_options_closed">
            <i class="ynicon yn-lock"></i>
            <?php echo $this->translate('This topic has been closed.')?>
        </div>
    <?php endif; ?>
    
    <div class="forum_moderator_features">
    	<?php if ($this->canPost): ?>
                <form action="<?php echo $this->topic->getHref(array('action' => 'post-create'));?>">
                    <button type="submit"> 
                        <?php echo $this->translate('Reply') ?> 
                    </button>
                </form>
            <?php endif; ?>
            
        <?php if ($this->canSticky) : ?>
            <?php if (!$this->topic->sticky): ?>
                <?php
                echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make sticky'), array(
                    'class' => 'buttonlink icon_forum_post_stick'
                ))
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove sticky'), array(
                    'class' => 'buttonlink icon_forum_post_unstick'
                ))
                ?>
            <?php endif; ?>
        <?php endif; ?>
                    
        <?php if ($this->canClose) : ?>
            <?php if (!$this->topic->closed): ?>
                <?php
                echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
                    'class' => 'buttonlink icon_forum_post_close'
                ))
                ?>
            <?php else: ?>
                <?php
                echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
                    'class' => 'buttonlink icon_forum_post_unclose'
                ))
                ?>
            <?php endif; ?>
        <?php endif; ?>
                    
        <?php
        if ($this->canEdit) {
            echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array('class' => 'buttonlink smoothbox icon_forum_post_rename'));
        }
        ?>
        <?php
        if ($this->canMove) {
            echo $this->htmlLink(array('action' => 'move', 'reset' => false), $this->translate('Move'), array('class' => 'buttonlink smoothbox icon_forum_post_move'));
        }
        ?>
                    
        <?php if ($this->canDelete): ?>
            <?php
            echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
                'class' => 'buttonlink smoothbox icon_forum_post_delete'
            ))
            ?>
        <?php endif; ?>
    </div>
    
    <ul class="forum_categories">
        <li>
            <div>
                <h3> 
                    <?php echo $this->translate('Topic:') ?> 
                    <?php echo $this->topic->getTitle() ?> 
                </h3>
                 <!-- AddThis Button BEGIN -->
				<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
                <!-- Go to www.addthis.com/dashboard to customize your tools -->  
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
				<!-- AddThis Button END -->
                <?php 
                    if ($this->viewer->getIdentity()) {
                        if (!$this->isWatching) {
                            echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch topic'), 
                                    array('class' => 'buttonlink icon_forum_topic_watch'));
                        } else {
                            echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop watching topic'),
                                    array('class' => 'buttonlink icon_forum_topic_unwatch'));
                        }
                    }
                ?>
                <?php echo $this->htmlLink($this->forum->getHref(), $this->translate('Back To Topics'), array(
                    'class' => 'buttonlink icon_back'
                )) ?>
               
            </div>
            <a name="ratetopic"></a>
            <?php $avgrating = $this->avgrating?$this->avgrating:0;
            	  $totalRates = $this->totalRates?$this->totalRates:0;?>
			 <span <?php if ($this->can_rate): ?> onmouseout="topicrating_mouseout()"  <?php endif;?>  id="topic_rate" class="">
			        <?php for($i = 1; $i <= 5; $i++): ?>
			          <img class = 'star_rate' id="topic_rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="topicrate(<?php echo $i; ?>);" onmouseover="topicrating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Ynforum/externals/images/<?php if ($i <= $avgrating): ?>star_full.png<?php elseif( $i > $avgrating &&  ($i-1) <  $avgrating): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
			        <?php endfor; ?>
			        <span id = 'total_rate'><?php echo $this->translate(array("(%s rate)","(%s rates)",$totalRates), $totalRates)?></span>
			  </span>
			<script type="text/javascript">
			    var img_star_full = "application/modules/Ynforum/externals/images/star_full.png";
			    var img_star_partial = "application/modules/Ynforum/externals/images/star_part.png";
			    var img_star_none = "application/modules/Ynforum/externals/images/star_none.png";  
			    
			   var topicrating_mousehover = function(rating) 
			   {
			        for(var x=1; x<=5; x++) 
			        {
			          if(x <= rating) 
			          {
			            $('topic_rate_'+x).src = img_star_full;
			          } 
			          else 
			          {
			            $('topic_rate_'+x).src = img_star_none;
			          }
			        }
			    }
			   var topicrating_mouseout = function() 
			   {
			        for(var x=1; x<=5; x++) 
			        {
			          if(x <= <?php echo $avgrating ?>) 
			          {
			            $('topic_rate_'+x).src = img_star_full;
			          } else if(<?php echo $avgrating ?> > (x-1) && x > <?php echo $avgrating ?>) {
			            $('topic_rate_'+x).src = img_star_partial;
			          } else {
			            $('topic_rate_'+x).src = img_star_none;
			          }
			        }
			    }
			   var topicrate = function(rates)
			   {
			        var makeRequest = new Request(
		            {
		                url: en4.core.baseUrl +  "forums/topic/<?php echo $this->topic->getIdentity()?>/<?php echo $this->topic->getSlug()?>/topic-rate/rates/" + rates,
		                onComplete:function(response) 
		                {
		                	$('topic_rate').set('onmouseout','');
		                	$('total_rate').innerHTML = '<?php echo $this->translate(array("(%s rate)","(%s rates)",$this->totalRates + 1),$this->totalRates + 1)?>';
		                	var i = 1;
		                  	$$('.star_rate').each(function(e)
		                  	{
		                  		e.set('onclick','');
		                  		e.set('onmouseover','');
		                  		if(i <= response)
		                  		{
		                  			e.set('src',img_star_full);
		                  		}
		                  		else if(i > response &&  (i-1) <  response)
		                  		{
		                  			e.set('src',img_star_partial);
		                  		}
		                  		i ++;
		                  	});
		                }
		            }
				    )
				    makeRequest.send();
			   }
			  
			</script>
            <script type="text/javascript">
                en4.core.runonce.add(function() {
                    $$('.forum_topic_posts_info_body').enableLinks();

                    // Scroll to the selected post
                    var post_id = <?php echo sprintf('%d', $this->post_id) ?>;
                    if( post_id > 0 ) {
                        window.scrollTo(0, $('forum_post_' + post_id).getPosition().y);
                    }
                });
				  var pageAction =function(page)
				  {
				    $('page').value = page;
				    $('ynforum_filter_form').submit();
				  }
            </script>

            <?php 
                foreach ($this->paginator as $i => $post) {
                     if ((($post->approved) || (!$post->approved && $this->canApprove))
                        || ($post->isOwner($this->viewer))) {
                        $isModeratorPost = $this->forum->isModerator($post->getOwner());
                        echo $this->partial('_post.tpl', array(
                            'post' => $post,
                            'user' => $this->user,
                            'canApprove' => $this->canApprove,
                            'canEdit' => $this->canEdit,
                            'canDelete' => $this->canDelete,
                            'canPost' => $this->canPost,
                            'canEdit_Post' => $this->canEdit_Post,
                            'canDelete_Post' => $this->canDelete_Post,
                            'topic' => $this->topic,
                            'forum' => $this->forum,
                            'viewer' => $this->viewer,
                            'isModerator' => $isModeratorPost,
                            'thankUsers' => $this->thankUsers,
                            'navigation' => $this->navigation,
                            'index' => $i,
                            'decode_bbcode' => $this->decode_bbcode,
                        ));
                    }
                }
            ?>
        </li>
    </ul>
    
    <?php if($this->paginator->getTotalItemCount() > $this->paginator->getItemCountPerPage()):?>    
	    <ul class="forum_categories">
	        <li>
	            <div class="advforum_postdetail_controlbox"> 
	               			
	                <div class="advforum_viewmore">
	                    <?php
	                        echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","ynforum"), array('params' => array('post_id' => null)));
	                    ?>
	                    <form id = "ynforum_filter_form" action = "<?php echo $this->topic->getHref()?>" method = "GET">
	                    	<input type = "hidden" id = "page" name = "page"/>
	                    </form>
	                </div>				
	            </div>
	        </li>
	    </ul>
    <?php endif;?>
    <div class="forum_post_addnew_button">
    	<div class="post_selectAll">
    		<?php
    		$options['0'] = $this->translate('Moderate Posts').' (0)';
			if($this -> canDelete)
           	 	$options['delete'] = $this->translate('Delete');
			
			if($this -> canApprove)
			    $options['approve'] = $this->translate('Approve');
                
    		?>
    		<?php if(count($options) > 1): ?>
    		<a href="javascript:;" onclick="selectAll()"><?php echo $this -> translate("Select All Posts");?></a>
    		<span>/</span>
    		<a href="javascript:;" style="padding-right: 50px"  onclick="unSelectAll()"><?php echo $this -> translate("Deselect All Posts");?></a>
    		<?php endif;?>
    		
    	</div>
    	<div>
        	<form id="post_moderate_form" method="post" onsubmit="return false">
        	<?php
				if(count($options) > 1):
               		echo $this->formSelect('post_moderate', null, null, $options);?>
                      <input type="hidden" name="post_ids" id="post_ids" value="" />
                      <button type="submit" name="moderate" onclick="return checkSelected()">
                            <?php echo $this->translate('Go') ?>
                        </button>
                <?php endif;?>
             </form>
         </div>
    </div>    
    <ul class="forum_categories">   	
    	
        <?php if ($this->canPost && $this->form): ?>
            <li>
                <div> 
                    <h3><?php echo $this->translate('Quick reply') ?></h3> 
                </div>
            </li>
            <li>
                <?php echo $this->form->render(); ?>
            </li>
        <?php endif; ?>
    </ul>
    
    <?php
        echo $this->partial('_icon_legend_forum_rights.tpl', array(
            'canPost' => $this->canPost,
            'canEdit' => $this->canEdit,
            'canDelete' => $this->canDelete,
            'canApprove' => $this->canApprove,
            'canSticky' => $this->canSticky,
            'canClose' => $this->canClose,
            'canMove' => $this->canMove,
            'allowBbcode' => $this->allowBbcode,
            'allowHtml' => $this->allowHtml,
        ));
    ?>		
</div>

<div style="display:none">
    <?php echo $this->formRepuration->render();?>    
</div>    


<script type="text/javascript" language="javascript">
	jQuery.noConflict();
    var reputationBox = $('add-reputation-form');
    var handleClickReputationLink = function(event) 
    {
        event.stop();
        Smoothbox.open(reputationBox, {width: 290});
        var block = Smoothbox.instance;
        var form = block.content.getChildren('form');
        var ele = this;
        form.addEvent('submit', function(event) {
            var children = this.getElements('input');
            var data = {};
            children.each(function(el){
                if (el.get('type') == 'radio') {
                    if (el.checked) {
                        data[el.get('name')] = el.get('value');
                    }
                } else {
                    data[el.get('name')] = el.get('value');
                }
            });            
            clickThankOrReputationLink(ele, event, data);
            block.close();
        });
    }
    var clickThankOrReputationLink = function(ele, event, data) 
    {    
    	event.stop();
    	var parent = ele.getParent('ul.forum_boxarea');
    	var body_old = parent.getElements('div.forum_topic_posts_info_body')[0].get('html');
        var url = ele.get('href');
        var req = new Request.HTML({
            url: url,
            data : data,
            method : 'post',
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) 
            { 
                if (responseHTML == -1) {
                    alert('<?php echo $this->string()->escapeJavascript($this->translate('You cannot thank or add reputation to the same post twice.'))?>');
                } else if (responseHTML == -2) {
                    alert('<?php echo $this->string()->escapeJavascript($this->translate('There is an error occurred. Please try again !!!')) ?>')    
                } else {
                    var e = new Element('div', 
                    {      
                        html : responseHTML
                    }); 
                    var newElement = null;
                    var newThankElement = e.getFirst().getNext();
                    if (newThankElement) {
                        var parentThanks = parent.getNext();
                        if (parentThanks != null && parentThanks.hasClass('forum_categories')) {
                            newThankElement.replaces(parentThanks);
                        } else {
                            newThankElement.inject(parent, 'after');
                        }
                    }
                    newElement = e.getFirst().replaces(parent);
                    if (newElement) 
                    {
                    	<?php if($this->detect_link):?>
                    		var body = newElement.getElements('div.forum_topic_posts_info_body');
                    		body[0].innerHTML = body_old;
                        <?php endif;?>
                        var links = newElement.getElements('a.icon_forum_thank_post');
                        if (links) {
                                links.addEvent('click', function(event) {
                                    clickThankOrReputationLink(this, event);
                                });
                        }
                        var smoothBoxLinks = newElement.getElements('a.smoothbox');
                        // add events for showing the smoothbox when clicking on tag a with class smoothbox
                        smoothBoxLinks.addEvent('click', function(event) {
                            event.stop();
                            Smoothbox.open(this);
                        });

                        var addReputationLink = newElement.getElement('a.icon_forum_reputation_post');
                        if (addReputationLink) {
                            addReputationLink.addEvent('click', handleClickReputationLink);
                        }
                    }
                }
            },
            onFailure: function(xhr){
                alert('<?php echo $this->string()->escapeJavascript($this->translate('There is an error occurred. Please try again !!!')) ?>')
            }
        }).send();
    }
    
    $$('a.icon_forum_thank_post').addEvent('click', function(event) 
    {
        clickThankOrReputationLink(this, event);
    });    
    
    $$('a.icon_forum_reputation_post').addEvent('click', handleClickReputationLink);  
    <?php if($this->detect_link):?>
     window.addEvent('domready', function() {
        var arrText = $$('.forum_topic_posts_info_body').getElements('p');
        for (var i = 0; i < arrText.length; i++) {
            for (var j = 0; j < arrText[i].length; j++) 
            { 
                var text = arrText[i][j];
	            if (text.get('tag') != 'a' && text.getElements('a').length <= 0 && text.getElements('iframe').length <= 0 && text.getElements('img').length <= 0) 
	            {
		            var html = text.get('html');
		            if (typeof html == 'string' || html instanceof String) 
		            {
			            text.set('html', replaceURLWithHTMLLinks(html));
		            }
	            }
            }
        }
    });
	<?php endif;?>
    var replaceURLWithHTMLLinks = function(text) 
    {
        var exp1 = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
        var exp2 = /(\b(www).[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
        if (text.match(exp1)) {
        	return text.replace(exp1,"<a href='$1'>$1</a>");
        } else {
            if (text.match(exp2)) {
        		return text.replace(exp2,"<a href='http://$1'>$1</a>");
            }
        }
        return text;
    } 
    window.addEvent('domready', function() 
    {
		jQuery("area[rel^='prettyPhoto']").prettyPhoto();
		jQuery(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({autoplay_slideshow: false});
		jQuery(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({hideflash: true});
		jQuery("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
			custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
			changepicturecallback: function(){ initialize(); }
		});
		jQuery("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
			custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
			changepicturecallback: function(){ _bsap.exec(); }
		});
	}); 
	
	var count = 0;
    en4.core.runonce.add(function()
    {
    	$$('li.forum_boxarea_header input[type=checkbox]').addEvent('click', function()
       	{     
			if(this.checked == true)
			{
				count ++;
			}
			else
			{
				count --;
			}
			$$('#post_moderate')[0].childNodes[1].text = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
			$$('#post_moderate')[0].childNodes[1].label = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
		});
    });
    $$('.forum_select_quick_navigation').addEvent('change', function() {
        if (this.value) {
            window.location.href = this.value;
        }
    });  
	function selectAll()
    {
    	var checks = $$('li.forum_boxarea_header input[type=checkbox]');
		checks.each(function(i)
		{
	 		i.checked = true;
		});
		count = checks.length;
		$$('#post_moderate')[0].childNodes[1].text = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
		$$('#post_moderate')[0].childNodes[1].label = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
    }
    function unSelectAll()
    {
    	var checks = $$('li.forum_boxarea_header input[type=checkbox]');
		checks.each(function(i)
		{
	 		i.checked = false;
		});
		count = 0;
		$$('#post_moderate')[0].childNodes[1].text = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
		$$('#post_moderate')[0].childNodes[1].label = "<?php echo $this->translate('Moderate Posts'); ?>" + " (" + count +")";
    }
    function checkSelected()
    {
    	var checks = $$('li.forum_boxarea_header input[type=checkbox]');
    	var post_ids = "";
    	
    	checks.each(function(i)
		{
	 		if(i.checked == true)
	 		{
	 			post_ids += i.value + ",";
	 		}
		});
		if($$('#post_moderate')[0].value == 0)
		{
			alert("<?php echo $this->translate('Please select a action to moderate.'); ?>");
			return false;
		}
		if(post_ids == "")
		{
			alert("<?php echo $this->translate('Please select a post to moderate.'); ?>");
			return false;
		}
		$('post_ids').value = post_ids;
		if(confirm("<?php echo $this->translate('Are you sure you want to moderate the selected posts?'); ?>"))
		{
			$('post_moderate_form').submit();
		}
        return false;
    }
</script> 
 
 