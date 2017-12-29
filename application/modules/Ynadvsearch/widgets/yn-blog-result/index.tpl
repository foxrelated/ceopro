<script type="text/javascript">
    function ynblogRenderViewMode() {
        var myCookieViewMode = getCookie('ynblog-modeview');
        if ( myCookieViewMode == '') {
            myCookieViewMode = 'ynblog_list-view';
        }
        $$('#ynblog-view-mode-button > span[rel='+myCookieViewMode+']').addClass('active');
        $$('#ynblog-content-mode-views').addClass(myCookieViewMode);
        // Set click viewMode
        $$('#ynblog-view-mode-button > span').addEvent('click', function(){
            var viewmode = this.get('rel');
            var content = $('ynblog-content-mode-views');
            setCookie('ynblog-modeview', viewmode, 1);
            // set class active
            $$('#ynblog-view-mode-button > span').removeClass('active');
            this.addClass('active');
            content
                    .removeClass('ynblog_list-view')
                    .removeClass('ynblog_grid-view');

            content.addClass( viewmode );
        });
    }
</script>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynblog-choose-view-mode">
    <div id="ynblog-view-mode-button" class="ynblog-modeview-button">
        <span class="ynblog-btn-list-view" rel="ynblog_list-view" ><i class="fa fa-th-list"></i></span>
        <span class="ynblog-btn-grid-view" rel="ynblog_grid-view" ><i class="fa fa-th"></i></span>
    </div>
</div>
<ul class="ynblog-mode-views clearfix" id="ynblog-content-mode-views">
    <?php
  	foreach( $this->paginator as $item )
    {
    if ($item->checkPermission())
        {
            echo $this->partial('_listItem.tpl', 'ynblog', array('item' => $item, 'type' => 'new'));
            echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'new'));
        }
    }
    ?>
</ul>
<?php elseif( $this->category || $this->tag ): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There were no blogs found matching your search criteria.'); ?>
		</span>
	</div>

<?php else: ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('There were no blogs found matching your search criteria.'); ?>
		</span>
	</div>
<?php endif; ?>

<?php echo $this->paginationControl($this->paginator, null, null, array(
	'pageAsQuery' => true,
	'query' => $this->formValues,
	)); ?>


