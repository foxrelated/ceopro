<?php $user = $this->user($this->post->user_id); ?>
<?php $signature = $this->post->getSignature(); 
$showCheck = false;
if($this -> canDelete)
 	$showCheck = true;
if($this -> canApprove)
   $showCheck = true;
?>
<?php
    if ($this->viewer->getIdentity() && $this->viewer->getIdentity() != $user->getIdentity() && $user->getIdentity()) {
        $menus = array();
        $menus[] = array(
            'label' => '> Report User',
            'class' => 'smoothbox',
            'route' => 'default',
            'params' => array(
                'module' => 'core',
                'controller' => 'report',
                'action' => 'create',
                'subject' => $user->getGuid(),
                'format' => 'smoothbox',
            ),
        );

        $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
        $subject = $user;
        if (!$direction) { //one-way mode
            $row = $subject->membership()->getRow($this->viewer);
            $friendship_status = $this->viewer->membership()->getRow($subject);
        } else { //two-way mode
            $row = $this->viewer->membership()->getRow($subject);
            $friendship_status = $subject->membership()->getRow($this->viewer);
        }
        if ($row == null && $user->getIdentity()) {
            $menus[] = array(
                'label' => '> Add to My Friends',
                'class' => 'smoothbox',
                'route' => 'user_extended',
                'params' => array(
                    'controller' => 'friends',
                    'action' => 'add',
                    'user_id' => $subject->getIdentity()
                ),
            );
        }
        if( $friendship_status && $friendship_status->active && $user->getIdentity()) {
            $menus[] = array(
                'label' => "> Send Message",
                'route' => 'messages_general',
                'params' => array(
                    'action' => 'compose',
                    'to' => $subject->getIdentity()
                ),
            );
        }

        $navigation = new Zend_Navigation();
        $navigation->addPages($menus);
    }
