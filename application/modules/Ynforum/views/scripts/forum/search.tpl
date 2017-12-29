<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>
<div class="forum_breadcumb">
	<div>
    <?php
        echo $this->partial('_navigation.tpl', array(
            'linkedCategories' => $this->linkedCategories,
            'navigationForums' => $this->navigationForums,
        ));
    ?>
   </div>
</div> 
<div>
	<ul class="forum_categories">
    <li>
	    <div>
	    	<?php 
		        echo $this->partial('_form_search.tpl', array(
		            'searchInSubForums' => $this->searchInSubForums,
		            'title' => $this->title));
		    ?>		
	        <div class="forum_icon">
	            <?php echo $this->itemPhoto($this->forum, 'thumb.icon') ?>
	        </div>    
	        <h3> 
	            <?php echo $this->forum->getTitle() ?> 
	        </h3>
	        <p class="forum_boxarea_description"> 
	            <?php echo $this->forum->description ?> 
	        </p>	
	        <span> 
	            <?php if (count($this->moderators)) : ?>
	                <?php echo $this->translate('Moderated by') ?> 
	                <?php echo $this->fluentList($this->moderators) ?>,
	            <?php endif; ?>    
	            <?php echo $this->locale()->toNumber($this->forum->approved_topic_count)?> <?php echo $this->translate(array('topic', 'topics', $this->forum->approved_topic_count))?>,
	            &nbsp;<?php echo $this->locale()->toNumber($this->forum->approved_post_count)?> <?php echo $this->translate(array('post', 'posts', $this->forum->approved_post_count))?>,
	            &nbsp;<?php echo $this->translate(array('%1$s view', '%1$s views', $this->forum->view_count), $this->locale()->toNumber($this->forum->view_count))?>                    
	        </span>
	    </div>	
        <div class="advforum_search_result">
            <?php echo $this->paginator->getTotalItemCount()?>
            <?php echo $this->translate(array('result was', 'results were', $this->paginator->getTotalItemCount()))?>
            <?php echo $this->translate('found.') ?>
        </div>
        
        <?php if (count($this->paginator) > 0): ?>
            <ul class="forum_boxarea" style="padding-top: 20px">
                <li class="forum_boxarea_header">
                    <div class="forum_boxarea_latest_replies"> <?php echo $this->translate('Latest Posts') ?> </div>
                    <div class="forum_boxarea_replies_views"> <?php echo $this->translate('Posts/Views') ?> </div>
                    <div class="forum_boxarea_note"></div>
                    <div class="forum_boxarea_stickey_posts"> <?php echo $this->translate('Normal Posts') ?> </div>				
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
                                </a>
                            </div>
                            <div class="forum_boxarea_bodylatest_replies">						
                                <div class="forum_info_date">
                                    <div> 
                                        <?php echo $this->translate('by') ?> 
                                        <?php echo $this->htmlLink($last_user->getHref(), $last_user->getTitle())?>
                                        <?php if (isset($last_post)) : ?>
                                            <a href="<?php echo $this->url(array(
                                            'slug' => $topic->getSlug(), 
                                            'action' => 'view', 
                                            'topic_id' => $topic->getIdentity(),
                                            'post_id' => $last_post->getIdentity()), 'ynforum_topic')?>" title="<?php echo $this->translate('Go to the last post')?>">
                                                <img src="application/modules/Ynforum/externals/images/lastpost-right.png" />
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div> 
                                        <?php echo $this->timestamp($last_post->creation_date, array('class' => 'timestamp'))?>
                                    </div>             							
                                </div>
                            </div>
                            <div class="forum_reply_view">
                                <span> <?php echo $topic->approved_post_count?> / <?php echo $topic->view_count?> </span>
                            </div>
                            <div class="forum_note">
                                <!--TODO : This part is reserved for the rating features and attachments in a topic-->
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

                    <div class="advforum_normalpost">
                        <?php if ($this->canPost) : ?>
                            <div class="forum_addnew_button">
                                <form action="<?php echo $this->forum->getHref(array('action' => 'topic-create'))?>" method="get">
                                    <button type="submit"><?php echo $this->translate('Add New Topic') ?></button>						
                                </form>
                            </div>
                        <?php endif;?>
                        <div class="forum_boxarea_body_control">
                            <div class="advforum_viewmore">
                                <?php echo $this->paginationControl($this->paginator); ?>
                            </div>
                        </div>
                    </div>					
                </li>			
            </ul>
        <?php endif; ?>
    </li>
</ul>    

<script language="javascript" type="text/javascript">
    if($('search_forum_text')){
        new OverText($('search_forum_text'), {
            poll: true,
            pollInterval: 500,
            positionOptions: {
                position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                offset: {
                    x: ( en4.orientation == 'rtl' ? -4 : 4 ),
                    y: 0
                }
            }
        });
    }
</script>