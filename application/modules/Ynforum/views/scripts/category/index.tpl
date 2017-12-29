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
	    echo $this->partial('_navigation.tpl', array('linkedCategories' => $this->linkedCategories));
	?>
	</div>
	<div class="forum_user_dashboard">
		<?php 
		echo $this->htmlLink(array(
				'callback_url' => base64_encode($this -> url()),						
				'route' => 'ynforum_dashboard'),
				$this->translate('User Dashboard'),
				array('class' => 'buttonlink icon_forum_user_dashboard'));
		?>	           
	</div>  
	<div class="forum_block_title" style="margin-top: 0px">
	    <?php
	        echo $this->partial('_quick_navigation.tpl', array(
	            'categories' => $this->categories,
	            'forums' => $this->forums,
	            'viewer' => $this->viewer,
	            'check_permission' => $this->check_permission
	        ));
	    ?>
	</div>    
</div>
<ul class="forum_categories">
    <li>
        <div class="category">
            <div class="category_icon">
                <?php echo $this->itemPhoto($this->category, 'thumb.icon') ?>
            </div>
            <div class="category-info">
                <h3>
                    <?php 
                        echo $this->htmlLink($this->category->getHref(), $this->category->getTitle());
                    ?>
                </h3>
                <span><?php echo $this->category->description ?></span>
            </div>
        </div>

        <?php
            $cats = array();
            foreach ($this->categories as $cat) {
                if ($cat->parent_category_id == $this->category->getIdentity()) {
                    array_push($cats, $cat);
                }
            }
        ?>
        <ul class="forum_boxarea">   
	        <?php foreach ($cats as $cat) : ?>
	            <?php
	                echo $this->partial('_category.tpl', array(
	                                'category' => $cat, 
	                                'user' => $this->user,
	                                'lastTopics' => $this->lastTopics,
	                                'lastPosts' => $this->lastPosts,
	                                'forums' => $this->forums));
	            ?>
	        <?php endforeach; ?>
        
	        <?php if (array_key_exists($this->category->category_id, $this->forums)) : ?>
	                         
	                <li class="forum_boxarea_body">                        
	                    <?php
	                        foreach ($this->forums[$this->category->getIdentity()] as $forum) 
	                        {
	                        	$memberList = $forum->getMemberList();
								if ($memberList) {
								    $members = $memberList->getAllChildren();
								}
								$check_user_view = true;
								if(count($members) > 0)
								{
									if(!$forum->isMember($this->viewer) && !$forum->isModerator($this->viewer))
									{
										$check_user_view = false;
									}
								}
								if(($this->check_permission && $forum->authorization()->isAllowed($this->viewer,'view') && $check_user_view) || !$this->check_permission)
		                        {
		                            if (!$forum->parent_forum_id) {
		                                echo $this->partial('_forum.tpl', array(
		                                    'forum' => $forum, 
		                                    'user' => $this->user,
		                                    'lastTopics' => $this->lastTopics,
		                                    'lastPosts' => $this->lastPosts));
		                            }
								}
	                        }
	                    ?>
	                </li>			
	            
	        <?php endif; ?>
        </ul>
    </li>
</ul>

<?php
echo $this->partial('_quick_navigation.tpl', array(
    'categories' => $this->categories,
    'forums' => $this->forums,
    'viewer' => $this->viewer,
            'check_permission' => $this->check_permission
));
?>

<script language="javascript" type="text/javascript">
    $$('.forum_select_quick_navigation').addEvent('change', function() {
        window.location.href = this.value;
    });
</script>