<div class='count_results'>
		<span class="search_icon fa fa-search"></span>
		<span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
		<span class="total_results">(<?php echo $this->total_content?>)</span>
		<span class="label_results"><?php echo $this->htmlLink(array('route' => 'blog_general'), $this->label_content, array());?></span>
</div>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="ynblogs_entrylist">
	<?php foreach( $this->paginator as $item ): ?>
	<?php
       $photoUrl = $item ->getPhotoUrl();
		if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynadvsearch/externals/images/nophoto_blog_thumb_profile.png';
	?>
	<li>
		<div class="wrap_col3">
			<div class="wrap_col_left">
				<div class="ynblog_entrylist_entry_date">
					<?php
              $creation_date = new Zend_Date(strtotime($item->creation_date));
					$creation_date->setTimezone($this->timezone);
					?>
					<div class="day">
						<?php echo $creation_date->get(Zend_Date::DAY)?>
					</div>
					<div class="month">
						<?php echo $creation_date->get(Zend_Date::MONTH_NAME_SHORT)?>
					</div>
					<div class="year">
						<?php echo $creation_date->get(Zend_Date::YEAR)?>
					</div>
				</div>
			</div>
			<div class="blog-image">
				<img src="<?php echo $photoUrl ?>" alt="">
			</div>
			<div class="wrap_col_center">
				<div class="yn_title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></div>
				<div class="post_by"><?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></div>
				<div class="ynblog_entrylist_entry_body"><?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?></div>
			</div>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
<?php elseif( $this->category || $this->show == 2 || $this->search ): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There were no blogs found matching your search criteria.');?>
		</span>
	</div>

<?php else:?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There were no blogs found matching your search criteria.'); ?>
		</span>
	</div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
	'pageAsQuery' => true,
	'query' => $this->formValues,
)); ?>