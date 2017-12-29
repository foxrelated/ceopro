<?php 
    $count = 0;
    $forum_id = 0;
    $forum = null;
    $forum_auth = true;
    foreach ($this->topics as $topic) {
        if ($topic->forum_id != $forum_id) {
            $forum_id = $topic->forum_id;
            $forum = Engine_Api::_()->getItem('forum_forum', $forum_id);
            $forum_auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($forum, null, 'view') -> checkRequire();
        }
        if ($forum_auth) $count++;
    }
?>
<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $count),$count)?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route'=>$this->route), $this->label_content, array());?></span>
</div>
<div id="ynadvsearch_forum_result">
<?php if (count($this->paginator) > 0): ?>
    <ul class="ynadvsearch_forum_lists">
    <?php 
        $forum_id = 0;
        $forum = null;
        $forum_auth = true;
    ?>
    <?php foreach($this->paginator as $topic) :?>
    <?php if ($topic->forum_id != $forum_id) :?>
        <?php $forum_id = $topic->forum_id?>
        <?php $forum = Engine_Api::_()->getItem('forum_forum', $forum_id)?>
        <?php $forum_auth = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')-> setAuthParams($forum, null, 'view') -> checkRequire();?>
        <?php if (!$forum_auth) continue; ?>
    <?php endif; ?>
    <?php if (!$forum_auth) continue; ?>
    <li class="ynadvsearch_forum_item">
        <div class="topic_search">
            <div class="forum_icon">
                <a href="javascript:void(0);">
                    <?php if ($this->ynforum_enable) : ?>
                        <?php if ($topic->closed) : ?>
                            <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_closed.png" />
                        <?php elseif ($topic->approved_post_count >= $this->numberOfPostOfHotTopic) : ?>
                            <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_hot_topics.png" />
                        <?php elseif ($topic->approved_post_count > 1) : ?>   
                            <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_has_reply.png" />    
                        <?php else : ?>
                            <img alt="" src="application/modules/Ynforum/externals/images/types/advforum_no_reply.png" />
                        <?php endif; ?> 
                    <?php else : ?>
                        <?php if( $topic->isViewed($this->viewer())): ?>
                            <?php echo $this->htmlLink($topic->getHref(), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Forum/externals/images/topic.png')) ?>
                        <?php else: ?>
                            <?php echo $this->htmlLink($topic->getHref(), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Forum/externals/images/topic_unread.png')) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
            </div>
            <div class="forum_posts_title">
                <div> 
                    <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle())?>
                </div>
                <p class="forum_info_date">
                    <?php $topic_user = $this->user($topic->user_id);?>
                    <?php echo $this->translate('Posted on') ?>          
                    <?php echo $this->timestamp($topic->creation_date, array('class' => 'timestamp'))?>
                    <?php echo $this->translate('by') ?>
                    <!-- TODO check $topic_user available -->
                    <?php echo $this->htmlLink($topic_user->getHref(), $topic_user->getTitle())?>
                </p>
            </div>  
        </div>        
    </li>
    <?php endforeach; ?>
    </ul>
    
    <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate('There were no topics found matching your search criteria.')?></span>
</div>    
<?php endif;?>
</div>