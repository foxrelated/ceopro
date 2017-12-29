<script type="text/javascript">
    function showUploader()
    {
        $('photo').style.display = 'block';
        $('photo-label').style.display = 'none';
    }
</script>
<div class="forum_breadcumb">
	<div>
    <?php
        echo $this->partial('_navigation.tpl', array(
            'linkedCategories' => $this->linkedCategories,
            'navigationForums' => $this->navigationForums,
        ));
    ?>
    <span class="advforum_navigation_item">
            <?php echo $this->htmlLink(array('route' => 'ynforum_topic', 'topic_id' => $this->topic->getIdentity()), $this->topic->title); ?>
        </span>
    	<span class="advforum_navigation_item">
            <?php echo $this->translate('Post Reply'); ?>
        </span>   
   </div>
</div>
<?php echo $this->form->render($this) ?>