
<div class="ynforum_category">
	<div class="forum_icon">
        <?php echo $this->htmlLink($this->category->getHref(), $this->itemPhoto($this->category, 'thumb.icon')) ?>
    </div>
	<div class="forum_title">
		<h3>
			<?php echo $this->htmlLink($this->category->getHref(), $this->category->title)?>
		</h3>
	</div>
	 <span class="ynforum_cat_description"><?php echo $this->category->description ?></span>
	
</div>

