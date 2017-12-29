<div class="global_form_popup">
<?php if(isset($this->aDuplicateBlogs) && count($this->aDuplicateBlogs)): ?>
	<h3><?php echo $this->translate(array('%s blog was not imported because of its duplication', '%s blogs were not imported because of their duplication', count($this->aDuplicateBlogs)), $this->locale()->toNumber(count($this->aDuplicateBlogs)))  ?>:</h3>
	<ul>
		<?php foreach($this->aDuplicateBlogs as $dblog): ?>
			<li>- <?php echo $dblog['title']; ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
</div>