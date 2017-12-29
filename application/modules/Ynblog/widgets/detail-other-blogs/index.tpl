<ul class="ynblog-mode-views ynblog-other-blogs">
  <?php 
  	foreach( $this->blogs as $item )
    {
  		if ($item->checkPermission())
		{
			echo $this->partial('_listItem.tpl', 'ynblog', array('item' => $item, 'type' => 'new', 'noClearFix' => true));
		}
	}
  	?>
</ul>
