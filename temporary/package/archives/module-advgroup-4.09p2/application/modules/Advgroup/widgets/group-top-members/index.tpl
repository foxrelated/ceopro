<?php
/**
 * @category   Adv.Group Widget
 * @package    Adv.Group
 * @copyright  Copyright 2013-2014 YouNet Developments
 * @author     trunglt
 */
?>
<?php $count = 0;?>
<ul id="groups_topmembers">
	<?php foreach( $this->results as $result ): ?>
		<?php if ($count == $this->limit) break; ?>
		<?php $user = Engine_Api::_()->user()->getUser($result['subject_id'])?>
		<?php if ($this->group -> membership() -> isMember($user)) : ?>
		<?php $count++; ?>
		<li>
			<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('class' => 'topmembers_thumb')) ?>
			<div class='groups_topmembers_info'>
			<div class='groups_topmembers_name'>
			  <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
			</div>
			<div class='groups_topmembers_count'>
			  <?php echo $this->translate(array('%s action', '%s actions', $result['post_count']),$this->locale()->toNumber($result['post_count'])) ?>
			</div>
			</div>
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>