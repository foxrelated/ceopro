<?php if (!$this->identity) $this->identity = 0; ?>
<?php echo $this->partial('_viewModeChooser.tpl', 'ynblog', array(
    'identity' => $this->identity,
    'mode_enabled' => $this->mode_enabled
)); ?>
<ul class="ynblog-mode-views clearfix" id="ynblog-content-mode-views-<?php echo $this->identity?>">
  <?php foreach ($this->paginator as $item)
  		if ($item->checkPermission())
		{
			echo $this->partial('_listItem.tpl', 'ynblog', array('item' => $item, 'type' => 'new'));
			echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'new'));
		}
   ?>
</ul>

<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'blog_view'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
<?php endif;?>


<script type="text/javascript">
  window.addEvent('domready', function(){
      ynblogRenderViewMode(<?php echo $this->identity?>, '<?php echo $this->view_mode ?>', <?php echo json_encode($this->mode_enabled) ?>);
  });
</script>