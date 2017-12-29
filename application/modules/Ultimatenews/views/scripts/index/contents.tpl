<?php
if ($this->categoryparent)
{
	$this->headTitle($this->categoryparent->category_name, Zend_View_Helper_Placeholder_Container_Abstract::SET);
	$this->headMeta()->setName('description', trim($this->categoryparent->category_description));
	$this->headMeta()->setName('keywords', $this->categoryparent->category_name);
}
?>
<script type="text/javascript">
    var pageAction =function(page){
        document.getElementById('nextpage').value = page;
        document.getElementById('gotoPage').submit();
    }
</script>
<?php echo $this->content()->renderWidget('ultimatenews.menu-ultimatenews') ?>
<div class="generic_layout_container layout_main">
	<div class="layout_left">
	<div class='generic_layout_container'>
		<h3 style="margin-bottom: 6px;"><?php echo $this->translate("Categories"); ?></h3>
		<ul class="global_form_box" style="background-color: #FFF; padding: 5px;">
		<?php 
		      foreach($this->categories as $cat): ?>
	            <li style="font-weight:bolder; padding: 5px; <?php if($cat->category_id == $this->categoryparent->category_id): echo "background-color: #E9F4FA;"; endif;?>" title="<?php //echo $cat->category_name; ?>">
		            <a  href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent')?>">
		            <?php echo strlen($this->translate($cat->category_name))>22?substr($this->translate($cat->category_name),0,19).'...':$this->translate($cat->category_name); ?>
		            </a>
	            </li>
		      <?php endforeach;?>
		      <li style="font-weight:bolder; padding: 5px; <?php if('0' == $this->category_id): echo "background-color: #E9F4FA;"; endif;?>" title="<?php echo $this->translate("Other") ?>">
		            <a  href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=> 0),'ultimatenews_categoryparent')?>">
		            <?php echo $this->translate('Other');?>
		            </a>
	          </li>
		</ul>
	</div>
	</div>
	<div class='layout_middle'>
	<div class='generic_layout_container'>
			<h3 style="margin-bottom: 6px;">
			<?php if($this->categoryparent)
					echo $this->categoryparent->category_name;
				else
					echo $this->translate("Other") ?> &#187;</h3>
			<?php if($this->categoryparent): 
				if($this->categoryparent->category_description):?>
				<div style="margin-bottom: 10px; border-bottom: 2px solid #EAEAEA;">
				<div style="margin-bottom: 10px;"><?php echo $this->categoryparent->category_description;?></div>
				</div>
			<?php endif; endif;?>
	    <form name="gotoPage" id="gotoPage" method="post">
	        <input type="hidden" name="nextpage" id="nextpage">
	        <span style="display:none;">
	            <input type="text" name="category" id="category" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['category'];?>" />
	            <input type="text" name="search" id="search" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['keyword'];?>" />
	        </span>
	        <div style="overflow: hidden; margin-top: -10px;">	    
	            <?php if ($this->paginator->getTotalItemCount() > 0):?>
					<?php echo $this  -> partial('_list_news.tpl', array('paginator' => $this->paginator)); ?>
	            <?php else:?>
	                <div class="tip" style="margin-top: 10px;">
	                    <span>
	                        <?php echo $this->translate('There are no news items.') ?>   
	                    </span>
	                </div>
	            <?php endif;?>
	            <br /><br />
	            <?php echo $this->paginationControl($this->paginator, null, array("pagination/ultimatenewspagination.tpl", "ultimatenews"));?>
	        </div>
	    </form>
	</div>
	</div>
</div>