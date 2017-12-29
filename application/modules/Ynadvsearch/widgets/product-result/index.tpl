<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <?php 	$key = Engine_Api::_()->socialstore()->getWidgetName(0);
	$viewtype = widgetGetCookie($key);
	?>
<ul id ="ynul-<?php echo $key?>" class="main_product_list <?php echo $viewtype?>">

	<?php foreach($this->paginator as $item): ?>
		<?php if(Engine_Api::_()->user()->getUser($item->owner_id)->getIdentity()!=0):?>
	<li>		
		<div class="recent_product_img">
			<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
		</div>
		
		<div id ="yndivlist-<?php echo $key?>" class="pricecart_wrapper yndivlist">
			<?php echo $this->cart($item)?>
		</div>
		<div class="product_info">
			<div class="product_title"> <?php echo $item ?></div>
			<div class="store_browse_info_date">
			<?php echo $this->translate('Posted by %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>
			<?php echo $this->translate("Store: "); ?> <?php echo $this->htmlLink($item->getStore(), $item->getStore()->getTitle())?>
			</div>
			<div class="product_description"> <?php echo $item->getDescription(); ?> </div>
			<?php 
				$string = '';
				$defaultType = $item->getOptions();
				$i = 0;
				if (count($defaultType) > 0) {
					$v = 0; 
					$length = count($defaultType);	
					foreach ($defaultType as $key => $type) {
						$v++;
						$default_option = Engine_Api::_()->getApi('attribute','socialstore')->getDefaultOption($item->product_id, $key);
						if ($default_option != null) {
							$type_label = Engine_Api::_()->getApi('attribute','socialstore')->getTypeLabel($key);
							$string .= $type_label.': '. $default_option->label;
							if ($default_option->adjust_price != 0) { 
								$string.= " (".$this->currency($default_option->adjust_price).")";
							}
							if ($v < $length) {
								$string.=' - ';
							}
						}
					}
				}
				if ($string != '') :
			?>
			<!--  <span class= "ynstore_widget_pro_opt">
				<?php echo $this->translate('Options')?>
			</span>:-->
			<span class="ynstore_widget_pro_attr">
				<?php echo $string;?>
			</span>
			<?php endif;?>
			<div class="product_favourite">
				<?php echo $this->favourite($item) ?>
			</div>
		</div>
		<div id ="yndivgrid-<?php echo $key?>" class="pricecart_wrapper yndivgrid" style = "display:none;">
			<?php echo $this->cart($item)?>
		</div>	
		<div style="clear:both"></div>
	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>

<br/>
<?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no product meets your criteria.');?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    //'query' => '',
    'params' => $this->values,
  )); ?>
</div>
<script type="text/javascript"> 
window.addEvent('domready',function(){
	className = '<?php echo $this->className?>';
	key = '<?php echo Engine_Api::_()->socialstore()->getWidgetName(0)?>';
	viewtype = '<?php echo $viewtype?>';
	en4.socialstore.addClass(className, key, viewtype);
});
</script>