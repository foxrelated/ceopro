<div class="forum_quick_navigation">
    <span><?php echo $this->translate('Quick navigation') ?></span>
    <select class="forum_select_quick_navigation">
        <option><?php echo $this->translate('Please select an item')?></option>
        <?php foreach ($this->categories as $category) : ?>
            <option value="<?php echo $category->getHref() ?>">
                <?php echo str_repeat('&nbsp;&nbsp;', $category->level) . $category->title ?>
            </option> 
                <?php 
                    if (array_key_exists($category->getIdentity(), $this->forums)) {
                ?>
                    <?php foreach ($this->forums[$category->getIdentity()] as $forum): 
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
                    	if(($this->check_permission && $forum->authorization()->isAllowed($this->viewer,'view') && $check_user_view) || !$this->check_permission):
						?>
	                        <option value="<?php echo $forum->getHref() ?>">
	                            <?php echo str_repeat('&nbsp;&nbsp;', $category->level + $forum->level + 1) . $this->translate('Forum') . '::' . $forum->title ?>
	                        </option>
                    <?php endif; endforeach; ?>
                <?php
                    }
                ?>
        <?php endforeach; ?>
    </select>
</div>