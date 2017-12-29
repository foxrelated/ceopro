<script type="text/javascript">
    var pageAction =function(page){
        document.getElementById('nextpage').value = page;
        document.getElementById('gotoPage').submit();
    }
</script>
<div class='layout_middle'>
    <form name="gotoPage" id="gotoPage" method="post">
        <input type="hidden" name="nextpage" id="nextpage">
        <div style="overflow: hidden; margin-top: -10px;">	    
            <?php if ($this->paginator->getTotalItemCount() > 0):?>
                <?php echo $this  -> partial('_list_news.tpl', array('paginator' => $this->paginator, 'favorite' => 1)); ?>
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