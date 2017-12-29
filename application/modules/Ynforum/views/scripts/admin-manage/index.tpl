<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     DangTH
 */
?>
<h2><?php echo $this->translate("Forums Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class="advforum_navigation_bar">
    <span class="advforum_navigation_item_home">
        <i class="ynicon yn-home"></i>
        <?php echo $this->htmlLink($this->url(array('module' => 'ynforum', 'controller' => 'manage'), null, true), $this->translate('Admin Forums')) ?>
    </span>

    <?php if (isset($this->linkedCategories)) : ?>
        <?php foreach (array_reverse($this->linkedCategories) as $linkedCat) : ?>
            <span class="advforum_navigation_item">
                <?php echo $this->htmlLink($this->url(
                        array('module' => 'ynforum', 'controller' => 'manage', 'cat_id' => $linkedCat->getIdentity()), 
                        null, true), 
                        $linkedCat->title) ?>
            </span>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if ($this->navigationForums) : ?>
        <?php foreach ($this->navigationForums as $navigationForum) : ?>
            <span class="advforum_navigation_item">
                <?php echo $this->htmlLink($this->url(
                        array('module' => 'ynforum', 'controller' => 'manage', 'forum_id' => $navigationForum->getIdentity())), 
                        $navigationForum->getTitle()) ?>
            </span>    
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    var moveCategoryUp = function(category_id) {
        var url = '<?php echo $this->url(array('action' => 'move-category-up')) ?>';
        var request = new Request.JSON({
            url : url,
            data : {
                format : 'json',
                category_id : category_id
            },
            onComplete : function() {
                window.location.replace( window.location.href );
            }
        });
        request.send();
    }
    
    var moveCategory = function(category_id, ele) {
        // get the li block with the same level
        var parentEle = ele.getParent('li');
        var preEles = parentEle.getAllPrevious();
        var preEle = null;
        for (var i = 0; i < preEles.length; i++) {
            if (preEles[i].get('class') == parentEle.get('class')) {
                preEle = preEles[i];
                break;
            }
        }
        
        if (preEle) {
            var pre_category_id = preEle.get('id').substring(11);
            var url = '<?php echo $this->url(array('action' => 'move-category')) ?>';
            var request = new Request.JSON({
                url : url,
                data : {
                    format : 'json',
                    id : category_id,
                    pre_category_id : pre_category_id
                },
                onComplete : function() {
                    window.location.replace(window.location.href);
                }
            });
            request.send();
        }
    }
    
    var moveForumUp = function(forum_id) {
        var url = '<?php echo $this->url(array('action' => 'move-forum-up')) ?>';
        var request = new Request.JSON({
            url : url,
            data : {
                format : 'json',
                forum_id : forum_id
            },
            onComplete : function() {
                window.location.replace( window.location.href );
            }
        });
        request.send();
    }
    
    var moveForum = function(forum_id, ele) {
        var preEle = ele.getParent('li').getPrevious();
        if (preEle) {
            var pre_forum_id = preEle.get('id').substring(8);
            var url = '<?php echo $this->url(array('action' => 'move-forum')) ?>';
            var request = new Request.JSON({
                url : url,
                data : {
                    format : 'json',
                    id : forum_id,
                    pre_forum_id : pre_forum_id
                },
                onComplete : function() {
                    window.location.replace(window.location.href);
                }
            });
            request.send();
        }
    }
</script>

<div class="admin_forums_options">
    <a href="<?php echo $this->url(array('action' => 'add-category')); ?>" class="buttonlink smoothbox admin_forums_createcategory">
        <i class="ynicon yn-plus"></i>
        <?php echo $this->translate("Add Category") ?>
    </a>
    <a href="<?php echo $this->url(array('action' => 'add-forum')); ?>" class="buttonlink smoothbox admin_forums_create">
        <i class="ynicon yn-plus"></i>
        <?php echo $this->translate("Add Forum") ?>
    </a>
</div>

<br />

<?php if ($this->categories) : ?>
    <ul class="admin_forum_categories">
        <?php 
            $firstLevel = $this->firstCatgoryLevel;
            $curLevel = $firstLevel;
        ?>
        <?php foreach ($this->categories as $category): ?>
            <li id="yncategory-<?php echo $category->getIdentity()?>" class="cat-level-<?php echo $category->level?>">
                <div class="admin_forum_categories_info <?php echo ($category->level > $firstLevel)?'sub_category':'same_level_category'?>">
                    <div class="admin_forum_categories_options">
                        <?php if ($category->level <= $curLevel) :?>
                            <span class="admin_forums_moveup">      
                                <?php 
                                    echo $this->htmlLink('javascript:void(0);', $this->translate('move up'), 
                                            array('onclick' => 'moveCategory(' . $category->category_id . ', this);')); ?> |
                            </span>
                        <?php endif; ?> 
                        <a href="<?php echo $this->url(array('action' => 'edit-category', 'category_id' => $category->getIdentity())); ?>" class="smoothbox">
                            <?php echo $this->translate("edit") ?>
                        </a>
                        | 
                        <a class="smoothbox" href="<?php echo $this->url(array('action' => 'delete-category', 'category_id' => $category->getIdentity())); ?>">
                            <?php echo $this->translate("delete") ?>
                        </a>
                        | <?php echo $this->htmlLink($category->getHref(), $this->translate('view'));?>
                    </div>
                    <div class="admin_forum_categories_title">
                        <a href="<?php echo $this->url(array('cat_id' => $category->getIdentity()))?>">
                            <?php echo $category->title; ?>
                        </a>                        
                    </div>

                    <span class="admin_forum_categories_moderators">
                        <span class="admin_forum_categories_moderators_top">
                            <?php echo $this->translate("Moderators") ?>
                            (<a href="<?php echo $this->url(array('action' => 'add-moderator', 'format' => 'smoothbox', 'category_id' => $category->getIdentity())); ?>" class="smoothbox"><?php echo $this->translate("add") ?></a>)
                        </span>
                        <span>
                            <?php
                            $i = 0;
                            foreach ($category->getModeratorList()->getAllChildren() as $moderator) {
                                if ($i > 0) {
                                    echo ', ';
                                }
                                $i++;
                                echo $this->htmlLink($moderator->getHref(), $moderator->getTitle()) . ' (<a class="smoothbox" href="' . $this->url(array('action' => 'remove-moderator', 'category_id' => $category->getIdentity(), 'user_id' => $moderator->getIdentity())) . '">' . $this->translate("remove") . '</a>)';
                            }
                            ?>
                        </span>
                    </span>
                    
                </div>
                <?php if ($category->level == $firstLevel) : ?>
                    <ul class="admin_forums">
                        <?php foreach ($category->getChildren('ynforum_forum', array('order' => 'order')) as $index => $forum): ?>
                            <?php if (!$forum->parent_forum_id) : ?>
                                <li id="ynforum-<?php echo $forum->getIdentity()?>">
                                    <?php 
                                        echo $this->partial('_admin_forum.tpl', array(
                                            'forum' => $forum,
                                            'index' => $index));    
                                    ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
            <?php $curLevel = $category->level?>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($this->forum_id && array_key_exists($this->forum_id, $this->forums)) : ?>
    <ul class="admin_forums">
        <li>
            <?php echo $this->partial('_admin_forum.tpl', array('forum' => $this->forums[$this->forum_id]))?>
        </li>
        <?php if (array_key_exists($this->forum_id, $this->forums)) : ?>
            <?php foreach ($this->forums[$this->forum_id]->getSubForums() as $index => $subForum) : ?>
            <li class="sub_forum" id="ynforum-<?php echo $subForum->getIdentity()?>">
                <?php 
                    echo $this->partial('_admin_forum.tpl', array(
                        'forum' => $this->forums[$subForum->forum_id],
                        'index' => $index,
                    ));
                ?>
            </li>    
            <?php endforeach;?>
        <?php endif;?>
    </ul>
<?php endif; ?>