<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('ynblog_filter_form').submit();
  }
</script>
<h2>
 <?php echo $this->owner;?>
 <?php echo $this->translate("'s Entries")?>
</h2>
<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div class="ynblog-choose-view-mode ynblog-listing-count">
  <div id="ynblog-total-item-count"><span><?php echo $this->paginator->getTotalItemCount();?></span><?php echo $this->translate(array(" blog", " blogs",$this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount()) ?></div>
  <div id="ynblog-view-mode-button-<?php echo $this -> identity;?>" class="ynblog-modeview-button">
    <span class="ynblog-btn-list-view" rel="ynblog_list-view" ><i class="fa fa-th-list"></i></span>
    <span class="ynblog-btn-grid-view" rel="ynblog_grid-view" ><i class="fa fa-th"></i></span>
  </div>
</div>

<ul class="ynblog-mode-views clearfix" id="ynblog-content-mode-views-<?php echo $this->identity?>">	
  <?php foreach ($this->paginator as $item)
  		{
        	if($item->authorization()->isAllowed(null,'view'))
			{
				echo $this->partial('_listItem.tpl', 'ynblog', array('item' => $item, 'type' => 'comment'));
				echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'comment'));
			}
		}
   ?>
</ul>

<?php elseif( $this->category || $this->tag ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
    </span>
  </div>
<?php endif; ?>

 <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>
<br/>


<script type="text/javascript">
  window.addEvent('domready', function(){
    renderViewMode<?php echo $this->identity?>();
  });

  function renderViewMode<?php echo $this->identity?>() {
    var myCookieViewMode = getCookie('ynblog-modeview-<?php echo $this -> identity; ?>');
      if ( myCookieViewMode == '') {
          myCookieViewMode = 'ynblog_list-view';
      }
      
      $$('#ynblog-view-mode-button-<?php echo $this -> identity;?> > span[rel='+myCookieViewMode+']').addClass('active');
      $$('#ynblog-content-mode-views-<?php echo $this->identity ?>').addClass(myCookieViewMode);
      
      // Set click viewMode
      $$('#ynblog-view-mode-button-<?php echo $this -> identity;?> > span').addEvent('click', function(){
          var viewmode = this.get('rel');
          var content = $('ynblog-content-mode-views-<?php echo $this->identity?>');

          setCookie('ynblog-modeview-<?php echo $this -> identity; ?>', viewmode, 1);

          // set class active
          $$('#ynblog-view-mode-button-<?php echo $this -> identity;?> > span').removeClass('active');
          this.addClass('active');

          content
              .removeClass('ynblog_list-view')
              .removeClass('ynblog_grid-view');

          content.addClass( viewmode );
      });
  }
</script>