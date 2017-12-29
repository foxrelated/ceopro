<?php
$moderatorList = $this->forum->getModeratorList();
if ($moderatorList) {
    $moderators = $moderatorList->getAllChildren();
}
$memberList = $this->forum->getMemberList();
if ($memberList) {
    $members = $memberList->getAllChildren();
}
?>
<div class="admin_forums_options">
    <?php if (isset($this->index) && $this->index > 0) : ?>
        <span class="admin_forums_moveup">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('move up'), array('onclick' => 'moveForum(' . $this->forum->getIdentity() . ', this);')); ?> |
        </span>
    <?php endif; ?>

    <a href="<?php
        echo $this->url(array(
            'action' => 'edit-forum',
            'forum_id' => $this->forum->getIdentity()));
        ?>" 
       class="smoothbox">
       <?php echo $this->translate("edit") ?>
    </a>
    | 
    <a class="smoothbox" href="<?php
           echo $this->url(array(
               'action' => 'delete-forum',
               'forum_id' => $this->forum->getIdentity()));
           ?>">
       <?php echo $this->translate("delete") ?>
    </a>

    <?php if ($moderators && count($moderators) > 0) : ?>                                
        | 
        <a class="smoothbox" href="<?php
            echo $this->url(array(
                'action' => 'share-permission',
                'forum_id' => $this->forum->getIdentity()));
        ?>">
       <?php echo $this->translate('moderator permission') ?>
        </a>
    <?php endif; ?>
    
    | 
    <a class="smoothbox" href="<?php
            echo $this->url(array(
                'action' => 'member-permission',
                'forum_id' => $this->forum->getIdentity()));
        ?>">
       <?php echo $this->translate("member level permission") ?>
    </a>
        
    | <?php echo $this->htmlLink($this->forum->getHref(), $this->translate('view'))?>    
</div>
<div class="admin_forums_info">
    <span class="admin_forums_title">
        <a href="<?php 
            echo $this->url(array(
                    'forum_id' => $this->forum->getIdentity(),
                    'controller' => 'manage', 
                    'module' => 'ynforum'
                    ), 'admin_default', true) ;
                ?>">
            <?php echo $this->forum->getTitle() ?>
        </a>
    </span>
    <span class="admin_forums_moderators">
        <span class="admin_forums_moderators_top">
            <?php echo $this->translate("Moderators") ?>
            (<a href="<?php
            echo $this->url(array(
                'action' => 'add-moderator',
                'format' => 'smoothbox',
                'forum_id' => $this->forum->getIdentity()));
            ?>" class="smoothbox"><?php echo $this->translate("add") ?></a>)
        </span>
        <span>
            <?php
            foreach ($moderators as $i => $moderator) 
            {
            	if($moderator->getIdentity())
				{
	                if ($i > 0) 
	                {
	                    echo ', ';
	                }
	                echo $this->htmlLink($moderator->getHref(), $moderator->getTitle())
	                    . ' (<a class="smoothbox" href="' . $this->url(array(
	                        'action' => 'remove-moderator',
	                        'forum_id' => $this->forum->getIdentity(),
	                        'user_id' => $moderator->getIdentity())) . '">' . $this->translate("remove") . '</a>)';
				}
            }
            ?>
        </span>
    </span>
    
    
    <span class="admin_forums_moderators">
        <span class="admin_forums_moderators_top">
            <?php echo $this->translate("Allow View Members") ?>
            (<a href="<?php
            echo $this->url(array(
                'action' => 'add-member',
                'format' => 'smoothbox',
                'forum_id' => $this->forum->getIdentity()));
            ?>" class="smoothbox"><?php echo $this->translate("add") ?></a>)
        </span>
        <span>
            <?php
            foreach ($members as $i => $member) 
            {
            	if($member->getIdentity())
				{
	                if ($i > 0) 
	                {
	                    echo ', ';
	                }
	                echo $this->htmlLink($member->getHref(), $member->getTitle())
	                    . ' (<a class="smoothbox" href="' . $this->url(array(
	                        'action' => 'remove-member',
	                        'forum_id' => $this->forum->getIdentity(),
	                        'user_id' => $member->getIdentity())) . '">' . $this->translate("remove") . '</a>)';
	            }
			}
            ?>
        </span>
    </span>
    
</div>