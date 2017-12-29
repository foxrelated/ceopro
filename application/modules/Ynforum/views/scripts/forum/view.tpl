<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 * @updated 	  MinhNc
 */
?>
<ul class="forum_categories">
    <li>
        <?php if (count($this->subForums) > 0):?>
            <ul class="forum_boxarea">
                <li class="forum_boxarea_header">
                    <div class="forum_boxarea_latestthread"> 
                        <?php echo $this->translate('Latest Posts') ?> 
                    </div>
                    <div class="forum_boxarea_forums"> 
                        <?php echo $this->translate('Forums')?>
                    </div>				
                </li>
                <li class="forum_boxarea_body">                        
                    <?php
                        foreach ($this->subForums as $forum) {
                            echo $this->partial('_forum.tpl', array(
                                'forum' => $forum, 
                                'user' => $this->user,
                                'lastTopics' => $this->lastTopics,
                                'lastPosts' => $this->lastPosts));
                        }
                    ?>
                </li>			
            </ul>
        <?php endif; ?>
		<?php
    		$options['0'] = $this->translate('Moderate Topics').' (0)';
			if($this -> canDelete)
           	 	$options['delete'] = $this->translate('Delete');
			if($this -> canSticky)
			{
                $options['stick'] = $this->translate('Stick Topics');
                $options['unstick'] = $this->translate('Un-stick Topics');
			}
			if($this -> canClose)
			{
                $options['close'] = $this->translate('Close Topics');
                $options['open'] = $this->translate('Open Topics');
			} 
    		?>
        <?php if (count($this->stickyTopics) > 0) : ?>
            <ul class="forum_boxarea">
                <li class="forum_boxarea_header">
                    <div class="forum_boxarea_latest_replies">
                        <?php echo $this->translate('Latest Posts') ?>
                    </div>                    
                    <div class="forum_boxarea_replies_views">
                        <?php echo $this->translate('Posts/Views') ?>
                    </div>
                    <?php if ($this->canApprove) : ?>
                        <div class="forum_boxarea_unapproved_posts_views">
                            <?php echo $this->translate('Unapproved posts') ?>
                        </div>
                    <?php endif; ?>
                    <div class="forum_boxarea_note"></div>
                    <div class="forum_boxarea_stickey_posts">
                        <?php echo $this->translate('Sticky Topics') ?>
                    </div>				
                </li>
                <li class="forum_boxarea_body">
                    <?php 
                        foreach ($this->stickyTopics as $stickyTopic) : 
                            $stickyTopicOwner = $this->user($stickyTopic->user_id);
                            $stickyLastPost = $stickyTopic->getLastCreatedPost();
                            if ($stickyLastPost) {
                                $stickyLastUser = $this->user($stickyLastPost->user_id);
                            } 
                    ?>
                        <div class="advforum_stickypost">
                        	<?php if(count($options) > 1):?>
                        		<input type='checkbox' class='checkbox' name='check_<?php echo $stickyTopic->topic_id; ?>' value='<?php echo $stickyTopic->topic_id; ?>' />
                            <?php endif;?>
                            <div class="forum_icon">
                                <a href="javascript:void(0);">
                                    <?php if ($stickyTopic->closed) : ?>
                                        <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_closed.png" />
                                    <?php elseif ($stickyTopic->approved_post_count >= $this->numberOfPostOfHotTopic) : ?>
                                        <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_hot_topics.png" />
                                    <?php elseif ($stickyTopic->approved_post_count > 1) : ?>   
                                        <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_has_reply.png" />    
                                    <?php else : ?>
                                        <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_no_reply.png" />
                                    <?php endif; ?>  
                                    <!-- Unread icon -->
	                                <?php if(!$stickyTopic->isViewedLastPost($this->viewer)):?>
										<img  class = "advforum_absolute" alt = "" src="application/modules/Ynforum/externals/images/advforum_unread_icon.png"/>
	                                <?php endif;?>  
                                </a>
                            </div>
                            <?php if ($stickyLastPost) : ?>
                                <div class="forum_boxarea_bodylatest_replies">						
                                    <div class="forum_info_date">
                                        <div> 
                                            <?php echo $this->translate('by') ?> 
                                            <?php echo $this->htmlLink($stickyLastUser->getHref(), $stickyLastUser->getTitle())?>
                                            <a href="<?php echo $this->url(array(
                                                'slug' => $stickyTopic->getSlug(), 
                                                'action' => 'view', 
                                                'topic_id' => $stickyTopic->getIdentity(),
                                                'post_id' => $stickyLastPost->getIdentity()), 'ynforum_topic')?>" title="<?php echo $this->translate('Go to the last post')?>">    
                                                <img src="application/modules/Ynforum/externals/images/lastpost-right.png" />
                                            </a>
                                        </div>
                                        <div> 
                                            <?php echo $this->timestamp($stickyLastPost->creation_date, array('class' => 'timestamp'))?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="forum_reply_view">
                                <span> <?php echo $this->locale()->toNumber($stickyTopic->approved_post_count)?> / <?php echo $this->locale()->toNumber($stickyTopic->view_count)?> </span>
                            </div>
                            <?php if ($this->canApprove) : ?>
                                <div class="forum_unapproved_posts_view">
                                    <span> <?php echo $this->locale()->toNumber($stickyTopic->post_count - $stickyTopic->approved_post_count)?></span>
                                </div>
                            <?php endif; ?>
                            <div class="forum_note">
                                <!--TODO : This part is reserved for the rating features and attachments in a topic-->
                                 <?php $avgrating = Engine_Api::_()->getApi('core', 'ynforum')->getAvgTopicRating($stickyTopic->getIdentity());
	                        	for($i = 1; $i <= 5; $i++): ?>
						          <img id="topic_rate_<?php print $i;?>" src="application/modules/Ynforum/externals/images/<?php if ($i <= $avgrating): ?>star_full.png<?php elseif( $i > $avgrating &&  ($i-1) <  $avgrating): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
						        <?php endfor; ?>
	                            <?php $attach = "";
	                            	if(Engine_Api::_()->getApi('core', 'ynforum')->checkAttach($stickyTopic)):
										$attach = '<span><img src="./application/modules/Ynforum/externals/images/attach.png"/></span>';
									endif;
									echo $attach;?> 
                            </div>					
                            <div class="forum_posts_title">
                                <div> 
                                    <?php echo $this->htmlLink($stickyTopic->getHref(), $stickyTopic->getTitle())?>
                                    <?php echo $this->pageLinks($stickyTopic, $this->forum_topic_pagelength, null, 'forum_pagelinks') ?>
                                </div>
                                <p class="forum_info_date">
                                    <?php echo $this->translate('Posted on')?>
                                    <?php echo $this->timestamp($stickyTopic->creation_date, array('class' => 'timestamp')) ?>
                                    <?php echo $this->translate('by')?>            
                                    <?php 
                                        echo $this->htmlLink($stickyTopicOwner->getHref(), 
                                            $this->string()->truncate($this->string()->stripTags($stickyTopicOwner->getTitle()), 18), 
                                            array('title' => $stickyTopicOwner->getTitle()))
                                    ?>       
                                </p>
                            </div>					
                        </div>			
                    <?php endforeach;?>
                </li>			
            </ul>
        <?php endif; ?>
        <ul class="forum_boxarea">
            <li class="forum_boxarea_header">
                <div class="forum_boxarea_latest_replies"> 
                    <?php echo $this->translate('Latest Posts') ?> 
                </div>
                <div class="forum_boxarea_replies_views">
                    <?php echo $this->translate('Posts/Views') ?>
                </div>
                <?php if ($this->canApprove) : ?>
                    <div class="forum_boxarea_unapproved_posts_views">
                        <?php echo $this->translate('Unapproved posts') ?>
                    </div>
                <?php endif; ?>
                <div class="forum_boxarea_note"></div>
                <div class="forum_boxarea_stickey_posts">
                    <?php echo $this->translate('Normal Topics') ?>
                </div>				
            </li>
            <li class="forum_boxarea_body">
                <?php
                    foreach ($this->paginator as $i => $topic):
                        $last_post = $topic->getLastCreatedPost();
                        if ($last_post) {
                            $last_user = $this->user($last_post->user_id);
                        } 
                        $topic_user = $this->user($topic->user_id);
                ?>
                    <div class="advforum_normalpost">
                    	<?php if(count($options) > 1):?>
                    		<input type='checkbox' class='checkbox' name='check_<?php echo $topic->topic_id; ?>' value='<?php echo $topic->topic_id; ?>' />
                        <?php endif;?>
                        <div class="forum_icon">
                            <a href="javascript:void(0);">
                                <?php if ($topic->closed) : ?>
                                    <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_closed.png" />
                                <?php elseif ($topic->approved_post_count >= $this->numberOfPostOfHotTopic) : ?>
                                    <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_hot_topics.png" />
                                <?php elseif ($topic->approved_post_count > 1) : ?>   
                                    <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_has_reply.png" />    
                                <?php else : ?>
                                    <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_no_reply.png" />
                                <?php endif; ?> 
								<!-- Unread icon -->
                                <?php if(!$topic->isViewedLastPost($this->viewer)):?>
									<img  class = "advforum_absolute" alt = "" src="application/modules/Ynforum/externals/images/advforum_unread_icon.png"/>
                                <?php endif;?>
                            </a>
                        </div>
                        <?php if (isset($last_post)) : ?>
                            <div class="forum_boxarea_bodylatest_replies">						
                                <div class="forum_info_date">
                                    <div> 
                                        <?php echo $this->translate('by') ?> 
                                        <?php 
                                            echo $this->htmlLink($last_user->getHref(), 
                                                $this->string()->truncate($this->string()->stripTags($last_user->getTitle()), 18), 
                                                array('title' => $last_user->getTitle()))
                                        ?>
                                        <a href="<?php echo $this->url(array(
                                            'slug' => $topic->getSlug(), 
                                            'action' => 'view', 
                                            'topic_id' => $topic->getIdentity(),
                                            'post_id' => $last_post->getIdentity()), 'ynforum_topic')?>" title="<?php echo $this->translate('Go to the last post')?>">
                                            <img src="application/modules/Ynforum/externals/images/lastpost-right.png" />
                                        </a>
                                    </div>
                                    <div> 
                                        <?php echo $this->timestamp($last_post->creation_date, array('class' => 'timestamp'))?>
                                    </div>             							
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="forum_reply_view">
                            <span> <?php echo $this->locale()->toNumber($topic->approved_post_count)?> / <?php echo $this->locale()->toNumber($topic->view_count)?> </span>
                        </div>
                        <?php if ($this->canApprove) : ?>
                            <div class="forum_unapproved_posts_view">
                                <span> <?php echo $this->locale()->toNumber($topic->post_count - $topic->approved_post_count)?></span>
                            </div>
                        <?php endif; ?>
                        <div class="forum_note">
                            <!--TODO : This part is reserved for the rating features and attachments in a topic-->
                            <?php $avgrating = Engine_Api::_()->getApi('core', 'ynforum')->getAvgTopicRating($topic->getIdentity());
                        	for($i = 1; $i <= 5; $i++): ?>
					          <img id="topic_rate_<?php print $i;?>" src="application/modules/Ynforum/externals/images/<?php if ($i <= $avgrating): ?>star_full.png<?php elseif( $i > $avgrating &&  ($i-1) <  $avgrating): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
					        <?php endfor; ?>
                            <?php $attach = "";
                            	if(Engine_Api::_()->getApi('core', 'ynforum')->checkAttach($topic)):
									$attach = '<span><img src="./application/modules/Ynforum/externals/images/attach.png"/></span>';
								endif;
								echo $attach;?> 
								
                        </div>					
                        <div class="forum_posts_title">
                            <div>
                                <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle())?>
                                <?php echo $this->pageLinks($topic, $this->forum_topic_pagelength, null, 'forum_pagelinks') ?>
                            </div>
                            <p class="forum_info_date">
                                <?php echo $this->translate('Posted on') ?>          
                                <?php echo $this->timestamp($topic->creation_date, array('class' => 'timestamp'))?>
                                <?php echo $this->translate('by') ?>
                                <?php echo $this->htmlLink($topic_user->getHref(), $topic_user->getTitle())?>
                            </p>
                        </div>					
                    </div>
                <?php endforeach;?>
                <?php if($this -> paginator -> getTotalItemCount()): ?>
	                <div class="advforum_normalpost ynforum_view_options">
	                    <div class="forum_addnew_button ynforum_moderate">
	                    	<div class="topic_selectAll clearfix">
	                    		<?php if(count($options) > 1): ?>
	                    		<div class= "ynforum_moderate_select">
		                    		<a href="javascript:;" onclick="selectAll()"><?php echo $this -> translate("Select All Topics");?></a>
		                    		<span class="ynforum_hide">/</span>
		                    		<a href="javascript:;" style="padding-right: 50px"  onclick="unSelectAll()"><?php echo $this -> translate("Deselect All Topics");?></a>
	                    		</div>
	                    		<?php endif;?>
	                    		<div class="ynforum_moderate_form">
			                    	<form id="topic_moderate_form" method="post" onsubmit="return false">
			                    	<?php
										if(count($options) > 1):
				                       		echo $this->formSelect('topic_moderate', null, null, $options);?>
						                      <input type="hidden" name="topic_ids" id="topic_ids" value="" />
						                      <button type="submit" name="moderate" onclick="return checkSelected()">
						                            <?php echo $this->translate('Go') ?>
						                        </button>
						                <?php endif;?>
				                     </form>
		                     	</div>
			                     	<div class="ynforum_moderate_markall">
		                    			<a href="<?php echo $this -> url(array('mark_read_all' => true))?>"><?php echo $this -> translate("Mark All as Read");?></a>
		                    		</div>
	                    	</div>
	                    </div>
	                <?php endif;?>
                    <?php
                        echo $this->partial('_quick_navigation.tpl', array(
                            'categories' => $this->categories,
                            'forums' => $this->forums,
                            'viewer' => $this->viewer,
                            'check_permission' => $this->check_permission
                        ));
                    ?>
                    <div class="forum_boxarea_body_control">
                        <div style="clear: both"> 
                            <form method="get">
                                <?php
                                    $options = array(
                                        '' => $this->translate('Show topics from'),
                                        '1' => $this->translate('1 day'),
                                        '2' => $this->translate('%1$d days', 2),
                                        '7' => $this->translate('1 week'),
                                        '10' => $this->translate('%1$d days', 10),
                                        '14' => $this->translate('%1$d weeks', 2),
                                        '30' => $this->translate('1 month'),
                                        '45' => $this->translate('%1$d days', 45),
                                        '60' => $this->translate('%1$d months', 2),
                                        '75' => $this->translate('%1$d days', 75),
                                        '100' => $this->translate('%1$d days', 100),
                                        '365' => $this->translate('1 year'),
                                    );
                                    echo $this->formSelect('topic_from', $this->topicFrom, null, $options);
                                ?>

                                <?php
                                    $viewCriterias = array(
                                        '' => $this->translate('Sort topics by'),
                                        'title' => $this->translate('Title'),
                                        'creation_date' => $this->translate('Post created time'),
                                        'approved_post_count' => $this->translate('Reply count'),
                                        'view_count' => $this->translate('View count'),
                                        'displayname' => $this->translate('Post user'),
                                    );
                                    echo $this->formSelect('sort_topic_by', $this->sortTopicBy, null, $viewCriterias);
                                ?>

                                <?php
                                    $sortDirections = array(
                                        '' => $this->translate('Order topics by'),
                                        'ASC' => $this->translate('Ascending'),
                                        'DESC' => $this->translate('Descending'),
                                    );
                                    echo $this->formSelect('order_direction', $this->orderDirection, null, $sortDirections);        
                                ?>
                                <button type="submit">
                                    <?php echo $this->translate('Show Topics') ?>
                                </button>
                            </form>    
                        </div>
                        <div class="advforum_viewmore">
                            <?php echo $this->paginationControl($this->paginator); ?>
                        </div>
                    </div>				
                </div>					
            </li>			
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
    </li>