?>
<ul class="forum_boxarea" id="forum_post_<?php echo $this->post->getIdentity()?>">
    <li class="forum_boxarea_header">
    	<?php if($showCheck):?>
    		<input type='checkbox' class='checkbox' name='check_<?php echo $this->post->getIdentity(); ?>' value='<?php echo $this->post->getIdentity(); ?>' />
        <?php endif;?>
        <?php if ($this->post->approved && $this->canPost) : ?>
            <div class="advforum_boxarea_quotelink"> 
                <?php
                    echo $this->htmlLink(array(
                            'route' => 'ynforum_topic',
                            'action' => 'post-create',
                            'topic_id' => $this->subject()->getIdentity(),
                            'quote_id' => $this->post->getIdentity()), 
                        $this->translate('Quote this'));
                ?>
            </div> 
        <?php endif; ?>
        
        <div class="forum_boxarea_forums"> 
            <?php if ($this->index == 0) : ?>
                <img alt="" src="application/modules/Ynforum/externals/images/post/advform_latestthread.png">
            <?php else : ?>
                <img alt="" src="application/modules/Ynforum/externals/images/post/advforum_reply.png">
            <?php endif; ?>
            <span class="advforum_postdetail_datepost"> 
                <?php echo $this->locale()->toDateTime(strtotime($this->post->creation_date)) ?>
            </span>
            <?php if ($this->post->approved): ?>    
                <?php
                    if ($this->viewer->getIdentity()) {
                        echo $this->htmlLink(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'share',
                            'type' => $this->post->getType(),
                            'id' => $this->post->getIdentity(),
                            'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox buttonlink icon_forum_share_post', 'title' => 'Share this post'));
						if((Engine_Api::_() -> hasModuleBootstrap('blog') || Engine_Api::_() -> hasModuleBootstrap('ynblog')) 
						&& Engine_Api::_()->authorization()->isAllowed('blog', $this->viewer,'create' ))
						{
                        	echo $this->htmlLink(array(
                            'route' => 'ynforum_blog',                           
                            'action' => 'create',
                            'forum_id' => $this-> forum -> getIdentity(),
                            'post_id' => $this->post->getIdentity()
                            ), $this->translate('Blog'), array('class' => 'buttonlink icon_forum_blog_post', 'title' => 'Blog this post')); 
						}									
                    }
                ?>
                <?php 
                    if ($this->viewer->getIdentity() && $user->getIdentity() != $this->viewer->getIdentity()) {
                        if (!$this->post->isThanked($this->viewer->getIdentity())) {
                            echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'thank'), 'ynforum_post'),
                                    $this->translate('Thank'), array(
                                        'id' => 'link' . $this->post->getIdentity(),
                                        'class' => 'buttonlink icon_forum_thank_post'));
                        }
                    }
                ?>

                <?php 
                    if ($this->viewer->getIdentity() && $user->getIdentity() != $this->viewer->getIdentity()) {
                        if (!$this->post->isAddedReputationBy($this->viewer->getIdentity())) 
                        {
                            echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'add-reputation', 'format'=>'smoothbox'), 'ynforum_post'),
                                  $this->translate('Add reputation'), array('class' => 'buttonlink icon_forum_reputation_post'));
                        } 
                    }
                ?>
                <?php 
                    if ($this->viewer()->getIdentity() && $user->getIdentity() != $this->viewer()->getIdentity()) {
                        echo $this->htmlLink(array(
                            'route' => 'default',
                            'module' => 'core',
                            'controller' => 'report',
                            'action' => 'create',
                            'subject' => $this->post->getGuid(),
                        ), $this->translate('Report'), array('class' => 'smoothbox buttonlink icon_forum_report_post'));
                    } 
                ?>
            <?php endif; ?>
        </div>				
    </li>
    <li class="forum_boxarea_body">
        <div class="advforum_member_info">
            <div class="advforum_membername">
                <a href="<?php echo $user->getHref()?>">
                    <?php if($user->getIdentity()) echo $this->string()->truncate($user->getTitle(),15); else echo $user->getTitle();?>
                </a>
                <?php if($user->getIdentity()):?>
                	<div class="floatRight">
						<?php
	            	 	$onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
				        $step = 900;
				        $select = $onlineTable -> select() -> where('user_id=?', (int)$user -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
						
				        $online = $onlineTable -> fetchRow($select);
		        		if(is_object($online)): ?>
		        			<img src="application/modules/Ynforum/externals/images/badges/onlineTab.gif" border="0" alt="Offline" title="Online">
						<?php else:?>
							<img src="application/modules/Ynforum/externals/images/badges/offlineTab.gif" border="0" alt="Offline" title="Offline">
						<?php endif;?>
					</div>   
				<?php endif;?>             
            </div>
            <?php
            $topic = $this->post -> getParent();
			$forum = $topic -> getParent();
             if($forum->isModerator($user)) echo $this->translate("(Moderator)")?>
            <div class="advforum_avartamember">
                <a href="<?php echo $user->getHref()?>">
                    <?php echo $this->itemPhoto($user); ?>
                </a>   
            </div>
            <ul class="ynforum_topic_information">
                <?php if ($this->post->user_id != 0): ?>
                    <?php if ($user): ?>
                        <?php if ($this->isModeratorPost): ?>
                            <li class="forum_topic_posts_author_info_title"><?php echo $this->translate('Moderator') ?></li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($signature): ?>
                    <li>
                        <div><?php echo $this->translate('Post(s)'); ?></div>
                        <span><?php echo $this->locale()->toNumber($signature->approved_post_count) ?></span>
                    </li>
                    <li>
                        <div><?php echo $this->translate('Thank(s)') ?></div>
                        <span><?php echo $this->locale()->toNumber($signature->thanks_count) ?></span>
                    </li>    
                    <li>
                        <div><?php echo $this->translate('Thanked') ?></div>
                        <span><?php echo $this->locale()->toNumber($signature->thanked_count) ?></span>
                    </li>    
                    <li>
                        <div><?php echo $this->translate('cR(s)') ?></div>
                        <span style="padding-right: 20px"><?php echo $this->locale()->toNumber($signature->positive) ?></span>
                        <span><?php echo $this->locale()->toNumber($signature->neg_positive) ?></span>
                    </li>    
                <?php endif; ?>											
            </ul>	
            <?php if (isset ($navigation)) : ?>
                <div class="advforum_postdetail_quicklinks">
                    <?php 
                            echo $this->navigation()
                                ->menu()
                                ->setContainer($navigation)
                                ->setPartial(array('_navIcons.tpl', 'ynforum'))
                                ->render();
                    ?>
                </div>				
            <?php endif; ?>
        </div>						
        <div class="advforum_boxarea_postcontent">
            <div class="forum_topic_title"> 
                <h3>
            	<?php
            	$url_img = 'application/modules/Ynforum/externals/images/post/advform_latestthread.png';
            	if(isset($this->post->icon_id) && $this->post->icon_id)
				{
											
					$iconTopic = Engine_Api::_()->getItemTable('ynforum_icon')->find($this->post->icon_id)->current();
					if($iconTopic)
						$url_img = $iconTopic->getPhotoUrl('thumb.icon');
				}
            	?>
                <img alt="" src="<?php echo $url_img?>">	
                	<?php 
                	if($this->post->title)
                		echo $this->post->title;
                	else
                		echo $this -> translate("Untitled");
                	?></h3>
                <div class="forum_post_notice">
                    <?php
                        if ($this->viewer->getIdentity() == $user->getIdentity() && !$this->post->approved) {
                            echo ' (' . $this->translate('Your post is waiting for the approval') . ')';
                        }
                    ?>
                </div>                
            </div>
            <div class="forum_topic_posts_info_body yntinymce">
            <?php
                $body = $this->post->body;
                if ($body == strip_tags($body)) {
                    $body = nl2br($body);
                }
                if (!$this->decode_html && $this->decode_bbcode) {
                    $body = $this->BBCode($body, array('link_no_preparse' => true));
                }
                echo $body;
            ?>
            </div>
            <?php $photos = $this->post->getPhotos(); ?>
            <?php if (count($photos) > 0) :?>
                <div class="advforum_dashedline"></div>            
                <?php foreach ($this->post->getPhotos() as $photo): ?>
                    <div class="forum_topic_posts_info_photo">
                        <?php echo $this->itemPhoto($photo, null, '', array('class' => 'forum_post_photo')); ?>
                    </div>  
                <?php endforeach; ?>  
            <?php else : ?>
                <?php if ($this->post->file_id):?>
                <div class="forum_topic_posts_info_photo">                      
                    <?php echo $this->itemPhoto($this->post, null, '', array('class'=>'forum_post_photo'));?>
                </div>
                <?php endif;?>
            <?php endif; ?>
            <ul class="gallery yn-clearfix">
            	<?php
            	$album = $this -> post -> getSingletonAlbum();
				$postPhotos = $album -> getCollectiblesPaginator();	
				$postPhotos-> setItemCountPerPage(100);
            	foreach( $postPhotos as $photo ):?>
            		<li><a href="<?php echo $photo->getPhotoUrl('thumb.main')?>" rel="prettyPhoto[gallery2]" title=""><img src="<?php echo $photo->getPhotoUrl('thumb.normal')?>" width="60" height="60" alt="" /></a></li>           		
                	
			    <?php endforeach;?>
			    
            </ul>
                        
            <?php 
            $attachment = $this->post->getAttachments();
            if(count($attachment) > 0):?>
	            <div class="advforum_dashedline"></div>  
	            <?php 
				foreach($attachment as $attach): ?>
				<div class = "post_attachment">
					<span><img src="./application/modules/Ynforum/externals/images/attach.png"/></span>
				    <?php $file =  Engine_Api::_()->getItem('storage_file', $attach->file_id);
				    $req = Zend_Controller_Front::getInstance()->getRequest();
				    $domain = $req-> getScheme() . '://' . $req -> getHttpHost();
				    $path = $file->map();
					if(strstr($path, 'http') == "")
					{
						$path = $domain.$path;
					}
				    ?>
				    <a href="./application/modules/Ynforum/externals/scripts/download.php?f=<?php echo $path ?>&fc='<?php echo $attach->title ?>'"> <?php echo $attach->title ?> </a></td>
				    <span>(<?php if($file->size < 1024*1024):
				        echo round($file->size/(1024),2)." Kb)";
				        else:
				        echo round($file->size/(1024*1024),2)." Mb)";
				        endif;?> 
						</span>
				</div>
				<?php endforeach;?> 
            <?php endif;?>
            <?php if ($this->post->edit_id && !empty($this->post->modified_date)): ?>
                <div class="advforum_dashedline"></div>            
                <i>
                    <?php
                        $editedUser = $this->user($this->post->edit_id);
                        echo $this->translate('This post was edited by %1$s at %2$s', 
                                $this->htmlLink($editedUser->getHref(), $editedUser->getTitle()), 
                                $this->locale()->toDateTime(strtotime($this->post->modified_date)));
                    ?>
                </i>
                <br />
            <?php endif; ?>
            
            <?php if (!empty($signature->signature)) : ?>
            	<span style="padding-left: 10px">____________________________________</span>
            	<br />
            	<div class="advforum_signature"><?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('forum_bbcode', 0)) echo nl2br($this->BBCode($signature->signature)); else echo $signature->signature; ?></div>
            <?php endif;?>
        </div>	
    </li>
    <li class="forum_boxarea_footer">    	
    	<div class="forum_post_editing_tool">
            <?php 
                if (!$this->post->approved && $this->canApprove) 
                {
                    echo $this->htmlLink($this->url(array(
                            'post_id' => $this->post->getIdentity(),
                            'action' => 'approve'), 
                        'ynforum_post'), $this->translate('Approve'), array('class' => 'buttonlink icon_forum_post_approve'));
                } 
            ?>

            <?php
                if ($this->canEdit) {
                    echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'edit'), 'ynforum_post'), 
                        $this->translate('Edit'), array('class' => 'buttonlink icon_forum_post_edit'));
                } else {
                    if($this->canEdit_Post == Authorization_Api_Core::LEVEL_ALLOW) {
                        if( $this->post->user_id != 0 && $this->post->isOwner($this->viewer) && !$this->topic->closed ) {
                            echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'edit'), 'ynforum_post'), 
                                $this->translate('Edit'), array('class' => 'buttonlink icon_forum_post_edit'));
                        }
                    } elseif ($this->canEdit_Post == Authorization_Api_Core::LEVEL_MODERATE) {
                        echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'edit'), 'ynforum_post'), 
                                $this->translate('Edit'), array('class' => 'buttonlink icon_forum_post_edit'));
                    }
                }
            ?>

            <?php 
                if ($this->canDelete) 
                {
                    echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'delete'), 'ynforum_post'), 
                     $this->translate('Delete'), array('class' => 'buttonlink smoothbox icon_forum_post_delete'));
                } 
                else 
                {
                    if($this->canDelete_Post == Authorization_Api_Core::LEVEL_ALLOW) 
                    {
                        if( $this->post->user_id != 0 && $this->post->isOwner($this->viewer) && !$this->topic->closed ) {
                            echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'delete'), 'ynforum_post'), 
                                $this->translate('Delete'), array('class' => 'buttonlink smoothbox icon_forum_post_delete'));
                        }
                    } elseif ($this->canDelete_Post == Authorization_Api_Core::LEVEL_MODERATE) {
                        echo $this->htmlLink($this->url(array('post_id' => $this->post->getIdentity(),'action' => 'delete'), 'ynforum_post'), 
                                $this->translate('Delete'), array('class' => 'buttonlink smoothbox icon_forum_post_delete'));
                    }
                }
            ?>
        </div>	
    </li>
</ul>

<?php
    $thankedUserIds = $this->post->getThankedUserIds();
    if (count($thankedUserIds) > 0) {
        $thankUserLinks = array();
        foreach($thankedUserIds as $thankUserId) {
            $thankUser = $this->thankUsers[$thankUserId];
            array_push($thankUserLinks, $this->htmlLink($thankUser->getHref(), $thankUser->getTitle()));
        }
?>
<ul class="forum_categories">
    <li>
        <div> 
            <div> 
                <?php echo $this->translate('The following users say thanks to') ?> <?php echo $this->htmlLink($user->getHref(), $user->getTitle())?> <?php echo $this->translate('for this useful post:') ?> 
            </div>
            <div> 
                <?php echo $this->fluentList($thankUserLinks); ?>
            </div>				
        </div>
    </li>
</ul>
<?php
    }
?>
