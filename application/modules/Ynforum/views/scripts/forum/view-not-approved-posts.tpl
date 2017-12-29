<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>

<h2>
    <?php echo $this->htmlLink(array('route' => 'ynforum_general'), $this->translate("Forums")); ?>
    &#187; 
    <?php
    echo $this->htmlLink(array(
        'route' => 'ynforum_forum',
        'forum_id' => $this->forum->getIdentity()
        ), $this->forum->getTitle());
    ?>    
</h2>

<h2><?php echo $this->translate('List of unapproved posts')?></h2>

<div class="advforum_post_detail advforum_not_approved_post">
    <?php if (count($this->topics) > 0) : ?>
    <ul class="forum_categories">
        <?php foreach ($this->topics as $topic) : ?>
            <ul class="forum_categories">
                <li>
                    <div>
                        <h3 class="forum_topic_title">
                            <?php echo $this->translate('Topic')?>
                            &#187; 
                            <?php
                                echo $this->htmlLink(array(
                                    'route' => 'ynforum_topic',
                                    'topic_id' => $topic->getIdentity(),
                                    'slug' => $topic->getSlug(),
                                        ), $topic->getTitle());
                            ?>
                        </h3>
                    </div>    
                </li>
            </ul>
            <?php foreach ($topic->getPosts() as $i => $post): ?>   
                <ul class="forum_boxarea">
                    <li class="forum_boxarea_header">
                        <div class="forum_boxarea_forums"> 
                            <img alt="" src="application/modules/Ynforum/externals/images/post/advform_latestthread.png">
                            <span class="advforum_postdetail_datepost"> 
                                <?php echo $this->locale()->toDateTime(strtotime($post->creation_date)) ?>
                            </span>
                            <div class="forum_topic_posts_top_options">
                                <a onclick="hide(this);" href="<?php echo $this->url(array('post_id' => $post->getIdentity(), 'action' => 'approve'), 'ynforum_post'); ?>?return-url=<?php echo $this->url() ?>" class="buttonlink icon_forum_post_approve"><?php echo $this->translate('Approve'); ?></a>
                                <a href="<?php echo $this->url(array(
                                            'slug' => $topic->getSlug(), 
                                            'action' => 'view', 
                                            'topic_id' => $topic->getIdentity(),
                                            'post_id' => $post->getIdentity()), 'ynforum_topic')?>" class="buttonlink icon_forum_post_watch"><?php echo $this->translate('Browse'); ?></a>
                                <a href="<?php echo $this->url(array('post_id' => $post->getIdentity(), 'action' => 'edit'), 'ynforum_post'); ?>?return-url=<?php echo $this->url() ?>" class="buttonlink icon_forum_post_edit"><?php echo $this->translate('Edit'); ?></a>
                                <a href="<?php echo $this->url(array('post_id' => $post->getIdentity(), 'action' => 'delete'), 'ynforum_post'); ?>?return-url=<?php echo $this->url() ?>" class="buttonlink smoothbox icon_forum_post_delete"><?php echo $this->translate('Delete'); ?></a>

                                <?php if ($this->viewer()->getIdentity() && $post->user_id != $this->viewer()->getIdentity()): ?>
                                    <?php
                                    echo $this->htmlLink(array(
                                        'route' => 'default',
                                        'module' => 'core',
                                        'controller' => 'report',
                                        'action' => 'create',
                                        'subject' => $post->getGuid(),
                                        'format' => 'smoothbox',
                                            ), $this->translate('Report'), array(
                                        'class' => 'buttonlink icon_report smoothbox',
                                    ))
                                    ?>
                                <?php endif; ?>
                            </div>
                        </div>				
                    </li>
                    <li class="forum_boxarea_body">
                        <div class="advforum_boxarea_postcontent">
                            <p class="forum_topic_posts_info_body yntinymce">
                                <?php
                                    $body = $post->body;
                                    if (strip_tags($body) == $body) {
                                        $body = nl2br($body);
                                    }
                                    if (!$this->decode_html && $this->decode_bbcode) {
                                        $body = $this->BBCode($body, array('link_no_preparse' => true));
                                    }
                                    echo $body;
                                ?>
                            </p>
                        </div>	
                    </li>
                </ul>
            <?php endforeach; ?>
        <?php endforeach; ?>  
    </ul>
    <?php else : ?>
        <?php echo $this->translate('There is no post waiting for your approval !!!');?>
    <?php endif; ?>
</div>

<div class="advforum_viewmore">
    <?php echo $this->paginationControl($this->paginator); ?>
</div>
<script text="">
	var hide = function(obj)
	{
		obj.hide();
	}
</script>
