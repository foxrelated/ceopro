<span class="advforum_navigation_item_home">
	<i class="ynicon yn-home"></i>
    <?php echo $this->htmlLink(array('route' => 'ynforum_general'), $this->translate('Forums')) ?>
</span>

<?php foreach (array_reverse($this->linkedCategories) as $linkedCat) : ?>
    <span class="advforum_navigation_item">
    	<i class="ynicon yn-arr-right"></i>
        <?php echo $this->htmlLink($linkedCat->getHref(), $linkedCat->title) ?>
    </span>
<?php endforeach; ?>
<?php if ($this->navigationForums) : ?>
    <?php foreach ($this->navigationForums as $navigationForum) : ?>
        <span class="advforum_navigation_item">
            <i class="ynicon yn-arr-right"></i>
            <?php echo $this->htmlLink($navigationForum->getHref(), $navigationForum->getTitle()) ?>
        </span>    
    <?php endforeach; ?>
<?php endif; ?>