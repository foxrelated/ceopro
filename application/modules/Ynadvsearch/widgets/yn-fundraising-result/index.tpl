<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynfundraising_filter_form').submit();
  }
</script>

<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route' => 'ynfundraising_general'), $this->label_content, array());?></span>
</div>

<?php
 if( $this->paginator->getTotalItemCount() > 0 ): ?>
<ul class="ynfundraising_list_items_campaigns ynadvsearch-clearfix" style="margin-bottom: 15px;">
	<?php foreach($this->paginator as $item): ?>
		<li>
			<?php echo $this->partial('_campaign_item.tpl', 'ynfundraising', array('campaign' => $item));?>
		</li>
	<?php endforeach;?>
</ul>
  <?php elseif($this->formValues['search']): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There were no campaigns found matching your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There were no campaigns found matching your search criteria.');?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  ));
  ?>
  <?php //echo $this->paginationControl($this->paginator,null, array("pagination/pagination.tpl","ynfundraising")); ?>