</ul>

<script type='text/javascript'>
    var count = 0;
    en4.core.runonce.add(function()
    {
        if($('global_advforum_search_field')){
            new OverText($('global_advforum_search_field'), {
                poll: true,
                pollInterval: 600,
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
           $$('li.forum_boxarea_body input[type=checkbox]').addEvent('click', function()
           {
				if(this.checked == true)
				{
					count ++;
				}
				else
				{
					count --;
				}
				$('topic_moderate').childNodes[1].text = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
				$('topic_moderate').childNodes[1].label = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
			});
    });
    $$('.forum_select_quick_navigation').addEvent('change', function() {
        if (this.value) {
            window.location.href = this.value;
        }
    });
    
    function selectAll()
    {
    	var checks = $$('li.forum_boxarea_body input[type=checkbox]');
		checks.each(function(i)
		{
	 		i.checked = true;
		});
		count = checks.length;
		$('topic_moderate').childNodes[1].text = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
		$('topic_moderate').childNodes[1].label = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
    }
    function unSelectAll()
    {
    	var checks = $$('li.forum_boxarea_body input[type=checkbox]');
		checks.each(function(i)
		{
	 		i.checked = false;
		});
		count = 0;
		$('topic_moderate').childNodes[1].text = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
		$('topic_moderate').childNodes[1].label = "<?php echo $this->translate('Moderate Topics'); ?>" + " (" + count +")";
    }
    function checkSelected()
    {
    	var checks = $$('li.forum_boxarea_body input[type=checkbox]');
    	var topic_ids = "";
    	
    	checks.each(function(i)
		{
	 		if(i.checked == true)
	 		{
	 			topic_ids += i.value + ",";
	 		}
		});
		if($('topic_moderate').value == 0)
		{
			alert("<?php echo $this->translate('Please select a action to moderate.'); ?>");
			return false;
		}
		if(topic_ids == "")
		{
			alert("<?php echo $this->translate('Please select a topic to moderate.'); ?>");
			return false;
		}
		$('topic_ids').value = topic_ids;
		if(confirm("<?php echo $this->translate('Are you sure you want to moderate the selected topics?'); ?>"))
		{
			$('topic_moderate_form').submit();
		}
        return false;
    }
</script>