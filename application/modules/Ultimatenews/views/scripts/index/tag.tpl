<script type="text/javascript">
    var pageAction =function(page){
        document.getElementById('nextpage').value = page;
        document.getElementById('gotoPage').submit();
    }
</script>
<?php echo $this->content()->renderWidget('ultimatenews.menu-ultimatenews') ?>
 <div class='layout_right'>   
 	<h3 style = "padding-bottom: 5px;"><?php echo $this -> translate("Tags")?></h3>
 	<?php echo $this->content()->renderWidget('ultimatenews.tag-news') ?> 
 </div>
<div class='layout_middle'>
	<?php if($this->tag_name): ?>
		<h3>
			<span style="margin-left: 5px;"><?php echo $this -> translate("News in '%s'",$this->tag_name); ?></span>
		</h3>
		<div style="clear: both;"></div>
	<?php endif;?>
    <form name="gotoPage" id="gotoPage" method="post">
        <input type="hidden" name="nextpage" id="nextpage">
        <div style="overflow: hidden;">	    
            <?php if ($this->paginator->getTotalItemCount() > 0):?>
            	<?php echo $this  -> partial('_list_news.tpl', array('paginator' => $this->paginator, 'tag' => 1)); ?>
            <?php else:?>
                <div class="tip" style="margin-top: 10px;">
                    <span>
                         <?php echo $this->translate('No news with that criteria') ?>   
                    </span>
                </div>
            <?php endif;?>
            <br /><br />
            <?php echo $this->paginationControl($this->paginator, null, array("pagination/ultimatenewspagination.tpl", "ultimatenews"));?>
        </div>
    </form>
</div>