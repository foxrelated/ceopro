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
                    <?php echo $this->translate(array('%1$s view', '%1$s views', $topic->view_count), $this->locale()->toNumber($topic->view_count))?>
                </a>    
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