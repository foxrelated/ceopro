<?php if ($this->canCreate): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'ynvideochannel_general',
        'action' => 'share-video',
        'parent_type' => 'group',
        'subject_id' => $this->group->group_id,
    ), '<i class="fa fa-plus-square"></i>' . $this->translate('Create New Video'), array(
        'class' => 'buttonlink'
    ));
    ?>
<?php endif; ?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <ul class="ynvideochannel_browse_video_items ynvideochannel_video_listing_items">
        <?php foreach ($this->paginator as $video):
            echo $this->partial('_videochannel_video_item.tpl', array('item' => $video, 'showAddto' => true));
        endforeach; ?>
    </ul>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues
    ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No videos found.'); ?>
        </span>
    </div>
<?php endif; ?>
<script type="text/javascript">
    ynvideochannelVideoOptions();
</script>