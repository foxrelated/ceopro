<?php if( count($this->paginator) ): ?> 
	<?php foreach($this->paginator as $item) :?>
		<div class="ynmultilisting-faq-item <?php if($this->paginator -> getTotalItemCount() > 5) echo "ynmultilisting-collapse"; ?>">
			<div class="ynmultilisting-faq-title">
				<span class="ynmultilisting-faq-icon"></span>
				<div class="ynmultilisting-faq-title-item ynmultilisting_question_preview"><?php echo $this->string()->truncate($item->title, 200);?></div>
				<div class="ynmultilisting-faq-title-item ynmultilisting_question_full"><?php echo $item->title?></div>
			</div>
			<div class="ynmultilisting-faq-content rich_content_body">
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
	$$('.ynmultilisting-faq-title').addEvent('click', function(){
		this.getParent('div.ynmultilisting-faq-item').toggleClass('ynmultilisting-collapse'); 
	});
</script>

<script type="text/javascript">
	$$('.core_main_ynmultilisting').getParent().addClass('active');
</script>