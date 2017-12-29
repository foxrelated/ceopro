
<h2><?php echo $this->translate('Advanced Search') ?></h2>


<form id = "search_form" class="search_form " method="post" action="<?php echo $this->url()?>">
	<div id="searchform" class="global_form_box">
	<input id="query" type="text" value="<?php echo $this->query;?>" name="query">
	</div>
	<div class = "result_per_page">
	<?php echo $this->translate('Results per page')?> <input id = "qty" type = "text" value="<?php echo $this->qty?>" name = "qty" size = "4">
	</div>
	<div class = "mod_boxes">
	<?php foreach($this->modules as $module) :?>
	<div class = "mod_check">
		<input type="hidden" name="moduleynsearch[<?php echo $module['name']?>]" value="0">
		<input type="checkbox" id = "moduleynsearch[<?php echo $module['name']?>]" name="moduleynsearch[<?php echo $module['name']?>]" onclick='uncheckAll();' value="1" <?php echo (@$module['checked'])? 'checked' : '';?> ><?php echo $module['title'];?>
	</div>
	<?php endforeach;?>
	<div class = "mod_check">
	<input type="hidden" name="checkall" value="0" >
	<input id = 'checkall' type='checkbox' name='checkall' onclick='checkedAll();' value = "1" <?php echo (@$this->checkAll)? 'checked' : '';?>><?php echo $this->translate('Select All');?>
	</div>
	</div>
	<div class = "button_submit">
	<button id="submit" type="submit" name="submit"><?php echo $this->translate('Search');?></button>
	</div>
</form>

<br />

<?php if( empty($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please enter a search query.') ?>
    </span>
  </div>
<?php elseif( $this->paginator->getTotalItemCount() <= 0 ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results were found.') ?>
    </span>
  </div>
<?php else: ?>
 <div class = "count_result"> 
 <?php echo $this->translate(array('%s result found', '%s results found', $this->paginator->getTotalItemCount()),
                              $this->locale()->toNumber($this->paginator->getTotalItemCount()) ) ?>
	</div>
	
  <?php 
  	$type = '';
  	foreach( $this->paginator as $item ):
    if ($type != $item->type) :
    	$type = $item->type; ?>	
    	<div class="search_type">
    		<?php echo $this->translate(strtoupper('ITEM_TYPE_' . $type));?>
    	</div>
   <?php endif; 
  	$item = $this->item($item->type, $item->id);
    if( !$item ) continue; 
    
    ?> 
    <div class="search_result">
      <div class="search_photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      </div>
      <div class="search_info">
        <?php if( '' != $this->query ): ?>
          <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
        <?php else: ?>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
        <?php endif; ?>
        <p class="search_description">
          <?php if( $type != "news_content" ): ?>
	          <?php if( '' != $this->query ): ?>
	            <?php echo $this->highlightText($this->viewMore($item->getDescription()), $this->query); ?>
	          <?php else: ?>
	            <?php echo $this->viewMore($item->getDescription()); ?>
	          <?php endif; ?>
          <?php endif; ?>
        </p>
      </div>
    </div>
  <?php /*
    <div class="search_result">
      <div class="search_icon">
        &nbsp;
      </div>
      <div class="search_info">
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
        <p class="search_description">
          <?php echo $item->getDescription(); ?>
        </p>
      </div>
    </div>
   *
   */?>
  <?php endforeach; ?>

  <br />

  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      //'params' => array(
      //  'query' => $this->query,
      //),
      'pageAsQuery' => true,
    'query' => array(
        'query' => $this->query,
        'type' => $this->types,
    	'module_result' => $this->modules,
    	'qty' => $this->qty,
    	'checkall' => $this->checkAll
      ),
      
    )); ?>
  </div>

<?php endif; ?>

<style type="text/css">

</style>
<script type="text/javascript">
function checkedAll() {
	var checkboxes = document.getElementById('search_form');
	var box = document.getElementById('checkall');
	var boxchecked = box.checked;
	for (var i =0; i < checkboxes.elements.length; i++) 
	{
		checkboxes.elements[i].checked = boxchecked;
	}
}
window.addEvent('domready',function(){
	var selectAll = '<?php echo @$this->checkAll;?>';
	if (selectAll) {
		var checkboxes = document.getElementById('search_form');
		for (var i =0; i < checkboxes.elements.length; i++) 
		{
			checkboxes.elements[i].checked = true;
		}
	}
});
function uncheckAll() {
	var box = document.getElementById('checkall');
	if (box.checked == true) {
		box.checked = false;
	}
}
</script>
