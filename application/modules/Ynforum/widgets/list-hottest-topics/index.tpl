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
                <a href="javascript:void(0);">
                    <?php echo $topic->approved_post_count?> <?php echo $this->translate(array('post', 'posts', $this->locale()->toNumber($topic->approved_post_count))) ?>
                </a>
            </div>
            <?php echo $this->htmlLink($topic->getHref(), $topic->title); ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php else:?>
<div class="tip">
		<span><?php echo $this->translate("There are no topic yet.")?></span>
</div>
<?php endif;?>