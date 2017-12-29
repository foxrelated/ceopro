<?php if (count($this->blogs)): ?>
	<ul class="ynblog-mode-views ynblog-related-blogs clearfix">
	  <?php
		foreach( $this->blogs as $item )
		{
			if ($item->checkPermission())
			{
				echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'comment'));
			}
		}
		?>
	</ul>
<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate("No blogs found.") ?>
		</span>
	</div>
<?php endif; ?>