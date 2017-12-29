<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>

<div class="forum_block_title">
    <h2><?php echo $this->translate('Forums') ?></h2>
    <?php
	echo $this -> partial('_quick_navigation.tpl', array('categories' => $this -> categories, 'forums' => $this -> forums, 'viewer' => $this -> viewer, 'check_permission' => $this -> check_permission));
    ?>
</div>    

<?php 
$auth = Engine_Api::_() -> authorization() -> context;
foreach($this->categories as $index => $category) { 
	 if($this -> check_permission && !$auth -> isAllowed($category, $this -> viewer, 'forumcat.view')) 
	 {
	    continue;
	 }
  ?>
    <?php if ($category->level == 0) { ?>
    	<?php
		$subCats = array();
		foreach ($this->categories as $cat) {
			if ($cat -> parent_category_id == $category -> getIdentity()) {
				$subCats[] = $cat;
			}
		}
        ?>
        <ul class="forum_categories">
            <li>
                <div class="category">
                    <div class="category-info">
                        <h3>
                            <?php
							echo $this -> htmlLink($category -> getHref(), $category -> title);
                            ?>                            
                        </h3>
                        <span style="width: 90%; display: inline-block; overflow: hidden; vertical-align: sub;"><?php echo $category->description ?></span>                        
                        <?php if ($category->forum_count > 0 || count(subCats) > 0):?>
                        

                        <span class="font-icon-collapse" style="float:right;" rel="ynforum_expand_cat" id="ynforum_expand_cat_<?php echo $category->category_id?>" onmousedown="toggleMenu('#forum_boxarea_'+ <?php echo $category->category_id?>,'ynforum_expand_cat_'+<?php echo $category->category_id?>); return false;">
                        	<i class="fa fa-plus-circle fa-2x"></i>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
				
			 	<ul class="forum_boxarea parent" id="forum_boxarea_<?php echo $category->category_id?>">                        
                    <li class="forum_boxarea_body">     
				
		                <?php foreach($subCats as $subCat) : ?>
		                    <?php echo $this -> partial('_category.tpl', array('category' => $subCat, 'user' => $this -> user, 'lastTopics' => $this -> lastTopics, 'lastPosts' => $this -> lastPosts, 'forums' => $this -> forums));?>
		                <?php endforeach; ?>
		                
		                <?php if ($category->forum_count > 0) { ?>
		                    <ul class="forum_boxarea" id="forum_boxarea_<?php echo $category->category_id?>">                        
		                        <li class="forum_boxarea_body">                        
		                            <?php
									if (array_key_exists($category -> category_id, $this -> forums)) {
										foreach ($this->forums[$category->category_id] as $forum) {
											$memberList = $forum -> getMemberList();
											if ($memberList) {
												$members = $memberList -> getAllChildren();
											}
											$check_user_view = true;
											if (count($members) > 0) {
												if (!$forum -> isMember($this -> viewer) && !$forum -> isModerator($this -> viewer)) {
													$check_user_view = false;
												}
											}
		
											if (($this -> check_permission && $forum -> authorization() -> isAllowed($this -> viewer, 'view') && $check_user_view) || !$this -> check_permission) {
												if (!$forum -> parent_forum_id) {
													echo $this -> partial('_forum.tpl', array('forum' => $forum, 'user' => $this -> user, 'lastTopics' => $this -> lastTopics, 'lastPosts' => $this -> lastPosts));
												}
											}
										}
									}
		                            ?>
		                        </li>			
		                    </ul>
		                <?php } ?>
                
               		</li>
        		</ul> 
            </li>
        </ul>
    <?php } ?>
<?php } ?>

<script language="javascript" type="text/javascript">
	$$('.forum_select_quick_navigation').addEvent('change', function() {
		if (this.value) {
			window.location.href = this.value;
		}
	});
	function toggleMenu(cat_id, img_id) {
		if ($$(cat_id)[0].style.display == 'none') {
			$$(cat_id)[0].style.display = 'block';
			document.getElementById(img_id).innerHTML = '<i class="fa fa-plus-circle fa-2x"></i>';
		} else {
			$$(cat_id)[0].style.display = 'none';
			document.getElementById(img_id).innerHTML = '<i class="fa fa-minus-circle fa-2x"></i>';
		}
	}
</script>