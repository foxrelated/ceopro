<?php
/**
 * SocialEngine
 *
 * @category   Application_Extension
 * @package    Ynforum
 * @author     DangTH
 */
?>
<?php if(count($this->topics)>0):?>
<ul class="advforum_statistic_daily">
    <?php foreach ($this->topics as $topic): ?>
        <li>
            <div>
                <?php 
                    $owner = $topic->getOwner();
                    echo $this->htmlLink($owner->getHref(), $this->string() -> truncate($owner->getTitle(), 20), array('title' => $owner->getTitle()));
                ?>
            </div>
            <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()); ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else:?>
<div class="tip">
		<span><?php echo $this->translate("There are no topic yet.")?></span>
</div>
<?php endif;?>