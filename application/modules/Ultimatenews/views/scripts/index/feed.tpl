<script type="text/javascript">
    var pageAction =function(page){
        document.getElementById('nextpage').value = page;
        document.getElementById('gotoPage').submit();
    }
</script>
 
<div class='layout_middle'>
	<?php if($this->category): ?>
		<h3>
			<?php if($this->category->logo != "" && $this->category->mini_logo):?>
                    <img style="float: left; height: 23px;" src="<?php echo $this->category->logo?>" />
            <?php endif;?>
			<span style="margin-left: 5px;"><?php echo $this->category->category_name; ?> &#187;</span>
		</h3>
		<div style="clear: both;"></div>
	<?php endif;?>
    <form name="gotoPage" id="gotoPage" method="post">
        <input type="hidden" name="nextpage" id="nextpage">
        <span style="display:none;">
            <input type="text" name="category" id="category" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['category'];?>" />
            <input type="text" name="search" id="search" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['keyword'];?>" />
        </span>
        <div style="overflow: hidden;">	    
            <?php if ($this->paginator->getTotalItemCount() > 0):?>
                <?php echo $this  -> partial('_list_news.tpl', array('paginator' => $this->paginator)); ?>
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