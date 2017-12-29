<?php
$this->headLink()
  ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ynforum/externals/styles/tooltip.css');
    $subForums = $this->forum->getSubForums();
?>

<div>
    <div class="forum_icon">
        <?php echo $this->htmlLink($this->forum->getHref(), $this->itemPhoto($this->forum, 'thumb.icon')) ?>
    </div>
    <div class="forum_boxarea_newthread">	
        <?php 
            $posts = array();            
            if ($this->forum->lastpost_id && array_key_exists($this->forum->lastpost_id, $this->lastPosts)) {
                $last_post = $this->lastPosts[$this->forum->lastpost_id];
                $posts[] = $last_post;
            }
            if ($subForums) {
                foreach($subForums as $subForum) {
                    if ($subForum->lastpost_id) {
                        $last_post = $this->lastPosts[$subForum->lastpost_id];
                        $posts[] = $last_post;
                    }
                }
            }
            $hasLastPost = false;
        ?>
        <?php foreach($posts as $post) : ?>
            <?php if ($post->approved) : ?>
                <?php
                    $hasLastPost = true;
                    $last_topic = $this->lastTopics[$post->topic_id];
                    $last_user = $this->user($post->user_id);
					
					$table = Engine_Api::_()->getItemTable('ynforum_post');
					$select = $table -> select();
					$select -> where('topic_id = ?', $last_topic -> getIdentity());
					$select -> order('post_id ASC');
                    $select -> limit(1);
					$first_post = $table -> fetchRow($select);
					$url_img = 'application/modules/Ynforum/externals/images/post/advform_latestthread.png';
					if(isset($first_post->icon_id) && $first_post->icon_id)
					{
						$iconTopic = Engine_Api::_()-> getItem('ynforum_icon', $first_post->icon_id);
						if($iconTopic)
							$url_img = $iconTopic->getPhotoUrl('thumb.icon');
					}
                ?>
                <div>
                	<a class="link_forum_post yntooltip" href="<?php echo $last_topic->getHref();?>">
                    	<span>
			               <div class="blogs_browse_info">
			                    <div class ="description_content" style="color: #fff">                   
			                       <?php
	                                    echo $this->string()->truncate($this->string()->stripTags($first_post->body), 500);
                                    ?>
			                    </div>        
							</div>
			            </span>  
			            <?php echo $this->string()->truncate($this->string()->stripTags($last_topic->getTitle()), 40);?> 
			        </a>
                    <a href="<?php echo $this->url(array(
                        'slug' => $last_topic->getSlug(), 
                        'action' => 'view', 
                        'topic_id' => $last_topic->getIdentity(),
                        'post_id' => $post->getIdentity()), 'ynforum_topic')?>" 
                        title="<?php echo $this->translate('Go to the last post')?>">
                        <img src="application/modules/Ynforum/externals/images/lastpost-right.png" />
                    </a>
                    <p class="forum_info_date">
                        <?php                            
                            echo $this->translate('Last reply by %1$s on %2$s', 
                                $this->htmlLink($last_user->getHref(), 
                                $this->string()->truncate($this->string()->stripTags($last_user->getTitle()), 18), array('title' => $last_user->getTitle())), 
                                $this->timestamp($post->creation_date, array('class' => 'timestamp')));                    
                        ?>
                    </p>						
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (!$hasLastPost) : ?>
            <div class="forum_no_post">
                <?php echo $this->translate('There is no post in this forum')?>
            </div>
        <?php endif; ?>    
    </div>
    
    <div class="forum_title">
        <h3> 
            <?php echo $this->htmlLink($this->forum->getHref(), $this->forum->title)?>
            <?php if ($hasLastPost) : ?>
                <a href="<?php echo $this->url(array('action' => 'newest-posts', 'forum_id' => $this->forum->getIdentity()), 'ynforum_forum', true)?>"
                    title="<?php echo $this->translate('View newest posts')?>">
                    <img src="application/modules/Ynforum/externals/images/newest-posts.png" />
                </a>
            <?php endif; ?>
        </h3>
        <span>
        	<div>
                <b><?php echo $this->locale()->toNumber($this->forum->getTotalTopic())?></b> <?php echo $this->translate(array('topic', 'topics', $this->forum->approved_topic_count))?> 
           </div>
           <div>
                <b><?php echo $this->locale()->toNumber($this->forum->getTotalPost())?></b> <?php echo $this->translate(array('post', 'posts', $this->forum->approved_post_count)) ?>
            </div>
        </span>
        <?php if($this->forum->description!= ''):?>
        	<p class="forum_boxarea_description"> 
            	<?php echo $this->forum->description ?>
        	</p>
        <?php endif;?>
        
        <?php if ($subForums) : ?>
            <?php foreach ($subForums as $subForum) :?>
                <div class="forum_subforum">
                    <div class="forum_subforum_icon">
                        <a href="javascript:void(0);"><img alt="<?php echo $this->string()->stripTags($subForum->getTitle()); ?>" src="application/modules/Ynforum/externals/images/icon-subforum.png" /></a>
                    </div>
                    <div class="forum_subforum_title"> 
                        <h4> 
                            <?php echo $this->htmlLink($subForum->getHref(), 
                                $this->string()->truncate($this->string()->stripTags($subForum->getTitle()), 20),
                                array('title' => $subForum->getTitle()))?>
                        </h4>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>					
</div>