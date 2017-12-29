<h2>
    <?php echo $this->htmlLink(array('route' => 'ynforum_general'), $this->translate("Forums")); ?>
        <?php foreach($this->navigationForums as $navigationForum) : ?>
            &#187; <?php echo $this->htmlLink($navigationForum->getHref(), $navigationForum->getTitle())?>
        <?php endforeach; ?>
        &#187; <?php echo $this->htmlLink($this->forum->getHref(), $this->forum->getTitle()) ?>
</h2>