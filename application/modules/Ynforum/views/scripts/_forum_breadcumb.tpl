<?php $forum = Engine_Api::_() -> core() -> getSubject();
$navigationForums = $forum -> getForumNavigations();?>
<div id="title-wrapper" class="form-wrapper">
	<div id="title-label" class="form-label">
		<label for="title" class="required">
			<?php echo $this -> translate("Forum")?>
		</label>
	</div>
	<div id="title-element" class="form-element">
		<?php if ($navigationForums) : ?>
		    <?php foreach ($navigationForums as $navigationForum) : ?>
		        <span class="advforum_navigation_item">
		            <?php echo $this->htmlLink($navigationForum->getHref(), $navigationForum->getTitle()) ?>
		        </span>    
		    <?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>