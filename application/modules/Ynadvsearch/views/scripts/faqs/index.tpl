<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynadvsearch-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynadvsearch-collapse"; ?>">
			<div class="ynadvsearch-faq-title">
				<span class="ynadvsearch-faq-icon"></span>
				<div class="ynadvsearch-faq-title-item ynadvsearch_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynadvsearch-faq-title-item ynadvsearch_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynadvsearch-faq-content">
				<?php echo $item->answer?>
			</div>
		</div>
	<?php endforeach; ?>   
<?php else:?>
<div class="tip">
	<span>
		<?php echo $this->translate("No FAQs has been added.") ?>
	</span>
</div>
<?php endif; ?>

<!-- Page Paginator -->
<div>
   <?php  echo $this->paginationControl($this->paginator, null, null, array());?>
</div>
<script type="text/javascript">
	$$('.ynadvsearch-faq-title').addEvent('click', function(){
		this.getParent('div.ynadvsearch-faq-item').toggleClass('ynadvsearch-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynadvsearch').getParent().addClass('active');
</script>